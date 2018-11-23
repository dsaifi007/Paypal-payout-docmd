<?php

// 1. Autoload the SDK Package. This will include all the files and classes to your autoloader
// Used for composer based installation
require __DIR__ . '/vendor/autoload.php';
// Use below for direct download installation
// require __DIR__  . '/PayPal-PHP-SDK/autoload.php';
// 'AXD-U4dZWNJfo3_fE12UO3b2B3RqpkDszwRXpk0p_nHWzDD0bkHzBFSq9NGGkU0iiTg8YpMqQt4Bk6jh'
// EPBJwgNxJgBMDA_bbiqz2WKJN3JPIYySmZcbdegeUW5CXD4gfrTxb13q1C2m_JkD6YCx37UKYMpmg53H
// After Step 1
$CI = &get_instance();

$apiContext = new \PayPal\Rest\ApiContext(
        new \PayPal\Auth\OAuthTokenCredential(
        $CI->config->item("paypal_client_id"), $CI->config->item("paypal_secret_id")
        )
);
?>