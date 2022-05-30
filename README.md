# Maker-Checker Admin
An administrative system that makes use of maker-checker rules for creating, updating and deleting user data

## Getting Started
These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Install
Clone the git repository on your computer

```$ git clone https://github.com/haybea/maker-checker.git```


You can also download the entire repository as a zip file and unpack in on your computer if you do not have git

After cloning the application, you need to install it's dependencies. 

```
$ cd maker-checker
$ composer install
$ copy .env.example .env
```


### Setup
- Generate the application key

  ```$ php artisan key:generate```


- Create database ```maker_checker```

- Run migrations for the db

  ```$ php artisan migrate```

### Run the application

  ```$ php artisan serve```
  
  -Run to listen for email jobs
  ```$ php artisan queue:listen```

### To test
- Create database ```maker_checker_test```

- Run the following 

```$ php artisan migrate --env=testing```


```$ php artisan test --env=testing```
  
## Useful links

Postman collection: https://www.getpostman.com/collections/e18c3f5f338d7f9f37f5

API documentation: https://documenter.getpostman.com/view/10025583/UyxjF64g


## Built With
* [Laravel](https://laravel.com) - The PHP framework for building the API endpoints needed for the application
