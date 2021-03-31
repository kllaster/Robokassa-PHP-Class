<?php

class Robokassa
{
	/**
	 * @var string
	 */
	protected $MerchLogin = "";

	/**
	 * @var array
	 */
	protected $Password = array();

	/**
	 * @var bool
	 */
	protected $Testing = false;

	function __construct($params)
	{
		if (empty($params) || !is_array($params))
			die('Не передан массив с данными магазина Robokassa. Прекращение работы!');

		if (!empty($params['name']))
			$this->MerchLogin = $params['name'];

		if (!empty($params['pass1']) && !empty($params['pass2']))
		{
			$this->Password[1] = $params['pass1'];
			$this->Password[2] = $params['pass2'];
		}
		else
			die('Нет паролей от магазина Robokassa. Прекращение работы!');

		if (!empty($params['test']))
			$this->Testing = $params['test'];
		else
			$this->Testing = false;
	}

	/**
	 * Переадресация на страницу оплаты.
	 * @param int $order_id - ID заказа (InvId)
	 * @param float $sum - Сумаа заказа (OutSum)
	 * @param string $desc - Описание заказа (InvDesc)
	 * @param array|null $params - Кастомные параметры
	 * @param string|null $pay_method - Способ оплаты (IncCurrLabel)
	 * @param string|null $lang - Язык (Culture)
	 */
	public function redirect(int $order_id, float $sum, string $desc,
							 array $params = null, string $pay_method = null,
							 string $lang = null)
	{
		$signature = $this->get_signature('redirect', $order_id, $sum, $params);
		$desc = urlencode($desc);
		$redirect_url = "http://auth.robokassa.ru/Merchant/Index.aspx?".
						"MrchLogin=".$this->MerchLogin.
						"&OutSum=".$sum.
						"&InvId=".$order_id.
						"&InvDesc=".$desc.
						"&Desc=".$desc.
						"&SignatureValue=".$signature.
						"&Encoding=UTF-8";
		if (!empty($pay_method))
			$redirect_url .= "&IncCurrLabel=".$pay_method;
		if (!empty($lang))
			$redirect_url .= "&Culture=".$lang;
		if ($this->Testing == true)
			$redirect_url .= "&isTest=1";
		if (!empty($params))
		{
			foreach ($params as $key => $value)
				$redirect_url .= "&shp_".$key."=".urlencode($value);
		}
		header("Location: ".$redirect_url);
	}

	/**
	 * Проверка ответа от Robokassa.
	 * @param string $request - Какой ответ проверяем (result, success)
	 * @param array $params - Кастомные параметры
	 * @return bool
	 */
	public function check_response(string $request, array $params): bool
	{
		if (!empty($request) && !empty($_REQUEST["SignatureValue"]))
		{
			$my_sig = $this->get_signature($request, 0, 0, $params);
			if (strtoupper($my_sig) == strtoupper($_REQUEST["SignatureValue"]))
				return (true);
			else
				return (false);
		}
		return (false);
	}

	/**
	 * Получение сигнатуры заказа (md5 хеша).
	 * @param string $request - Для какого запроса создаем (redirect, result, success)
	 * @param int $order_id - ID заказа (InvId)
	 * @param float $sum - Сумаа заказа (OutSum)
	 * @param array $params - Кастомные параметры
	 * @return string
	 */
	private function get_signature(string $request, int $order_id, float $sum, array $params): string
	{
		$sig = "";
		switch ($request)
		{
			case 'redirect':
			{
				$sig = $this->MerchLogin.":"."$sum".":".$order_id.":".$this->Password[1];
				break ;
			}
			case 'success':
			{
				$sig = $_REQUEST["OutSum"].":".$_REQUEST["InvId"].":".$this->Password[1];
				break ;
			}
			case 'result':
			{
				$sig = $_REQUEST["OutSum"].":".$_REQUEST["InvId"].":".$this->Password[2];
				break ;
			}
		}
		if (!empty($params))
		{
			foreach ($params as $key => $value)
				$sig .= ":shp_" . $key . "=" . $value;
		}
		return (md5($sig));
	}
}
