<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Twilio\Rest\Client;

define('TWILIO_SID', 'TU_ACCOUNT_SID');
define('TWILIO_TOKEN', 'TU_AUTH_TOKEN');
define('TWILIO_NUMBER', '+1XXXXXXXXXX');

function enviarSMS($para, $mensaje) {
    $client = new Client(TWILIO_SID, TWILIO_TOKEN);
    $client->messages->create(
        $para,
        [
            'from' => TWILIO_NUMBER,
            'body' => $mensaje
        ]
    );
}
?>
