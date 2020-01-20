<?php 
$checkout_token = "T1N6A349WYQ07OOY";
        
//These are sandbox credentials
$public_key = "R2APT9RTSF8ZUX1H";
$private_key = "HMLk1etOUp8AkoMnZB0cW2QJ54KLanZ6";
//This is the sandbox API URL
$url = "https://sandbox.affirm.com/api/v2/checkout/PK1CCK1REHENE2WU";

$data = array("checkout_token" => $checkout_token);
$json = json_encode($data);
$header = array('Authorization: Basic','Content-Type: application/json','Content-Length: 0');

$keypair = $public_key . ":" . $private_key;

$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);                                                                     
//curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_USERPWD, $keypair);
//curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

$response = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

http_response_code($status);
echo $response;
?>