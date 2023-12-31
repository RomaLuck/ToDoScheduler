## Table of contents
* [General info](#general-info)
* [Tools](#tools)
* [Current features](#current-features)
    - [Current works](#current-works)
    - [ToDo](#todo)
* [Setup](#setup)

## General info
The application is for creating and managing your plans. You will receive reminders about your tasks and follow them using the built-in calendar. For greater convenience, you can manage tasks in Telegram

Currently, it is under development.
## Tools
- PHP v8.2
- Symfony v6.3
- Twig v3
- Doctrine v2.15
- Knpuniversity/oauth2-client-bundle v2.16
- League/oauth2-facebook v2.2
- League/oauth2-google v4.0
- Nutgram/symfony-bundle v1.0
- Symfony/monolog-bundle v3.8
- Symfony/translation v6.3
- Bootstrap v5.3

## Current features
- Custom authentication system
- Custom authorization system
- Authorization through Google
- Authorization through Facebook
- Custom reset password feature
- Sending email after registration
- Task management in web/telegram
- Sending reminders
- Week, month calendar
- Translation ukrainian/english languages

### Current works
- Tests

### ToDo
- Synchronization with google-calendar

## Setup
Copy the .env.dist file and edit the entries to your needs:
```
cp .env.dist .env
```
Enter app folder and install Packages
```
composer install
```
Copy in app folder the .env file and edit the entries to your needs:
```
cp .env .env.local
```
Start docker-compose to start your environment:
```
docker-compose up
```
Make migration and migrate
```
docker-compose exec php bin/console make:migration
```
```
docker-compose exec php bin/console doctrine:migration:migrate
```
That is all. Use application
