# api-test-01

тестовое задание

## Цель:
- Разработать REST API (CRUD) сервис.
- Создание элементов
- Обновление элементов
- Удаление элементов
- Получение информации о элементе
- Валидацию полей сущности
- Тестами покрывается и функционал и БД
- Использование token для доступа к данным
- История изменений сущности

## Вводные данные:

Сущность: Item

Поля сущности:

* id - int автоинкремент
* name - char(255)
* phone - char(15)
* key - char(25) not null
* created_at - datetime - дата создания элемента
* updated_at - datetime - дата обновления элемента

## Стек технологий: PHP8(без использования фреймворков)
