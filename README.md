# Symfony OCR Image App

This is a simple web app where users can upload images, extract text using OCR, and search by keywords.

---

## Features / Возможности

- User registration / Регистрация пользователей  
- Image upload (public/private) / Загрузка изображений (публичные/приватные)  
- OCR text recognition / Распознавание текста (OCR)  
- Keyword search / Поиск по ключевым словам  

---

## How to Run / Как запустить

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony server:start
