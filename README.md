# Plus belle la semaine
Seriousgame pour smartphone de prévention à l'adaptation du logement

Ce jeu vise à déstigmatiser la démarche d’adaptation du domicile par les personnes âgées, en plaçant le joueur face à un enchainement de choix binaires pouvant survenir au cours de son quotidien.

Il a été développé dans le cadre du projet Bien Vivre Chez Soi à la Métropole, un projet de prévention porté par la Métropole de Lyon, dans le cadre du plan d’action 2017 de la Conférence des financeurs de la prévention de la perte d’autonomie.

## Installation environnement de dev windows
Prérequis :
- Wampserver 3.1.0+
- Mysql 5.6+
- Php 5.6.3+
- Ruby 2.5+

### Lancement de l'application
L'application est servie sous wampserver. Pour cela il faut se placer dans le répertoire ``www`` du dossier d'installation de votre wamp (par défaut ``C:\wamp64\www``).

```
cd C:\wamp64\www
git pull https://github.com/urbanlab/plusbellelasemaine
```

### Configuration
Dupliquer le fichier ```html/application/config/database.sample.php``` en ```database.php```

Renseigner les identifiants de connexion à la base de donnée du projet lignes 78 à 81.

### Installation BDD
Créer une base de donner 'bienvieillir'. Importer un dump de la base de production pour initialiser la BDD.
Note: penser a récupérer la clé d'encryption (encryption_key) dans le fichier config.php du serveur.
Créer un fichier database.php dans le dossier ''html/application/config/'' sur l'exemple de celui présent (database.sample.php). Renseigner les informations de connexion (bdd, user, mdp ...)

### Installation du framework css
Le framework css utilisé dans le cadre du projet est compass
```
gem update --system
gem install compass
```
Pour lancer la compilation automatique des fichers sass utiliser la commande suivante dans le dossier ``html/app``
```
compass watch
```
Toutes modification des fichiers sass entrainera une compilation et l'actualisation des fichiers css

## Installation serveur
Prérequis:
- OS Linux
- Mysql 5.6+
- Php 5.6.3+

### Procédure de livraison
Avant de procéder a la livraison, penser a sauvegarder:
- la BDD
- le dossier ``html/app/data``
- le dossier ``html/application/config``
  
```bash
# BDD
mysqldump -u root -p bienvieillir > ~/plusbellelasemaine.erasme.org/bdd_save

# App data
cp -r ~/plusbellelasemaine.erasme.org/www/html/app/data ~/plusbellelasemaine.erasme.org/tmp/

# App config
cp -r ~/plusbellelasemaine.erasme.org/www/html/application/config ~/plusbellelasemaine.erasme.org/tmp/
```

1) Récuperer les sources sur le serveur de production

```
cd ~/plusbellelasemaine.erasme.org/www/html/app
git pull
```

ATTENTION, le vhost du projet doit pointer sur le répertoire "html" du repository, le dossier "CI_system" du repository ne doit pas être accessible depuis un navigateur pour des raisons de sécurité, si il le faut il peut être remonté dans l'arborescence de dossiers, à ce moment là il faut ajuster son chemin d'accès dans le fichier "html/index.php", ligne 113

2) Dans le dossier ``html/``, passer les permissions du dossier "uploads" pour que PHP puisse lire et écrire dedans (ou vérifier qu'elle sont toujours bonnes).
```
cd ****
chmod 775
```

3) Vérifier que les dossier ``html/application/config`` et ``html/app/data`` sont inchangés. Si ce n'est pas le cas restaurer avec la sauvegarde.

Note: le dossier config contient la configuration globale du projet (BDD, googleAnalytics, mail...). Le dossier data contient toutes les images et scénarios uploader.

### Gestion du cache
Le cache est actualisé automatiquement lorsqu'une modification est faite au fichier ``manifest.appcache``. Voir le lien suivant: [Cache doc](https://www.html5rocks.com/en/tutorials/appcache/beginner/)


## Docker
Copier le fichier `.env.example` en `.env` et renseigner les variables d'environnement.

S'il s'agit de la première installation de plus belle la semaine assurez vous d'avoir copié votre dump sql dans le dossier db_dump.

Lancer le serveur avec la commande `docker-compose up`.

Vous pouvez accéder à **l'application** sur le port 8080.
Vous pouvez accéder à **phpmyadmin** sur le port 8081.
Vous pouvez accéder à **maildev** sur le port 8082 (pour voir les mails envoyés par l'application dans un environement de dev).
