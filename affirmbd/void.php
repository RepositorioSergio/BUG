<?php 
$checkout_token = "T1N6A349WYQ07OOY";
        
//These are sandbox credentials
$public_key = "R2APT9RTSF8ZUX1H";
$private_key = "HMLk1etOUp8AkoMnZB0cW2QJ54KLanZ6";
//This is the sandbox API URL
$url = "https://sandbox.affirm.com/api/v2/charges/G8QC-ZIAI/void";
$order_id = "123456789";
$data = array("order_id" => $order_id);
$json = json_encode($data);
$header = array('Content-Type: application/json');

$keypair = $public_key . ":" . $private_key;

$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);                                                                     
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_USERPWD, $keypair);
//curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
//curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

$response = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

http_response_code($status);
echo $response;

$id = $response['id'];
$created = $response['created'];
$order_id = $response['order_id'];
$type = $response['type'];
$transaction_id = $response['transaction_id'];

?>