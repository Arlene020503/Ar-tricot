# Ar Tricot - Site dynamique

Ce dépôt contient le site web d'Ar Tricot avec une fonctionnalité dynamique d'avis clients.
Le front-end est statique (HTML/CSS/JS) tandis qu'un petit serveur Node/Express gère la collecte et l'affichage des avis.

## Prérequis

- Node.js 14+ installé sur votre machine.

## Installation

```bash
# se placer dans le dossier du projet
cd "c:\Users\HP\Desktop\GomyCode\Ar tricot"

# installer les dépendances
npm install
```

## Lancer le serveur

```bash
npm start
```

Le site sera accessible sur `http://localhost:3000`. Tous les fichiers statiques (index.html, style.css, images, etc.) sont servis par Express.

## Fonctionnalités dynamiques

- Les visiteurs peuvent soumettre un avis via le formulaire dans la section "Avis clients".
- Les avis sont enregistrés dans `reviews.json`.
- Les avis sont partagés entre tous les visiteurs du site (s'affichent sur chaque appareil). 
- Les utilisateurs peuvent supprimer un avis à l'aide du bouton ✕ qui apparaît sur leur propre avis.

## Déploiement

Pour mettre le site en ligne, déployez `server.js` sur une plateforme Node (Heroku, Vercel, Azure Web App, etc.) et assurez-vous d'initialiser un `reviews.json` vide ou d'utiliser une base de données.

## Améliorations possibles

- Se connecter à une vraie base de données (MongoDB, PostgreSQL, etc.).
- Ajouter une authentification pour que chaque personne ne puisse modifier que ses propres avis.
- Gérer les images des produits de manière dynamique.
- Internationalisation, sécurité (sanitization des entrées, rate limiting, etc.).
