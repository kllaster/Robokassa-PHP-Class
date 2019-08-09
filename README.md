# Robokassa_PHP_Class

1. Подключаем класс

```
<?php

require_once 'Robokassa.class.php';

```

2. Создать массивы с данными магазина

```
<?php

$RK = [
    'name' => 'Name_Shop',                  // Имя магазина
    'pass1' => '********************',      // Пароль 1
    'pass2' => '********************',      // Пароль 2
    'test' => false,                        // Тестовые данные или нет (true / false)
    'logs' => true                          // Вести лог действий или нет (true / false)
];

$RK_test = [
    'name' => 'Name_Shop',                  
    'pass1' => '********************',      
    'pass2' => '********************',      
    'test' => true,                         
    'logs' => true                          
];

```

3. Редирект на страницу оплаты

```
<?php

$Robokassa = new Robokassa($RK);

// $price       - Сумаа заказа
// $invid       - Номер чека
// $pay_metod   - Способ оплаты
// $info_desk   - Описание заказа
// $params      - Пользовательсякие параметры
// $lang        - Язык

$Robokassa->Redirect($price, $invid, $pay_metod, $info_desk, $params, $lang);

```

4. Проверка параметров после нажатия на кнопку "Вернуться в магазин" (URL Success в настройках магазина RK)

```
<?php

if(!empty($_REQUEST["OutSum"]) && !empty($_REQUEST["InvId"]) && !empty($_REQUEST["SignatureValue"])){

    $params = array();
    if (!empty($_REQUEST)) {
        foreach ($_REQUEST as $key => $value) {
            if (strpos($key, 'shp_') !== false) {
                $x          = str_replace('shp_', '', $key);
                $params[$x] = $value;
                $log_params .= $x . ' - ' . $value . ' ; ';
            }
        }
    }

    $Robokassa = new Robokassa($RK);

    $check = $Robokassa->Success($_REQUEST["OutSum"] . 0000, $_REQUEST["InvId"], $params, $_REQUEST["SignatureValue"]);

    if($check == true){

        //Success code

    }else{

        //Failure code

    }
}

```

5. Проверка параметров результата (URL Result в настройках магазина RK). Запрос отправляет сама Robokassa

```
<?php

if (!empty($_REQUEST["OutSum"]) && !empty($_REQUEST["InvId"]) && !empty($_REQUEST["SignatureValue"])) {

    $params = array();
    if (!empty($_REQUEST)) {
        foreach ($_REQUEST as $key => $value) {
            if (strpos($key, 'shp_') !== false) {
                $x          = str_replace('shp_', '', $key);
                $params[$x] = $value;
                $log_params .= $x . ' - ' . $value . ' ; ';
            }
        }
    }

    $Robokassa = new Robokassa($RK);

    $check = $Robokassa->Result($_REQUEST["OutSum"], $_REQUEST["InvId"], $params, $_REQUEST["SignatureValue"]);

    if($check == true){

        //Success code

    }else{

        //Failure code

    }
}

```