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

`composer require drIceman/comments-client:dev-master`

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
