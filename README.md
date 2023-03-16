# Karma8 Async demo

Cервис проверки почтовых адресов и отправки писем.

Асинхронная многопоточная реализация на основе [AMPHP](https://amphp.org/)

Два варианта запуска и использования.

## 1. Локальный

### Системные требования

* PHP 8.2
    * ext-ctype
    * ext-curl
    * ext-dom
    * ext-mbstring
    * ext-openssl
    * ext-pdo_mysql
    * ext-phar
    * ext-zend-opcache
* Composer
* MySQL/MariaDB

Проверка системный требований

```bash
composer check-platform-reqs --no-dev
```

### Установка

```bash
git clone https://github.com/karma8-demo/async.git
cd async
composer install --no-dev
```

Загрузить дамп БД с тестовыми данными в MySQL/MariaDB

Проверить/исправить строку подключения к БД в `app/app.php:14`

### Использование

Запустить микросервис с воркерами

```bash
php app/app.php
```

Для включения планировщика заменить все *EventLoop::delay* на *EventLoop::repeat* и указать необходимый интервал в `app/app.php:16`

Логи выводятся на экран.

## 2. Docker

### Системные требования

Docker Desktop (Windows, MacOS) или docker-cli и docker-compose (Linux)

### Установка

```bash
git clone https://github.com/karma8-demo/async.git
cd async
docker build --progress=plain --tag async .
```

### Использование

Запустить микросервис с воркерами

```bash
docker run --interactive --tty async
```

Логи выводятся на экран

## Ключевые моменты

* Быстрая, масштабируемая и эффективная конфигурация
* БД MySQL/MariaDB
* Поле emails.email, дублирующее users.email, заменено на внешний ключ user_id
* Добавлены индексы по всем полям для выборок
* Выбор адресов для проверки и рассылки производится в обработчиках *app/Handlers/EmailsPromote.php*, *app/Handlers/EmailsCheck.php* и *app/Handlers/EmailsSend.php*
* Выбранные адреса асинхронно обрабатываются в *app/Jobs/EmailCheck.php* и *app/Jobs/EmailSend.php*, в них же эмулируется случайная скорость проверки адресов и отправки сообщений. Для каждой задачи можно указать любое количество потоков (concurrency) для достижения необходимой производительности
* Проверенным адресом присваивается случайное значение valid=true|false
* Confirmed-адреса не проверяются: им сразу присваивается checked=true и valid=true (экономия стоимости проверки)
* Время отправки письма записывается в поле users.notifiedts, для исключения повторной отправки

## Возможные улучшения

* Перенос поля confirmed в таблицу emails
* Реализация блокировок для исключения дублей задач
* Получение данных из БД чанками по 1000-10000 строк
* Масштабирование возможно: по количеству потоков (concurrency), количеству экземпляров микросервиса, выносом задач в отдельные треды (threads)
* Возможно межпроцессное взаимодействие через каналы как в Golang
* Множественные адреса для одного пользователя
* Не спамить: слать письма только на confirmed адреса - в таком случае не нужны дополнительные проверки адресов
* Использование Postfix или другого MTA для ускорения обработки очереди отправки писем
* Использование transactional-писем c шаблонами вместо непосредственной отправки
