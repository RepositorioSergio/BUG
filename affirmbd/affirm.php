<?php

require "creds.php";

$action = $_REQUEST["action"];

$affirm = new Affirm();

switch ($action) {
	case 'auth':
		$affirm->auth();
		break;
	case 'void':
		$affirm->void();
		break;
	case 'capture':
		$affirm->capture();
		break;
	case 'refund':
		$affirm->refund();
		break;
	case 'update':
		$affirm->update();
		break;
	case 'read':
		$affirm->read();
		break;
	default:
		echo "Hi!";
		break;
}

class Affirm {

	public function auth() {

		$checkout_token = $_REQUEST["checkout_token"];

		$endpoint = "charges/";
		$method = "POST";
		$data = array("checkout_token" => $checkout_token);
		$env = $_REQUEST["env"];

		$this->request($endpoint, $method, $data, $env);
	
	}

	public function void() {

		$charge_id = $_REQUEST["charge_id"];
		
		$endpoint = "charges/" . $charge_id . "/void";
		$method = "POST";
		$data = "";
		$env = $_REQUEST["env"];

		$this->request($endpoint, $method, $data, $env);
	
	}

	public function capture() {

		$charge_id = $_REQUEST["charge_id"];
		
		$endpoint = "charges/" . $charge_id . "/capture";
		$method = "POST";
		$data = "";
		$env = $_REQUEST["env"];

		$this->request($endpoint, $method, $data, $env);
	}

	public function refund() {

		$charge_id = $_REQUEST["charge_id"];
		$amount = $_REQUEST["amount"];
		
		$endpoint = "charges/" . $charge_id . "/refund";
		$method = "POST";
		$data = array('amount' => $amount);
		$env = $_REQUEST["env"];

		$this->request($endpoint, $method, $data, $env);	
	}

	public function read() {
		
		$charge_id = $_REQUEST["charge_id"];
		if ($charge_id){
			$endpoint = "charges/" . $charge_id;
		}
		else {
			$endpoint = "charges/?limit=2";
		}

		$method = "GET";
		$data = "";
		$env = $_REQUEST["env"];

		$this->request($endpoint, $method, $data, $env);
	}


	public function update() {

		$carrier = $_REQUEST["carrier"];
		$tracking = $_REQUEST["tracking"];
		$order_id = $_REQUEST["order_id"];
		$charge_id = $_REQUEST["charge_id"];
		
		$endpoint = "charges/" . $charge_id . "/update";
		$method = "POST";
		$data = array('shipping_carrier' => $carrier,'shipping_confirmation' => $tracking,'order_id' => $order_id);
		$env = $_REQUEST["env"];

		$this->request($endpoint, $method, $data, $env);
	}

	public function request($a, $b, $c, $d) {

		global $sandbox_public_key, $sandbox_private_key, $live_public_key, $live_private_key;

		$sandbox_base_url = "https://sandbox.affirm.com/api/v2/";
		$live_base_url = "https://api.affirm.com/api/v2/";

		if ($d === "live") {
			$public_key = $live_public_key;
			$private_key = $live_private_key;
			$base_url = $live_base_url;
		}
		else {
			$public_key = $sandbox_public_key;
			$private_key = $sandbox_private_key;
			$base_url = $sandbox_base_url;
		}

		$url = $base_url . $a;
		$json = json_encode($c);
		$header = array('Content-Type: application/json','Content-Length: ' . strlen($json));
		$keypair = $public_key . ":" . $private_key;

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $b);
		curl_setopt($curl, CURLOPT_USERPWD, $keypair);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		$response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);
		http_response_code($status); 
		echo $response;
	}
}

?>
