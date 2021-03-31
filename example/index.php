<?

require_once __DIR__ . '../Robokassa.php';

$RK = [
	'name' => 'shop_name',                  // Имя магазина
	'pass1' => '********************',      // Пароль 1
	'pass2' => '********************',      // Пароль 2
	'test' => false,                        // Тестовые данные или нет (true / false)
];

function Buy($order_id, $price, $pay_method, $params)
{
	global $RK;

	$desc = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
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
	$Robokassa->redirect($order_id, $price, $desc, $params, $pay_method, 'RU');
}

function Success()
{
	global $RK;

	if (!empty($_REQUEST["OutSum"]) && !empty($_REQUEST["InvId"]) && !empty($_REQUEST["SignatureValue"]))
	{
		$params = get_shp_params();
		$Robokassa = new Robokassa($RK);

		/**
		 * Проверка ответа от Robokassa.
		 * @param string $request - Какой ответ проверяем (result, success)
		 * @param array $params - Кастомные параметры
		 * @return bool
		 */
		$check = $Robokassa->check_response('success', $params);
		if ($check == true)
			echo 'Your order is being processed';
		else
			echo 'Payment failed';
	}
	else
		echo 'Bad request';
}

function Result()
{
	global $RK;

	if (!empty($_REQUEST["OutSum"]) && !empty($_REQUEST["InvId"]) && !empty($_REQUEST["SignatureValue"]))
	{
		$params = get_shp_params();
		$Robokassa = new Robokassa($RK);

		/**
		 * Проверка ответа от Robokassa.
		 * @param string $request - Какой ответ проверяем (result, success)
		 * @param array $params - Кастомные параметры
		 * @return bool
		 */
		$check = $Robokassa->check_response('result', $params);
		if ($check == true)
			echo 'Payment was successful';
		else
			echo 'Payment failed';
	}
	else
		echo 'Bad request';
}

function get_shp_params(): array
{
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
	return ($params);
}