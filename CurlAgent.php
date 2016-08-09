<?php
	class CurlAgent {
		 protected $urlToken;
		 protected $urlPayment;
		 protected $urlTest;

		 protected $oauthData;
		 protected $paymentData;

		 public function __construct($urlToken, $urlPayment, $urlTest)
		 {
			 $this->urlToken = $urlToken;
			 $this->urlPayment = $urlPayment;
			 $this->urlTest = $urlTest;
		 }

		 public function setOauthData ($oauthData)
		 {
			$this->oauthData = $oauthData;
		 }

		 public function setPaymentData ($paymentData)
		 {
			$this->paymentData = $paymentData;
		 }

		/*
		* Requête d'authentification du développeur
		* pour récupération de l'ACCESS TOKEN de sécurité.
		* methode: POST
		*/
		 public function authenticate()
		 {
			 $ch = curl_init();
			 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
			 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			 curl_setopt($ch, CURLOPT_URL, $this->urlToken);			// URL du service OAuth d'eashmobileapi.
			 curl_setopt($ch, CURLOPT_POST, true );						// On spécifi la requête post.
			 curl_setopt($ch, CURLOPT_POSTFIELDS, $this->oauthData);	// Coordonnées du développeur.
			 curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

			 $result = curl_exec($ch);									// On exécute la requête

			 $curl_errno = curl_errno($ch);								// Récupération du code d'erreur renvoyé par curl.
			 $curl_error = curl_error($ch);								// Description de l'érreur renvoyé par curl.
			 curl_close($ch);											// Fermeture de la requête curl.

			 if ($curl_errno > 0) {
				 echo "{'error': 'YES', 'code': $curl_errno, 'message': $curl_error}";
			 } else {
				return $result;											// On retourne le résultat renvoyé par l'API.
			 }
		  }

		/*
		* Requête de demande de payment.
		* methode: GET
		* format: Encodé pour les url.
		*/
		public function requestPayment()
		 {
			$data_string = http_build_query($this->paymentData);
			$this->urlPayment = $this->urlPayment . '?' . $data_string;// Données encodé dans l'url.

			 $chPay = curl_init();
			 curl_setopt($chPay, CURLOPT_SSL_VERIFYPEER, 1);
			 curl_setopt($chPay, CURLOPT_SSL_VERIFYHOST, 2);
			 curl_setopt($chPay, CURLOPT_URL, $this->urlPayment);		// URL de demande de payment.
			 curl_setopt($chPay, CURLOPT_RETURNTRANSFER,true);

			 $result = curl_exec($chPay);

			 $curl_errno = curl_errno($chPay);
			 $curl_error = curl_error($chPay);
			 curl_close($chPay);

			 if ($curl_errno > 0) {
				 echo "{'error': 'YES', 'code': $curl_errno, 'message': $curl_error}";
			 } else {
				return $result;											// On retourne le résultat renvoyé par l'API.
			 }
		 }

	}
?>
