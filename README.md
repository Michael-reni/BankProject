  Open terminal. Every command that needs to be executed in terminal starts with number and a dot
1. cd /path/to/project/
 Laravel project lies in laravel_project/laravel. I recommend to open it in some IDE like Visual Studio Code right now  
 Now we need to start a docker project  
2. docker-compose up -d
  Good, let's get inside the container and instal the project dependencies, create .env and run migrations to database 
4. docker exec -it nginx_container bash
  Now we are inside the container
4. cd app/laravel/
5. composer install
   Before we run the migrations, we need to create new .env inside laravel project. In the project there is a folder called ENV, open it, inside there is a .env file.      Copy its content, create new .env in IDE and paste it to that new created .env file inside laravel project. Now back to the terminal inside the docker container  
6.  php artisan migrate --path=database/migrations/banking_system
7. php artisan route:cache
8. php artisan config:cache
9. php artisan l5-swagger:generate
   Now you can interact with documentation written in Swagger. Just copy/paste this link to your browser: http://127.0.0.1:8080/api/documentation
   If you want to start the test 
10.  php artisan test --testsuite=Feature
