## Реализация абстрактного клиента для вымышленного сервиса комментариев

### Подключение к проекту

Добавить ссылку на репозиторий проекта в секцию `repositories` вашего `composer.json`

```
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/drIceman/comments-client"
    }
]
```

Установить пакет

`composer require example/comments-client:dev-master`

Если требуется, подключить файл автолоада

`require __DIR__ . '/vendor/autoload.php';`

После чего его можно использовать через

```
<?php

declare(strict_types=1);

use Example\CommentsClient\Settings;
use Example\CommentsClient\Client;

$client = new Client(new Settings('https://some-comments-server.dummy/api/v1/'));
$comments = $client->getComments();
...
```

### Тестирование пакета

#### Установка зависимостей
`composer install`

#### Тестирование без генерациеи покрытия кода
`composer phpunit`

#### Тестирование с генерацией покрытия кода
`composer phpunit-cover`
