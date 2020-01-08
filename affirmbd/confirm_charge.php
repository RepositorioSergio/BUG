<?php 
$checkout_token = $_REQUEST['checkout_token'];
        
//These are sandbox credentials
$public_key = "ARQBLCL7NAMBTZ7F";
$private_key = "RkHBmVSP5ayC2rCUujwhArpGWPxpuTtv";
//This is the sandbox API URL
$url = "https://sandbox.affirm.com/api/v2/charges/";

$data = array("checkout_token" => $checkout_token);
$json = json_encode($data);
$header = array('Content-Type: application/json','Content-Length: ' . strlen($json));

$keypair = $public_key . ":" . $private_key;

$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);                                                                     
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_USERPWD, $keypair);
curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

$response = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

http_response_code($status);
echo $response;
?>