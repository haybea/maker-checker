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
```


### Setup
- When you are done with installation, copy the `.env.example` file to `.env`

  ```$ cp .env.example .env```


- Generate the application key

  ```$ php artisan key:generate```


- Run migrations for the db

  ```$ php artisan migrate```

### Run the application

  ```$ php artisan serve```
  ```$ php artisan queue:listen```


## Built With
* [Laravel](https://laravel.com) - The PHP framework for building the API endpoints needed for the application
