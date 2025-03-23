# Projet SPEEDVETO : API using Symfony for managing a veterinary clinic

## Features

This project is a Symfony API for managing a veterinary clinic. There are 4 types of users :
- Clinic's director (ROLE_DIRECTOR),
- Veterinarians (ROLE_VETERINARIAN),
- Assistants (ROLE_ASSISTANT),
- Clients (from table `clients`)

**ROLE_ASSISTANT:**
- create a new consultation (POST /consultations)
  - Creation date is automatically set to the current datetime
  - Consultation status can be set to "programmed", "in_progress" or "done"

- update a consultation (PATCH /consultations/{id}) if status is not "done"
- set consultation's payment (PATCH /consultations/{id} with parameter `priceClient`)
- see consultation details (GET /consultations/{id})
- create a new animal (POST /animals)

**ROLE_VETERINARIAN:**
- list all consultations of the day (GET /consultations) with parameter `consultationDate=today`
- see consultation details (GET /consultations/{id})
- attribute a consultation to himself (PATCH /consultations/{id} with parameter `veterinarian`)
- update consultation status (PATCH /consultations/{id} with parameter `status`)
- create treatment (POST /treatments)
- list all treatments (GET /treatments)
- see treatment details (GET /treatments/{id})
- update treatment (PATCH /treatments/{id})
- delete treatment (DELETE /treatments/{id})

**ROLE_DIRECTOR:**
- create a new user (POST /users) except for ROLE_DIRECTOR
- list all users (GET /users)
- see user details (GET /users/{id})
- update user (PATCH /users/{id})
- delete user (DELETE /users/{id})

**ROLE_ASSISTANT, ROLE_VETERINARIAN & ROLE_DIRECTOR:**
- list consultations' history (GET /consultations/history) with parameters `consultationDate[before]` and `consultationDate[after]`

## Postman

The postman collection is available in this github repository. You need to import the `SpeedVeto.postman_collection.json` file in your postman application.

## Utilisation

You need to clone this project in your own pc by using Github Desktop -> ADD -> Clone Repository -> URL and then copy and paste this link in the repository URL field

```bash
https://github.com/MarquesThomasCoding/speedveto.git
```
or use this command in your terminal

```bash
git clone https://github.com/MarquesThomasCoding/speedveto.git
```
Then you need to go to this project using 

```bash
cd speedveto
```
and you need to install all dependences using this 

```bash 
composer install
```
after that run you need to run by using this command

```bash
symfony server:start
```
or

```bash
symfony serve
```

Next, we need to create a migration (create your own database) using this :

```bash
php bin/console make:migration
```

and then the code will ask to run this code : 

```bash
php bin/console doctrine:migrations:migrate
```

In your PhpMyAdmin, in the database created by this project you need to create a director role directly into "User" table (since we cannot create a ROLE_DIRECTOR directly by using endpoint) and use this code :

```sql
INSERT INTO user (email, password, roles, firstname, lastname) 
VALUES ('admin@gmail.com', 
        '$2y$13$rPdc5j9HV38nIsdkkf6K9uOOQHBoUgLMBfk1wWxJHAcfH5uqXEjT', 
        '["ROLE_DIRECTOR"]', 
        'Test', 
        'Test');
```

The password for the director role will be "123" and the email is "admin@gmail.com". Feel free to change this code.

## Project members

- Thomas MARQUES
- Roland HUON
