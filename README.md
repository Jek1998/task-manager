# Task Manager (Laravel + jQuery + Bootstrap)

Простой менеджер задач с возможностью добавления, удаления, фильтрации, редактирования и отметки выполненных задач.

## Технологии
- Laravel 12
- Laravel Breeze (Blade)
- MySQL (через XAMPP)
- jQuery + AJAX
- Bootstrap 5

## Установка
git clone https://github.com/IhZhur/task-manager.git
cd task-manager
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run build
php artisan serve

## Возможности
CRUD задач
Фильтрация (все, активные, завершённые)
Удаление завершённых задач
Визуальное редактирование задач
Поддержка авторизации (Laravel Breeze)

## Автор
IhZhur