# Инструкция по запуску проекта

## Требования
- Docker
- Docker Compose
- Поддержка Makefile


### 1. Клонирование проекта
```bash
git clone https://github.com/comtad/mkkluna-test
cd repo
```

## Запуск контейнера
```bash
make build
make up
```

## Подключение к контейнеру
```bash
make shell
```

## Установка зависимостей и подготовка окружения
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

## Доступ к приложению
- Приложение: [http://localhost/](http://localhost/)
- Документация по API: [http://localhost/api/documentation](http://localhost/api/documentation)
- Laravel Telescope: [http://localhost/telescope/requests](http://localhost/telescope/requests)

---

# API-эндпоинты

## Список всех организаций, находящихся в конкретном здании
[http://localhost/api/buildings/organizations?api_key=123&building_id=2](http://localhost/api/buildings/organizations?api_key=123&building_id=2)

## Список всех организаций, относящихся к указанному виду деятельности
[http://localhost/api/activities/organizations?api_key=123&activity_name=Рестораны](http://localhost/api/activities/organizations?api_key=123&activity_name=Рестораны)

## Список организаций в заданном радиусе относительно точки
[http://localhost/api/organizations/nearby?api_key=123&lat=55.7069&lng=37.5422026&radius=1000](http://localhost/api/organizations/nearby?api_key=123&lat=55.7069&lng=37.5422026&radius=1000)

## Список организаций в прямоугольной области (по координатам)
[http://localhost/api/organizations/in-rectangle?api_key=123&sw_lat=55.7&sw_lng=37.5&ne_lat=55.8&ne_lng=37.6](http://localhost/api/organizations/in-rectangle?api_key=123&sw_lat=55.7&sw_lng=37.5&ne_lat=55.8&ne_lng=37.6)

## Информация об организации по идентификатору
[http://localhost/api/organizations/by-id?id=3022&api_key=123](http://localhost/api/organizations/by-id?id=3022&api_key=123)

## Поиск по виду деятельности с учётом вложенных категорий
[http://localhost/api/organizations/activity-tree?api_key=123&activity_name=рест](http://localhost/api/organizations/activity-tree?api_key=123&activity_name=рест)

## Поиск организации по названию
[http://localhost/api/organizations/by-name?api_key=123&name=ЗАО](http://localhost/api/organizations/by-name?api_key=123&name=ЗАО)
