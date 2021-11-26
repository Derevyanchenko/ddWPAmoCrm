<?php

if ( ! class_exists( 'ddAmoCrm' ) ) 
{
	class ddAmoCrm
	{

		public $subdomain;
		public $basicUrl;
		public $accessToken;
		public $refreshToken = '';

		public function __construct()
		{
			$this->subdomain = 'danielderev';
			$this->basicUrl = "https://{$this->subdomain}.amocrm.ru";

			add_action( 'init', [$this, 'createLeads'] );
		}

		public function test()
		{
			$this->getAccessToken($this->refreshToken);
		}

		/**
		 * Get Auth and Refresh Token
		 */
		public function getRefreshToken()
		{
			$link = "{$this->basicUrl}/oauth2/access_token";
			$data = [
				'client_id' => '',
				'client_secret' => '',
				'grant_type' => 'authorization_code',
				'code' => '',
				'redirect_uri' => 'https://danielderev.amocrm.ru/',
			];

			$response = wp_remote_post($link, array(
				'user-agent' => 'amoCRM-oAuth-client/1.0',
				'headers' => array(
					'Content-Type' => 'application/json',
				),
				'body' => json_encode( $data ),
				'sslverify' => false,

			));

			// проверка ошибки
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo "Что-то пошло не так: $error_message";
				return;
			}

			$body = wp_remote_retrieve_body( $response );
			$body = json_decode( $body, true );


			$tokens = array(
				'access_token' => $body['access_token'],
				'refresh_token' => $body['refresh_token']
			);

			return $tokens;
		}

		/**
		 * Getting A New Access Token Once It Expires
		 */
		public function getAccessToken($refreshToken)
		{
			$link = "{$this->basicUrl}/oauth2/access_token";
			$data = [
				'client_id' => '',
				'client_secret' => '',
				'grant_type' => 'refresh_token',
				'refresh_token' => $refreshToken,
				'redirect_uri' => '',
			];

			$response = wp_remote_post($link, array(
				'user-agent' => 'amoCRM-oAuth-client/1.0',
				'headers' => array(
					'Content-Type' => 'application/json',
				),
				'body' => json_encode( $data ),
				'sslverify' => false,

			));

			// проверка ошибки
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo "Что-то пошло не так: $error_message";
				return;
			}

			$body = wp_remote_retrieve_body( $response );
			$body = json_decode( $body, true );

			if ( $body ) {
				$body["endTokenTime"] = time() + $body["expires_in"];
				$bodyJson = json_encode( $body );

				$jsonTokensFile = 'tokens.json';
				$f = fopen( $jsonTokensFile, 'w' );
				fwrite($f, $bodyJson);
				fclose( $f );

				$body = json_decode($bodyJson, true);
			}

			$accessToken = $body['access_token'];

			echo '<pre>';
			print_r($body);
			echo '</pre>';

			return $accessToken;

		}

		/**
		 * Create Leads method
		 */
		public function createLeads()
		{
			$dataToken = file_get_contents('tokens.json');
			$dataToken = json_decode( $accessToken, true );

			
			var_dump( $dataToken );
			wp_die();

			if ( $dataToken['endTokenTime'] < time() ) {
				echo 'if';
				$accessToken = $this->getAccessToken( $dataToken["refresh_token"] );
			} else {
				echo 'else';
				$accessToken = $dataToken["access_token"];
			}

			$link = "{$this->basicUrl}/api/v2/leads";

			var_dump( $accessToken );
			wp_die();

			$leads = array();
			$leads['add'] = array(
				'name'=>'Pencil Deal',
				'created_at'=>1298904164,
				'status_id'=>142,
				'sale'=>300000,
				'responsible_user_id'=>215302,
				'tags' => 'Important, USA', 
			);

			$response = wp_remote_post($link, array(
				'sslverify' => false,
				'user-agent' => 'amoCRM-oAuth-client/1.0',
				'headers' => array(
					'Authorization' => 'Bearer ' . $accessToken,
					'Content-Type' => 'application/json',
				),

			));

			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo "Что-то пошло не так: $error_message";
				return;
			}

			$body = wp_remote_retrieve_body( $response );
			$body = json_decode( $body, true );

		}

	}

	$ddAmoCrm = new ddAmoCrm();

}
