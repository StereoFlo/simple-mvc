# Simple MVC

Простой PHP фреймворк c автоматическим резолвом зависимостей в контроллерах

##### Использование DI

Откройте файл `config/container.php` добавьте строку

```php
$container->set(new \DateTime('now', \DateTimeZone::EUROPE));
```

Парамеры из методов котроллера резолвятся автоматом в `src/Application.php`

##### Роутинг

Откройте `config/routes.php`

и добавьте в возвращаемый массив новый инстанс `\Core\Router\Collection\Route` с памараметрами конструктора:

```php
\Core\Router\Collection\Route(
    (string) (Регулярное выражение из REQUEST_URI),
    (string) (Контроллер с указанием нэймспэйса),
    (string) (HTTP метод),
    (string) (метод в классе, который необходимо вызвать)
)
```