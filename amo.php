<?php

// amo crm integration

if ( ! class_exists( 'ddAmo' ) ) {

    class ddAmo
    {

        public function __construct()
        {
            add_action( 'init', [$this, 'test'] );
        }

        public function test()
        {
            // echo '<h1>Test Run</h1>';
        }

    }

    $ddAmo = new ddAmo();

}
/*******
 * Поля в амо CRM для инет-магазина:
 * - имя
 * - название товара
 * - бюджет
 * - артикул
 * - кол-во
 * - способ оплаты
 * - способ доставки
 * 
1.) заказ в холде (юзер добавил в корзину и оплатил) - привязываем к хуку статус_заказа_в_холде нашу фунцию, которая будет выполнять отправку заказа в CRM

2.) в параметрах функции принимаем id заказа, через функциию получаем инфу о всем заказе по этому ID

3.) создаем все нужые переменные, в соответствии с нужными полями в АМО (продукты, артикулы, кол-во)

$product_name = '';
$products = [];
$price = '';
$name = '';
$name = '';
$name = '';
$name = '';

4.) проходимся циклом по обьекту заказа и получаем все заказанные продукты - $order->get_items()

5.) в цикле в массив продуктов записываем инфу всю - имя_продукта, артикуль (get_sku()), qty

6.) переводим массив продуктов и массив артикулей в строку - $products = implode(' | ', $products), $articules = implode(' | ', $articules);

7.) в переменную $payment_title получаем инфу об способе оплаты

8.) так же в переменную получаем инфу о способе доставки ($sgipping_title)

9.) Получаем инфу о покупателе 
- $customer_email
- $customer_name
- $customer_last
- $customer_phone

10.) формируем данные об отправке запроса в CRM

$user = array(
	'USER_LOGIN' => 'почтовый_ящик_на_который_зареган_акк_срм',
	'USER_HASH' => 'API_KEY',
);

11.) созаем переменную поддомена - нужно для формирования УРЛ запроса (поддомен берем в УРЛ нашей СРМ - у меня это - danilderevyanchenko)

12.) формируем ссылку
$link = 'https://' . $subdomain . '.amocrm.ru/private/api.auth.php?type=json';

13.) выполняем запрос
$Response = jsin_decode(curlSend($link, $user), true);

14.) пишем функцию curl_send и curl_get

15.) делаем запрос на авторизацию, получаем нужные данные и поля, ид статусов и т.п., токен

16.) зная поля - отправляем запрос на создание сделки - в доке АПИ берем УРЛ сделки и параметры с примерами

17.) отправляем запрос через curl_send на contacts/set , для добавления в список контактов юзера.

*******/


// add_action( 'woocommerce_order_status_on-hold', 'amoAddTask', 1 );

function amoAddTask($order_id) {
	// $order = wc_get_order($order_id);

	// echo '<pre>';
	// var_dump($order);
	// echo '</pre>';

	// $order = new WC_Order(post->ID);
	// $order_id = $order->get_order_number();

	// echo $order_id;
	// wp_die($order_id);

	// $arrContactParams = [
	// 	// поля для сделки 
	// 	"PRODUCT" => [
	// 		"nameForm"	=> "Название формы",
	
	// 		"nameProduct" 	=> "Название товара",
	// 		"price"		=> "Цена",
	// 		"descProduct"	=> "Описание заказа",
	
	// 		"namePerson"	=> "Имя пользователя",
	// 		"phonePerson"	=> "Телефон",
	// 		"emailPerson"	=> "Email пользователя",
	// 		"messagePerson"	=> "Сообщение от пользователя",
	// 	],
	// 	// поля для контакта 
	// 	"CONTACT" => [
	// 		"namePerson"	=> "Имя пользователя",
	// 		"phonePerson"	=> "Телефон",
	// 		"emailPerson"	=> "Email пользователя",
	// 		"messagePerson"	=> "Сообщение от пользователя",
	// 	]
	// ];

	$leads = array(
		array(
			"name" => "Сделка для примера 1",
			"price" => 150,
			"custom_fields_values" => array(
				array(
					"field_id" => 745879,
					"values" => array(
						array(
							"value" => "Тест имя",
						),
					),
				),
				array(
					"field_id" => 745881,
					"values" => array(
						array(
							"value" => "Тест название товара",
						),
					),
				),
				array(
					"field_id" => 745885,
					"values" => array(
						array(
							"value" => "Тест артикул",
						),
					),
				),
			),
		),
	);

	$leads = json_encode($leads, JSON_UNESCAPED_UNICODE);

	echo '<pre>';
	print_r($leads);
	echo '</pre>';

	wp_die($result);

	// send 
	$subdomain = 'danilderevyanchenko';
	$link='https://' . $subdomain . '.amocrm.ru/api/v4/leads';

	$access_token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjcyZTBjZGNhMTNmZjZhMGJjZGNlZmRkNDY2ZjM2NDM0YzQxODBlNGEwMzA0NzhmMjJjNjNiOTBlZjQzNjk2YzkxYjM0M2NiZjU4ZTA0MTNjIn0.eyJhdWQiOiJhZTViOGZiYy0zNWYyLTRjZjYtODcwNC02OTJkMTBmYTQ4MTciLCJqdGkiOiI3MmUwY2RjYTEzZmY2YTBiY2RjZWZkZDQ2NmYzNjQzNGM0MTgwZTRhMDMwNDc4ZjIyYzYzYjkwZWY0MzY5NmM5MWIzNDNjYmY1OGUwNDEzYyIsImlhdCI6MTYzNDY2MDg1OCwibmJmIjoxNjM0NjYwODU4LCJleHAiOjE2MzQ3NDcyNTgsInN1YiI6Ijc1MzM1MzUiLCJhY2NvdW50X2lkIjoyOTc1NjcxMywic2NvcGVzIjpbInB1c2hfbm90aWZpY2F0aW9ucyIsImNybSIsIm5vdGlmaWNhdGlvbnMiXX0.nixS7ugfFqKkQJUHBqG3rH8C7jMTbmHrI1iDx8KrBz1iX0WvyRMDSBg76eUnZnBpkgp63k3JfqGYXtt4swR3TL_TMkaMX87aXMOBvS5tUxr0VQgMEKUvOGPIXqsn6VtfIUSqwQdhVVhHYIWnXhaAFZ72d4wh-sgokM18dfK9ai03-tgzmqgYgaeSGvsc6_DngDxP6zCV8qAlWiJmjEggbw9-8V-QuaVI7eccld3OMx5b9tMEsk6FsxJIr6hAoBIBbYWhaWqXXd7Rc6D98KO6SD3_GGhmyVqybqfApk9y3RZBJ2n2252Bkre_EIPSa39BF9UQdmsEbfxt8Dmpa0Ttcg";

	$headers = [
        "Accept: application/json",
        'Authorization: Bearer ' . $access_token
	];


	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_USERAGENT, "amoCRM-API-client-
	undefined/2.0");
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($leads));
	curl_setopt($curl, CURLOPT_URL, $link);
	curl_setopt($curl, CURLOPT_HEADER,false);
	curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__)."/cookie.txt");
	curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__)."/cookie.txt");
	$out = curl_exec($curl);
	curl_close($curl);
	$result = json_decode($out,TRUE);
	
	echo '<pre>';
	print_r($result);
	echo '</pre>';

	wp_die($result);
}



function getAuthAndRefreshToken() {
	$subdomain = 'danilderevyanchenko'; //Поддомен нужного аккаунта
	$link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса

	/** Соберем данные для запроса */
	$data = [
		'client_id' => 'ae5b8fbc-35f2-4cf6-8704-692d10fa4817', // id нашей интеграции
		'client_secret' => '5orGaeye9H5Hu5vm3zET1IdkCkmjQFJLz7Dj3ELVglOEpSqyAI4C0SAxCtMgNnbF', // секретный ключ нашей интеграции
		'grant_type' => 'authorization_code',
		'code' => 'def50200b9b500ed601e4c0c1b4d2260e4d32dbb8c879644c7d176958bcf4e2c3c2b3a9f9baf9c554e588605c375370e6566c1885548f22f54e714affc9d797918232f2817f14c91faea0e76133a64f856a12daf5db25aa9ec04641ed0ee75f18ecae48905276c6ac4dfd4e99908c4b57969cda6034593a8daf7369492685cb088537159cc77f4f2769c83b7b9bc30705290b643ac7cbcce2f7dfb7c59d7b9443fd3beb833626cf3f5ff2e5d8f5b24e6aa7c32c37c99b7fb9e711dd1cd3c783e82091037ee88ea45bd6fbec127037e66eb5905fde3b6671165edb3d828b8acc582240c7dab90a87efac8e6958fe60236133ba54db696cff37682430d5afcf49134bb0b1c490fec560f51f835d96060b6b66e7292cd7f6db82925cf933e1d238b703cb99fb3c5e5ce0f52ce89358292ff67c7b26fa2e5250e8178b6d0f668dec297a680b9991c833e70391219688bf3b47b05dc1cee3301420a23c7127366834d09b7ec70cc046e6ab13fccf77669bf6c059678d6efa322df511d1e8081c55f09b1db173c8b6df6dd9ff1bf1eaa7b282f08e457616aeaf4ec122230b05df44c5f039eb808a419efedb49a129a31d6b93bcdb46cdd4f4641b6519b525c4770b5e2832f937797838f9b928df8bd8fc1e923d8', // код авторизации нашей интеграции
		'redirect_uri' => 'https://danilderevyanchenko.amocrm.ru/',// домен сайта нашей интеграции
	];
	
	/**
	 * Нам необходимо инициировать запрос к серверу.
	 * Воспользуемся библиотекой cURL (поставляется в составе PHP).
	 * Вы также можете использовать и кроссплатформенную программу cURL, если вы не программируете на PHP.
	 */
	$curl = curl_init(); //Сохраняем дескриптор сеанса cURL
	/** Устанавливаем необходимые опции для сеанса cURL  */
	curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
	curl_setopt($curl,CURLOPT_URL, $link);
	curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
	curl_setopt($curl,CURLOPT_HEADER, false);
	curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
	$out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);
	/** Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
	$code = (int)$code;
	
	// коды возможных ошибок
	$errors = [
		400 => 'Bad request',
		401 => 'Unauthorized',
		403 => 'Forbidden',
		404 => 'Not found',
		500 => 'Internal server error',
		502 => 'Bad gateway',
		503 => 'Service unavailable',
	];
	
	try
	{
		/** Если код ответа не успешный - возвращаем сообщение об ошибке  */
		if ($code < 200 || $code > 204) {
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
		}
	}
	catch(\Exception $e)
	{
		die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
	}
	
	/**
	 * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
	 * нам придётся перевести ответ в формат, понятный PHP
	 */
	$response = json_decode($out, true);
	
	$access_token = $response['access_token']; //Access токен
	$refresh_token = $response['refresh_token']; //Refresh токен
	$token_type = $response['token_type']; //Тип токена
	$expires_in = $response['expires_in']; //Через сколько действие токена истекает
	
	// выведем наши токены. Скопируйте их для дальнейшего использования
	// access_token будет использоваться для каждого запроса как идентификатор интеграции
	// var_dump($access_token);
	// echo '<br><br><br>';
	// var_dump($refresh_token );
	// var_dump($expires_in );
}

function returnFieldsContact() {

	$access_token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjcyZTBjZGNhMTNmZjZhMGJjZGNlZmRkNDY2ZjM2NDM0YzQxODBlNGEwMzA0NzhmMjJjNjNiOTBlZjQzNjk2YzkxYjM0M2NiZjU4ZTA0MTNjIn0.eyJhdWQiOiJhZTViOGZiYy0zNWYyLTRjZjYtODcwNC02OTJkMTBmYTQ4MTciLCJqdGkiOiI3MmUwY2RjYTEzZmY2YTBiY2RjZWZkZDQ2NmYzNjQzNGM0MTgwZTRhMDMwNDc4ZjIyYzYzYjkwZWY0MzY5NmM5MWIzNDNjYmY1OGUwNDEzYyIsImlhdCI6MTYzNDY2MDg1OCwibmJmIjoxNjM0NjYwODU4LCJleHAiOjE2MzQ3NDcyNTgsInN1YiI6Ijc1MzM1MzUiLCJhY2NvdW50X2lkIjoyOTc1NjcxMywic2NvcGVzIjpbInB1c2hfbm90aWZpY2F0aW9ucyIsImNybSIsIm5vdGlmaWNhdGlvbnMiXX0.nixS7ugfFqKkQJUHBqG3rH8C7jMTbmHrI1iDx8KrBz1iX0WvyRMDSBg76eUnZnBpkgp63k3JfqGYXtt4swR3TL_TMkaMX87aXMOBvS5tUxr0VQgMEKUvOGPIXqsn6VtfIUSqwQdhVVhHYIWnXhaAFZ72d4wh-sgokM18dfK9ai03-tgzmqgYgaeSGvsc6_DngDxP6zCV8qAlWiJmjEggbw9-8V-QuaVI7eccld3OMx5b9tMEsk6FsxJIr6hAoBIBbYWhaWqXXd7Rc6D98KO6SD3_GGhmyVqybqfApk9y3RZBJ2n2252Bkre_EIPSa39BF9UQdmsEbfxt8Dmpa0Ttcg";

	$headers = [
		"Accept: application/json",
		'Authorization: Bearer ' . $access_token
	];

	$subdomain = 'danilderevyanchenko';
	$link='https://' . $subdomain . '.amocrm.ru/api/v4/leads/custom_fields';

	$curl = curl_init();
	curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
	curl_setopt($curl,CURLOPT_URL, $link);
	curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl,CURLOPT_HEADER, false);
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
	$out=curl_exec($curl); 
	$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
	curl_close($curl);
	$response=json_decode($out,true);

	echo '<pre>';
	print_r($response);
	echo '</pre>';

	wp_die($response);
}

// returnFieldsContact();