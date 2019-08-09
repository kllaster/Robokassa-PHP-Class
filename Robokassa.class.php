<?php

class Robokassa{

    /**
    * @var string
    */
    protected $MerchLogin = "";

    /**
    * @var char
    */
    protected $Password = array();

    /**
    * @var bool
    */
    protected $Testing = false;

    /**
    * @var bool
    */
    protected $Logs = true;

    
    function __construct($params){
        
        if (is_array($params)) {

            if (!empty($params['logs'])) {
                $this->Logs = (bool) $params['logs'];
            }else{
                $this->Logs = false;
            }

            if (!empty($params['name'])) {
                $this->MerchLogin = $params['name'];
            }

            if (!empty($params['pass1']) && !empty($params['pass2'])) {
                $this->Password[1] = $params['pass1'];
                $this->Password[2] = $params['pass2'];
            }else{
                if($this->Logs){
                    $this->Logs('[RK] Нет пароля Robokassa. Прекращение работы!');
                }
                exit;
            }

            if(!empty($params['test'])){
                $this->Testing = $params['test'];
            }else{
                $this->Testing = false;
            }

        }
    }

    // $sum - Сумаа заказа (OutSum)
    // $in_curr - Способ оплаты (IncCurrLabel)
    // $info_desk - Описание заказа (InvDesc)
    // $params - параметры
    // $lang - Язык (Culture)

    public function Redirect($sum, $invid, $pay = 'any', $info_desk, $params, $lang = 'ru'){

        $signature = $this->genSig($sum, $invid, $params, 'redirect');

        $redirect_url = "http://auth.robokassa.ru/Merchant/Index.aspx?MrchLogin=" . $this->MerchLogin . "&OutSum=" . $sum ."&InvId=" . $invid . "&InvDesc=" . urlencode($info_desk) . "&Desc=" . urlencode($info_desk) . "&Encoding=UTF-8&SignatureValue=" . $signature;
        
        if($this->Testing == true){
            $redirect_url .= "&isTest=1";
        }

        if(!empty($pay) && $pay != 'any'){
            $redirect_url .= "&IncCurrLabel=" . $pay;
        }

        if(!empty($params)){
            foreach ($params as $key => $value) {
                $redirect_url .= "&shp_" . $key . "=" . urlencode($value);
            }
        }

        header("Location:" . $redirect_url);
    }

    private function genSig($sum, $invid, $params, $signID){

        if($signID == 'redirect'){
            $sig = $this->MerchLogin . ":" . $sum . ":" . $invid . ":" . $this->Password[1];
        }else if($signID == 'success'){
            $sig = $sum . ":" . $invid . ":" . $this->Password[1];
        }else if($signID == 'result'){
            $sig = $sum . ":" . $invid . ":" . $this->Password[2];
        }
        
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $sig .= ":shp_" . $key . "=" . $value;
            }
        }

        return md5($sig);
    }

    public function Success($sum, $invid, $params, $crc){    

        if (!empty($sum) && !empty($params) && !empty($crc)){

            $my_crc = $this->genSig($sum, $invid, $params, 'success');
        
            if(strtoupper($my_crc) == strtoupper($crc)){
                return true;
            }else{
                return false;
            }
        }

        return false;
    }

    public function Result($sum, $invid, $params, $crc){    

        if (!empty($sum) && !empty($params) && !empty($crc)){

            $my_crc = $this->genSig($sum, $invid, $params, 'result');
        
            if(strtoupper($my_crc) == strtoupper($crc)){
                return true;
            }else{
                return false;
            }
        }

        return false;
    }

    private function Logs($log){
        $file = 'donate.log';
    
        $t = $log . PHP_EOL;
        
        $text = file_get_contents($file);
        $text .= $t;
    
        file_put_contents($file, $text);
        echo $t;
    }
}
