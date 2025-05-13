# Docker & Postgresql
1. `docker compose up --build -d` : lancer PostgreSQL avec Docker
2. `docker ps` : vérifier les conteneurs en cours 
3. `docker exec -it postgres_db psql -U melany -d symfony_pg` : accéder à PostgreSQL depuis le conteneur

# Commandes dans le shell PostgreSQL
- `\dt` : lister les tables
- `\d utilisateur` : voir la structure d'une table
- `\l` : lister les bases de données
- `\du` : lister les utilisateurs
- `\q` : quitter

# Supprimer et recréer la base de données
1. `docker compose down` : arrêter les conteneurs
2. `docker volume rm $(docker volume ls -q --filter name=postgres)` : supprimer les volumes (optionnel mais nettoie les données PostgreSQL)
3. `docker compose up --build -d` : relancer proprement

# Autres commandes utiles
## Se connecter à PostgreSQL avec un client GUI (type DBeaver)
- host: localhost
- port: 5432
- user: melany
- password: Y8D!=LQZ
- database: symfony_pg

# Symfony + PostgreSQL + Docker
- `docker compose up --build -d` : start a project
- `docker ps` : to list currently running containers
- `docker exec -it php_app sh` : open a shell inside a running container

# Vérifications Symfony (dans le conteneur php_app)
- `php bin/console` : vérifier que Symfony répond bien
- `php bin/console doctrine:mapping:info` : vérifier les entités Doctrine détectées
- `php bin/console doctrine:migrations:status` : vérifier l'état des migrations
- `php bin/console make:migration`: générer une migration
- `php bin/console doctrine:migrations:migrate` : appliquer les migrations
- `php bin/console doctrine:schema:validate` : valider le schéma Doctrine par rapport à la BDD
## Vider le cache Symfony
- `rm -rf var/cache/dev/*` : méthode manuelle
- `php bin/console cache:clear` : commande symfony
- `php bin/console cache:warmup` : réchauffer le cache (optionnel mais recommandé)

# Accéder à l'application
- http://localhost:8080

# Tester les routes API
- POST http://localhost:8080/api/register
- Content-Type: application/json
```
{
  "email": "test@example.com", 
  "password": "password123", 
  "phone": "0601020304"
}
```
- POST http://localhost:8080/api/login
- Content-Type: application/json
```
{
"email": "test@example.com",
"password": "password123"
}
```
- GET http://localhost:8080/api/user/me
- Authorization: Bearer <your_jwt_token>
