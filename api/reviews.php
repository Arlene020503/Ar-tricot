<?php
// api/reviews.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$pdo = require __DIR__ . '/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

// GET: list reviews
if ($method === 'GET') {
    $stmt = $pdo->query('SELECT id, name, message, contact, contact_type, rating, rating_count, rating_sum, created_at FROM reviews ORDER BY created_at DESC');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // compute average
    foreach ($rows as &$r) {
        $r['avg_rating'] = ($r['rating_count'] > 0) ? round($r['rating_sum'] / $r['rating_count'],2) : 0;
    }
    echo json_encode($rows);
    exit;
}

// POST: create or actions
if ($method === 'POST') {
    $action = $input['action'] ?? 'create';

    if ($action === 'create') {
        // required fields: name, contact, message, rating(optional), contact_type
        if (empty($input['name']) || empty($input['message'])) {
            http_response_code(400); echo json_encode(['error'=>'name and message required']); exit;
        }
        $name = substr($input['name'],0,255);
        $message = $input['message'];
        $contact = isset($input['contact']) ? substr($input['contact'],0,255) : null;
        $contact_type = isset($input['contact_type']) ? substr($input['contact_type'],0,20) : 'phone';
        $rating = isset($input['rating']) ? (int)$input['rating'] : 0;
        $token = bin2hex(random_bytes(16));

        $stmt = $pdo->prepare('INSERT INTO reviews (name, contact, contact_type, message, rating, rating_count, rating_sum, edit_token) VALUES (:name,:contact,:contact_type,:message,:rating,:rating_count,:rating_sum,:token)');
        $stmt->execute([
            ':name'=>$name, ':contact'=>$contact, ':contact_type'=>$contact_type, ':message'=>$message,
            ':rating'=>$rating, ':rating_count'=>($rating>0?1:0), ':rating_sum'=>($rating>0?$rating:0), ':token'=>$token
        ]);
        $id = $pdo->lastInsertId();
        echo json_encode(['success'=>true,'id'=>$id,'edit_token'=>$token]);
        exit;
    }

    if ($action === 'update') {
        // expects id, edit_token, message (and optional name, rating)
        if (empty($input['id']) || empty($input['edit_token']) || !isset($input['message'])) { http_response_code(400); echo json_encode(['error'=>'missing']); exit; }
        $id = (int)$input['id'];
        $token = $input['edit_token'];
        // verify token
        $stmt = $pdo->prepare('SELECT edit_token FROM reviews WHERE id = :id'); $stmt->execute([':id'=>$id]); $row = $stmt->fetch();
        if (!$row || $row['edit_token'] !== $token) { http_response_code(403); echo json_encode(['error'=>'forbidden']); exit; }
        $message = $input['message'];
        $name = isset($input['name']) ? substr($input['name'],0,255) : null;
        $params = [':id'=>$id, ':message'=>$message];
        $sql = 'UPDATE reviews SET message = :message';
        if ($name !== null) { $sql .= ', name = :name'; $params[':name']=$name; }
        $sql .= ' WHERE id = :id';
        $pdo->prepare($sql)->execute($params);
        echo json_encode(['success'=>true]); exit;
    }

    if ($action === 'rate') {
        if (empty($input['id']) || !isset($input['stars'])) { http_response_code(400); echo json_encode(['error'=>'missing']); exit; }
        $id = (int)$input['id']; $stars = (int)$input['stars']; if($stars<0) $stars=0; if($stars>5) $stars=5;
        // update sum and count
        $pdo->prepare('UPDATE reviews SET rating_sum = rating_sum + :s, rating_count = rating_count + 1 WHERE id = :id')->execute([':s'=>$stars,':id'=>$id]);
        $stmt = $pdo->prepare('SELECT rating_sum, rating_count FROM reviews WHERE id = :id'); $stmt->execute([':id'=>$id]); $r=$stmt->fetch();
        $avg = ($r && $r['rating_count']>0) ? round($r['rating_sum']/$r['rating_count'],2) : 0;
        echo json_encode(['success'=>true,'avg'=>$avg,'count'=>$r['rating_count']]); exit;
    }

    http_response_code(400); echo json_encode(['error'=>'unknown action']); exit;
}

http_response_code(405);
echo json_encode(['error'=>'method not allowed']);
