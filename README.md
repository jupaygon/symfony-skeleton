# 🩻 Symfony 7.1 skeleton

Basic installation of Symfony 7.1

### 🎯 Hexagonal architecture 

The structure of directories is ready for a **hexagonal architecture**, instead the default one of Symfony.

```
- Application
- Domain
    - Entity
- Infrastructure
    - Database
        - Repository
    - Http
        - Controller
```

To create controllers and entities using the make command, include the path of new file instead of use only the class's name, in order to create it in the custom folder.

```
php bin/console make:entity '\App\Domain\Entity\Demo'
php bin/console make:controller '\App\Infrastructure\Http\Controller\Demo'
```

🚨 When you create a new entity, the corresponding repository is created in the default directory of Symfony (instead of the Infrastructure/Database/Repository directory). You have to move the repository manually to the Infrastructure/Database/Repository directory.

### 📋 Pre requirements

- 🐘 PHP 8.2 (or higher)
- 📦 Composer
- 🐬 MySQL/MariaDB

### 💻 Clone the repository in your **projects' folder**:

```
git clone git@github.com:jupaygon/symfony-skeleton.git myproject
```

If you want to make your own repository starting from this one, follow the next steps:

- Make your new repository in GitHub.
- Clone original one to your new repo with the following commands (change "myproject" by the name of your new repository):

```
git clone --bare git@github.com:jupaygon/symfony-skeleton.git
cd symfony-skeleton.git
git push --mirror git@github.com:your_github_username/myproject.git
cd ..
rm -rf symfony-skeleton.git
```

- Now you new repository "myproyect" is a exact copy of "jupaygon/symfony-skeleton". You can download it executing the following command in your projects' folder:

```
git clone git@github.com:your_github_username/myproject.git
```

Add a volume for your project in the docker-compose.yml file:

```
volumes:
  - ./yourdomain:/var/www/html/yourdomain
```

Make your .env.local file:
```
$ touch .env.local
```

Put your database connection url in your .env.local file:
```
DATABASE_URL="mysql://root:password@server_mariadb:3306/your_db_name?serverVersion=mariadb-10.10.2&charset=utf8mb4"
```

Add your domain in your hosts file:
```
127.0.0.1 yourdomain.local
```

Add your domain in a nginx configuration file of your docker:
```
server {
    listen 80;
    server_name yourdomain.local;

    root /var/www/html/yourdomain/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass php:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    error_log  /var/log/nginx/error_yourdomain.log;
    access_log /var/log/nginx/access_yourdomain.log;
}
```

Stop the container:

```
$ docker-compose down
```

Start the container:

```
$ docker-compose up -d
```

### 🐳 In the docker **container**

Install dependencies in the project folder:
```
$ composer install
```

Create your database:
```
$ php bin/console doctrine:database:create
$ php bin/console doctrine:schema:create
```

### 🍾 Voilà! 🛫 🎉
Put the url of your project in your browser, taking into account the port you have configured in your docker-compose.yml file.

You will see the welcome page of Symfony, **congratulations**! 🎉 🎊
```
http://yourdomain.local:81/
```
