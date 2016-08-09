<?php
// Utilisation du sdk pour la configuration de l'API et les appels rest POST.
require("ecashmobileapi.php");
/*
    session_start();

    if(!isset($_SESSION['accessTokenFetched'])){
        $_SESSION['accessTokenFetched'] = false;
    }
    if(!isset($_SESSION['tokenDate'])){
        $_SESSION['tokenDate'] = time() + 3600;
    }
    if(!isset($_SESSION['accessToken'])){
        $_SESSION['accessToken'] = "0000000000";
    }
    if(!isset($_SESSION['refreshToken'])){
        $_SESSION['refreshToken'] = "0000000000";
    }
*/

//$ecashAPI = new EcashMobileAPI("1_4jwm37vfn7s4wc4w0ggwkc4804ck40004ocwsc00sc8k8s4g0k", "1tuz1fhnop8g4sk44g8scwkcks4ogc0kwo4co4cc88og88cgck", "dassi", "dassi");

// Récupération d'une instance de la classe principale du sdk: EcashMobileAPI + configuration des donnés du developpeur.
$ecashAPI = EcashMobileAPI::getInstance("1_4jwm37vfn7s4wc4w0ggwkc4804ck40004ocwsc00sc8k8s4g0k", "1tuz1fhnop8g4sk44g8scwkcks4ogc0kwo4co4cc88og88cgck", "dassi", "dassi");

// Autentification du développeur et récupération du token d'accées ou mise à jour de ce dernier.
$acessTk = $ecashAPI->oauthAuthenticate();

echo "\n \n \n The access token is: $acessTk";

echo "\n \n \n";

// Here we will checkout customer with phoneNumber XXXXXXXXX with amount YYYYYYYY
$phoneNumber = "672252530"; // Numéro du client à débiter.
$amount = "1";              // Montant à débiter.

// Demande de paiement adressée au client pour validation sur son téléphone portable.
$resPayment = $ecashAPI->requestPayment($amount, $phoneNumber);

echo "\n \n \n";
var_dump($resPayment);

?>
