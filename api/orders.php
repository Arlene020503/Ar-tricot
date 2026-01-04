<?php
// api/orders.php
// Reçoit une commande en POST (JSON) et l'enregistre dans la table `orders`.

header('Content-Type: application/json; charset=utf-8');
// Autoriser CORS basique pour tests locaux
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['error' => 'Aucune donnée reçue']);
    http_response_code(400);
    exit;
}

// Validation minimale
$required = ['product_code','product_name','customer_name','contact','contact_type','size','color','delivery_address','quantity','payment_method'];
foreach ($required as $f) {
    if (!isset($input[$f]) || $input[$f] === '') {
        echo json_encode(['error' => "Champ manquant: $f"]);
        http_response_code(400);
        exit;
    }
}

// Validation du contact selon le type
$contact = trim($input['contact']);
$contact_type = strtolower(trim($input['contact_type'] ?? 'phone'));
if ($contact_type === 'phone') {
    // garder seulement les chiffres pour compter
    $digits = preg_replace('/[^0-9]/', '', $contact);
    if (strlen($digits) < 6) {
        http_response_code(400);
        echo json_encode(['error' => 'Numéro de téléphone invalide']);
        exit;
    }
} elseif ($contact_type === 'email') {
    if (!filter_var($contact, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Adresse email invalide']);
        exit;
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Type de contact invalide']);
    exit;
}

// Connexion DB via config
$pdo = require __DIR__ . '/config.php';

try {
    $stmt = $pdo->prepare('INSERT INTO `orders` (product_code, product_name, customer_name, contact, contact_type, size, color, delivery_address, quantity, payment_method, note) VALUES (:product_code,:product_name,:customer_name,:contact,:contact_type,:size,:color,:delivery_address,:quantity,:payment_method,:note)');
    $stmt->execute([
        ':product_code' => substr($input['product_code'],0,120),
        ':product_name' => substr($input['product_name'],0,255),
        ':customer_name' => substr($input['customer_name'],0,255),
        ':contact' => substr($input['contact'],0,255),
        ':contact_type' => substr($input['contact_type'],0,20),
        ':size' => substr($input['size'],0,20),
        ':color' => substr($input['color'],0,80),
        ':delivery_address' => $input['delivery_address'],
        ':quantity' => (int)$input['quantity'],
        ':payment_method' => substr($input['payment_method'],0,80),
        ':note' => isset($input['note']) ? $input['note'] : null,
    ]);

    $id = $pdo->lastInsertId();
    echo json_encode(['success' => true, 'order_id' => $id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur', 'detail' => $e->getMessage()]);
}
