<?php
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article_id = intval($_POST['article_id']);
    $nom = htmlspecialchars($_POST['client_nom']);
    $contact = htmlspecialchars($_POST['client_contact']);

    if (!empty($nom) && !empty($contact)) {
        $stmt = $pdo->prepare("INSERT INTO commandes (article_id, client_nom, client_contact) VALUES (?, ?, ?)");
        $stmt->execute([$article_id, $nom, $contact]);
        // Redirection vers une page de confirmation ou retour front
        header('Location: ../index.php?success=commande');
    }
}
?>