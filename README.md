# Robokassa_PHP_Class

Простой класс для работы с API Robokassa (https://robokassa.com).

## Как использовать
### 1. Подключаем класс

```php
<?php

require_once 'Robokassa.php';
```

### 2. Создаем массив с данными магазина

```php
<?php

$RK = [
    'name' => 'Name_Shop',                  // Имя магазина
    'pass1' => '********************',      // Пароль 1
    'pass2' => '********************',      // Пароль 2
    'test' => false,                        // Тестовые данные или нет (true / false)
];
```

### 3. Перенаправление пользователя на страницу оплаты

```php
<?php

$Robokassa = new Robokassa($RK);

/**
 * Переадресация на страницу оплаты.
 * @param int $order_id - ID заказа (InvId)
 * @param float $sum - Сумаа заказа (OutSum)
 * @param string $desc - Описание заказа (InvDesc)
 * @param array|null $params - Кастомные параметры
 * @param string|null $pay_method - Способ оплаты (IncCurrLabel)
 * @param string|null $lang - Язык (Culture)
 */
$Robokassa->redirect($order_id, $sum, $desc, $params, $pay_method, $lang);
```

### 4. Проверка параметров после нажатия на кнопку "Вернуться в магазин" (URL Success в настройках магазина Robokassa)

```php
<?php

$params = array();
if (!empty($_REQUEST))
{
    foreach ($_REQUEST as $key => $value)
    {
        if (strpos($key, 'shp_') !== false)
        {
            $x          = str_replace('shp_', '', $key);
            $params[$x] = $value;
        }
    }
}

$Robokassa = new Robokassa($RK);

/**
 * Проверка ответа от Robokassa.
 * @param string $request - Какой ответ проверяем (result, success)
 * @param array $params - Кастомные параметры
 * @return bool
 */
$check = $Robokassa->check_response('success', $params);
if ($check == true)
{
    // Success code
}
else
{
    // Failure code
}
```

### 5. Проверка параметров результата (URL Result в настройках магазина Robokassa). Запрос отправляет сама Robokassa.

```php
<?php

$params = array();
if (!empty($_REQUEST))
{
    foreach ($_REQUEST as $key => $value)
    {
        if (strpos($key, 'shp_') !== false)
        {
            $x          = str_replace('shp_', '', $key);
            $params[$x] = $value;
        }
    }
}

$Robokassa = new Robokassa($RK);

/**
 * Проверка ответа от Robokassa.
 * @param string $request - Какой ответ проверяем (result, success)
 * @param array $params - Кастомные параметры
 * @return bool
 */
$check = $Robokassa->check_response('result', $params);
if ($check == true)
{
    // Success code
}
else
{
    // Failure code
}
```