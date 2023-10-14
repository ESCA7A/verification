Модуль верификации

Что бы воспользоваться verification serivce'ом, нужно его сформировать.

Формирование service объекта состоит из нескольких этапов (подробнее в Service.php):
- создание __channel__ канала связи
- передача __verifyValue__ номера телефона, почты и т.д. по которому будет отправлен код подтверждения
- передача __message__ сообщения

## Создание запроса на верификацию
____
### `1 этап "Канал связи"`
Указать один из каналов связи (Подробнее о каналах связи в Channels):

```php
use Domain\Verification\VerificationService;
use Domain\Verification\Channels\SmsChannel;
...
$service = new VerificationService();
$service->channel(new SmsChannel()); // 
```

### `2 этап "Верифицируемое значение/адрес"`
Указать куда отправлять код подтверждения:
```php
...
$verify_value = '+71112223344'; $verify_value = 'test@testmail.com';
$service->verifyValue($verify_value); // 
```

### `3 этап "Сообщение для отправки с кодом подтверждения"`
Код формируется автоматически на этапе генерации сервиса как свойство. 
Поэтому у вас сразу есть к нему доступ
Кол-во символом или размерность кода, можно задать в `конфигурации`
Указать текст сообщения и передать в тело код:
```php
...
$message = __('вас приветствует meet-market. Код для активации: :code', [
    'code' => $service->code
]);
```

### _В бой вступает метод send()_
Когда у нас есть все необходимые ресурсы, время для отправки данных в таблицу кодов проходящих верификацию `verifications`
```php
$service->send(); // отсылает код по каналу связи и создает новую запись в `verifications`
```
____

## Верификация запроса
____
Для верификации запроса нужно проедать те же действия с небольшими отличиями
- метод `message($message)` нам уже не понадобиться

### `верификация кода подтверждения`
Что бы верифицировать пользователя, необходимо указать с какого канала связи он стучится и верен ли код который он присылает
Если проверка пройдет успешно, то статус записи сменится с `send` на `confirmed`, а метод `verify()` вернет __true__

```php
use Domain\Verification\VerificationService;
use Domain\Verification\Channels\SmsChannel;

$code = '1234';
$verify_value = '+71112223344'; $verify_value = 'test@testmail.com';

$service = new VerificationService();
$service->channel(new SmsChannel())->verifyValue($verify_value)->verify($code): bool;

```
____
## Создание собственного канала связи
Что бы создать свой канал связи, достаточно имплементировать ему `VerificationChannelContract` (Подробнее в Channels)
```php 
use Domain\Verification\Channels\VerificationChannelContract;

class MyNewChannel implements VerificationChannelContract {...}
```
Теперь когда вам нужно будет использовать сервис со своим каналом передачи кода подтверждения, достаточно будет написать

```php
use Domain\Verification\VerificationService;
use My\Namespace\Channels\MyNewChannel;
...
$service = new VerificationService();
$service->channel(new MyNewChannel())->verifyValue($verify_value)->message($some_message)->send();
```
____

## Подробнее о конфигурации

Сервис предоставляет конфиг с небольшим набором параметров (Подробнее в ./config/verification.php)
Давайте рассмотрим что они из себя представляют
```php
    "code_length" => 4,         // можно указать длину кода подтверждения
    "limit_send_count" => 3,    // число разрешенных запросов на отправку кода
    "next_send_after" => 30,    // кол-во секунд когда на отправку стоит ограничение в виде паузы
    "expire_seconds" => 240,    // кол-во секунд когда код считается действительным
    "timeout" => 600,           // кол-во секунд таймаута, это ограничение если кол-во попыток превысило ожидания 
    "flushCode" => true,        // будет ли удаляться/перезаписываться старая запись перед отправкой нового кода
```