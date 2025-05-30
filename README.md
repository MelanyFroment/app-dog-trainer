# Symfony + Mysql + Nginx

## K8s

### mysql-deployment
### Nginx-deployement
### Php-deployement
### mysql-sercice
### Nginx-service
### Nginx-configmap
### persistent-volume

## Commande kube
### kubectl apply -f mysql-deployment
### kubectl apply -f Nginx-deployment
### kubectl apply -f Php-deployment
### kubectl apply -f mysql-service
### kubectl apply -f nginx-service
### kubectl apply -f mysql-configmap
### kubectl apply -f persist-ent-volume



PostgreSQL + Docker - Commandes utiles

* Lancer PostgreSQL avec Docker
docker-compose up -d --build
* Vérifier les conteneurs en cours
docker ps
* Accéder à PostgreSQL depuis le conteneur
docker exec -it postgres_db psql -U melany -d symfony_pg
* Commandes dans le shell PostgreSQL
  -- Lister les tables
  \dt

-- Voir la structure d'une table
\d utilisateur

-- Lister les bases de données
\l

-- Lister les utilisateurs
\du

-- Quitter
\q

* Supprimer et recréer la base de données
# Arrêter les conteneurs
docker-compose down

# Supprimer les volumes (optionnel mais nettoie les données PostgreSQL)
docker volume rm $(docker volume ls -q --filter name=postgres)

# Relancer proprement
docker-compose up -d --build

* Autres commandes utiles
# Se connecter à PostgreSQL avec un client GUI (type DBeaver)
host: localhost
port: 5432
user: melany
password: Y8D!=LQZ
database: symfony_pg



* Symfony + PostgreSQL + Docker - Commandes utiles

* Démarrer le projet
docker-compose up -d --build
docker ps
* Accéder à un conteneur
docker exec -it php_app sh
* Vérifications Symfony (dans le conteneur php_app)
# Vérifier que Symfony répond bien
php bin/console
# Vérifier les entités Doctrine détectées
php bin/console doctrine:mapping:info
# Vérifier l'état des migrations
php bin/console doctrine:migrations:status
# Générer une migration
php bin/console make:migration
# Appliquer les migrations
php bin/console doctrine:migrations:migrate
# Valider le schéma Doctrine par rapport à la BDD
php bin/console doctrine:schema:validate
* Vider le cache Symfony
# Méthode manuelle
rm -rf var/cache/dev/*
# Commande Symfony
php bin/console cache:clear
# Réchauffer le cache (optionnel mais recommandé)
php bin/console cache:warmup
* Accéder à l'application
http://localhost:8080


* Tester les routes API


POST http://localhost:8080/api/register
Content-Type: application/json

{
"email": "test@example.com",
"password": "password123",
"phone": "0601020304"
}



POST http://localhost:8080/api/login
Content-Type: application/json

{
"email": "test@example.com",
"password": "password123"
}



GET http://localhost:8080/api/user/me
Authorization: Bearer <votre_token_jwt>
