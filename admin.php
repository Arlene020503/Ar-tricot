<?php
session_start();
require '../config/db.php';

// Vérification de session admin
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $description = htmlspecialchars($_POST['description']);
    $prix = intval($_POST['prix']); // Uniquement des entiers pour le CFA

    // Gestion de l'upload d'image
    $target_dir = "../uploads/creations/";
    $image_name = time() . '_' . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $stmt = $pdo->prepare("INSERT INTO articles (nom, description, prix_cfa, image_url) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $description, $prix, $image_name]);
        echo "L'article a été ajouté avec succès.";
    }
}
?>