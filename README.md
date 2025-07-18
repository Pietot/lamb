# fashion-chic

Ce modèle vous aidera à démarrer le développement avec Vue 3 et Vite.

## Configuration recommandée de l'IDE

[VSCode](https://code.visualstudio.com/) + [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar) (et désactivez Vetur).

## Personnalisation de la configuration

Voir la [documentation de configuration de Vite](https://vite.dev/config/).

## Prérequis

- Cloner le dépôt dans `C:\wamp64\www` si vous utilisez WAMP, ou dans `C:\xampp\htdocs` si vous utilisez XAMPP.
- Importer la base de données depuis `api\routes\utils\sql\lamb.sql` dans votre serveur de base de données.
- Compléter les fichiers `.env` dans `api\routes\utils\.env` et `.env.local`.

## Installation du projet

```sh
npm install
```

### Compilation et rechargement à chaud pour le développement

```sh
npm run dev
```

### Compilation et minification pour la production

```sh
npm run build
```

### Pour mettre à jour les .env
```sh
npm run setup-env
```

### Pour réinitialiser .env
```sh
npm install
```

### Pour installer vite si besoin
```sh
npm vite install
```

### Pour formatter le code (facultatif)
```sh
npm run pretty
```
