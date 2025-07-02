# 💻 PROJET DEV WEB


Ce projet universitaire est une application web permettant de consulter ses mails, d'en ajouter, modifier ou supprimer
### 👤 Équipe
- SOLANKI Priyank
- FARIA Lucas
- LUAN Nam
- SYLLA El Hadj

## 📁 Structure

- **front-end** : Dossier contenant la partie front de l'application
- **back-end** : Dossier contenant la partie back de l'application
## 🔧 Prérequis

- PHP >= 8.1
- Composer
- Symfony CLI → https://symfony.com/download
- MySQL ou PostgreSQL
- Node.js >= 16

## ⚙️ Configuration de l'environnement
1. Back-end
- ```cd back-end```
- ```cp .env.dist .env```
- ```composer install```
- ```cd ..````
2. Front-end
- ```cd front-end```
- ```cp .env.dist .env```
- ```npm install```
- ```cd ..```


## 📊 Configuration de la base de données
1. Changer l'url du fichier .env du dossier back-end
- ```DATABASE_URL="mettre votre url"```
2. Taper les commandes suivantes dans l'ordre
- ```cd back-end```
- ```php bin/console doctrine:database:create```
- ```php bin/console make:migration```
- ```php bin/console doctrine:migrations:migrate```

## ✅ Lancer le projet
1. Back-end
- ```cd back-end```
- ```symfony server:start```
- ```cd ..```
2. Front-end
- ```cd front-end```
- ```npm run serve```

## 📄 Documentation de l'API
- Lancer le back-end
-  http://localhost:8000/api/doc
