<?php
// Simple page d'administration pour lister les commandes
// IMPORTANT: protégez ce fichier en production (auth, mot de passe, IP restriction).

$pdo = require __DIR__ . '/../api/config.php';

try {
    $stmt = $pdo->query('SELECT * FROM `orders` ORDER BY created_at DESC');
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $orders = [];
    $error = $e->getMessage();
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin — Commandes Ar Tricot</title>
  <style>
    body{font-family:Arial,Helvetica,sans-serif;margin:20px;background:#f7f7f7;color:#222}
    .wrap{max-width:1200px;margin:0 auto;background:#fff;padding:18px;border-radius:8px;box-shadow:0 6px 24px rgba(0,0,0,0.06)}
    table{width:100%;border-collapse:collapse;font-size:14px}
    th,td{padding:8px 10px;border-bottom:1px solid #eee;text-align:left}
    th{background:#faf7f3;color:#333;position:sticky;top:0}
    tr:nth-child(even){background:#fcfcfc}
    .small{font-size:12px;color:#666}
    .actions{display:flex;gap:8px}
    .pill{display:inline-block;padding:6px 10px;border-radius:999px;background:#eef6ff;color:#024c7a;font-weight:600}
    .status{font-weight:700}
    .header{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
    .logo{height:48px}
    .note{margin-top:12px;color:#666}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <div>
        <h2>Commandes</h2>
        <div class="small">Liste des commandes récentes</div>
      </div>
      <div><img src="../image/logo.jpg" alt="logo" class="logo"></div>
    </div>

    <?php if(!empty($error)): ?>
      <div style="color:#b00020">Erreur: <?= h($error) ?></div>
    <?php endif; ?>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Produit</th>
          <th>Client</th>
          <th>Contact</th>
          <th>Taille / Couleur</th>
            <th>Quantité</th>
            <th>Paiement</th>
            <th>Contact type</th>
          <th>Statut</th>
          <th>Créée le</th>
          <th>Adresse / Remarque</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($orders)): ?>
          <tr><td colspan="10" class="small">Aucune commande trouvée.</td></tr>
        <?php else: foreach($orders as $o): ?>
          <tr>
            <td><?= h($o['id']) ?></td>
            <td>
              <div style="font-weight:700"><?= h($o['product_name'] ?: $o['product_code']) ?></div>
              <div class="small">code: <?= h($o['product_code']) ?></div>
            </td>
            <td><?= h($o['customer_name']) ?></td>
            <td><?= h($o['contact']) ?></td>
            <td><?= h($o['size']) ?> / <?= h($o['color']) ?></td>
            <td><?= (int)$o['quantity'] ?></td>
            <td><?= h($o['payment_method']) ?></td>
            <td><?= h($o['contact_type']) ?></td>
            <td class="status"><?= h($o['status']) ?></td>
            <td class="small"><?= h($o['created_at']) ?></td>
            <td>
              <div class="small"><?= h($o['delivery_address']) ?></div>
              <?php if(!empty($o['note'])): ?><div class="small">Remarque: <?= h($o['note']) ?></div><?php endif; ?>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>

    <div class="note">Conseil: protégez cette page par mot de passe en production. Pour filtrer/exporter, je peux ajouter des outils supplémentaires.</div>
  </div>
</body>
</html>
