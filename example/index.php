<?

require_once __DIR__ . '../Robokassa.class.php';

$RK = [
    'name' => 'Name_Shop',                  // Имя магазина
    'pass1' => '********************',      // Пароль 1
    'pass2' => '********************',      // Пароль 2
    'test' => false,                        // Тестовые данные или нет (true / false)
    'logs' => true                          // Вести лог действий или нет (true / false)
];

function Buy($price, $invid, $pay_metod, $params){

    $pay_metod = 'Qiwi40QiwiRM';
    $info_desk = 'Test Test Test';

    $Robokassa = new Robokassa($RK);
    $Robokassa->Redirect($price, $invid = 0, $pay_metod, $info_desk, $params);
}

function Success(){

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
    
            echo 'OK';
    
        }else{
    
            echo ' Ohh, No..';
    
        }
    }

}

function Result(){

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

}