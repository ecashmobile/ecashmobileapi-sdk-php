<?php
	require("CurlAgent.php"); // Necessité d'utiliser curl pour les requêtes externes.

	/*
	* SDK d'EcashMobileAPI
	* Version V0.8
	*/
	class EcashMobileAPI{
		/*
		* Identification du développeur(client id).
		*/
		private $clientId;
		/*
		* Clé secrète du développeur(client secret).
		*/
		private $clientSecret;
		/*
		* Nom d'utilisateur du développeur.
		*/
		private $username;
		/*
		* Mot de passe du dévéloppeur.
		*/
		private $password;

		// URL du serveur OAuth d'Ecashmobileapi.
		private $URL_TOKEN = "http://api.ecashmobile.com/oauth/v2/token";
		// private $URL_TOKEN = "localhost:8000/oauth/v2/token";
		// URL dy payment MoMo via ecashmobileapi.
		private $URL_PAYMENT = "http://api.ecashmobile.com/api/mtn/withdraw";
		// private $URL_PAYMENT = "localhost:8000/api/mtn/withdraw";
		// Url de test de l'api
		private $URL_TEST = "http://api.ecashmobile.com/api/articles";
		// private $URL_TEST = "localhost:8000/api/articles";

		private $curlAgent;				// L'instance curl utilisé pour faire les requêtes post.

		private $accessToken;
		private $refreshToken;			// Refresh token à utiliser lors de l'expiration du token actuel.

		private $accessTokenFetched;	// Token récupéré pour la première fois.
		private $tokenDate;				// Date d'expiration du token actuellement utiliser.


		// Objet singleton servant à faire les requêtes ves le serveur.
		public static $instance = NULL;

		/**
		* Object singleton permettant de conserver l'état de  l'utilisation
		* de l'API:
		* les coordonnées du dévelopeur, l'access token, le refresh token,
		* la date d'expirationd du token et les url à appeller.
		*/
		public static function getInstance($clientId, $clientSecret, $username, $password) {
			if (static::$instance === NULL) {
				echo "I seem to be working";
				static::$instance = new EcashMobileAPI();

				static::$instance->clientId = $clientId;
				static::$instance->clientSecret = $clientSecret;
				static::$instance->username = $username;
				static::$instance->password = $password;

				static::$instance->accessToken = "0000000000";
				static::$instance->refreshToken = "0000000000";
				static::$instance->accessTokenFetched = false;
				static::$instance->tokenDate = time();

				static::$instance->curlAgent = new CurlAgent(static::$instance->URL_TOKEN, static::$instance->URL_PAYMENT, static::$instance->URL_TEST);
			}

			return static::$instance;
		}

		/**
		* Protected constructor to prevent creating a new instance of the
		* *Singleton* via the `new` operator from outside of this class.
		*/
		protected function __construct()
		{
		}

		/*
		* <p>Authentification du developpeur à l'aide de ses credentials.</p>
		* Et Mise à jour automatique du access token lors de son expiration
		*/
		public function oauthAuthenticate (){
			// Date courante pour tester si le token de sécurité est expiré.
			$currentDate = time();

			// echo "\n \n";
			// echo static::$instance->accessTokenFetched ? 'Bool is: True' . "\n" : 'Bool is: False' . "\n";

			// Si le token a déjà été récupéré pour la première
			// Fois on l'actualise juste ou on le revoi s'il n'est pas expiré.
			if($this->accessTokenFetched == true){
				if(static::$instance->tokenDate <= $currentDate){
					// On retourne le token puisqu'il n'est pas expiré.
					return static::$instance->accessToken;
				}else{
					// On actualise le token de sécurité.
					// On configure les données du développeurs.
					$instance->curlAgent->setOauthData(Array("client_id"=>static::$instance->clientId, "client_secret"=>static::$instance->clientSecret, "refresh_token"=>static::$instance->refreshToken, "grant_type"=>"refresh_token"));
					// Autentification.
					$response = $this->curlAgent->authenticate();
					$jsonResponse = json_decode($response);

					// Récupération du token de sécurité.
					$developerAccessToken = $jsonResponse->access_token;
					static::$instance->tokenDate = time() + 3600;
					// On spécifie que le token de sécurité a déjà été récupérer pour la première fois.
					$this->accessTokenFetched = true;
					static::$instance->accessToken = $developerAccessToken;
					static::$instance->refreshToken =  $jsonResponse->refresh_token;

					return static::$instance->accessToken;
				}
			}else{
				static::$instance->curlAgent->setOauthData(Array("client_id"=>static::$instance->clientId, "client_secret"=>static::$instance->clientSecret, "username"=>static::$instance->username, "password"=>static::$instance->password, "grant_type"=>"password"));
				// Autentification.
				$responseTwo = $this->curlAgent->authenticate();
				$jsonResponseTwo = json_decode($responseTwo);

				// Récupération du token de sécurité.
				$developerAccessToken = $jsonResponseTwo->access_token;
				static::$instance->tokenDate = time() + 3600;

				// On spécifie que le token de sécurité a déjà été récupérer pour la première fois.
				$this->accessTokenFetched = true;
				static::$instance->accessToken = $developerAccessToken;
				static::$instance->refreshToken =  $jsonResponseTwo->refresh_token;

				return static::$instance->accessToken;
			}
		}

		/*
		* Demande de payment adréssé à un client possédant un numéro de téléphone.
		*/
		public function requestPayment($amountToDebited, $customerPhoneNumber){
			// echo "ACCESS TOKEN IS:" . static::$instance->accessToken;
			static::$instance->curlAgent->setPaymentData(Array("access_token"=>static::$instance->accessToken, "amount"=>$amountToDebited, "phoneNumber"=>$customerPhoneNumber));

			$response = static::$instance->curlAgent->requestPayment(); // Demande de payment.
			$jsonResponse = json_decode($response); 					// Décodage du json renvoyé.

			return $response;
		}

	}
?>
 
