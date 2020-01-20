<?php 
$checkout_token = "PK1CCK1REHENE2WU";
        
//These are sandbox credentials
$public_key = "R2APT9RTSF8ZUX1H";
$private_key = "HMLk1etOUp8AkoMnZB0cW2QJ54KLanZ6";
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

$id = $response['id'];
$currency = $response['currency'];
$under_dispute = $response['under_dispute'];
$user_id = $response['user_id'];
$platform = $response['platform'];
$refundable = $response['refundable'];
$charge_event_count = $response['charge_event_count'];
$pending = $response['pending'];
$merchant_external_reference = $response['merchant_external_reference'];
$status = $response['status'];
$order_id = $response['order_id'];
$void = $response['void'];
$expires = $response['expires'];
$payable = $response['payable'];
$merchant_id = $response['merchant_id'];
$auth_hold = $response['auth_hold'];
$refunded_amount = $response['refunded_amount'];
$created = $response['created'];
$is_instore = $response['is_instore'];
$amount = $response['amount'];
$is_marqeta_charge = $response['is_marqeta_charge'];
$balance = $response['balance'];
$financing_program = $response['financing_program'];
//details
$details = $response['details'];
if (count($details) > 0) {
    $financing_program_external_name = $details['financing_program_external_name'];
    $financing_program_name = $details['financing_program_name'];
    $checkout_flow_type = $details['checkout_flow_type'];
    $checkout_type = $details['checkout_type'];
    $order_iddetails = $details['order_id'];
    $currencydetails = $details['currency'];
    $shipping_amount = $details['shipping_amount'];
    $tax_amount = $details['tax_amount'];
    $total = $details['total'];
    $loan_type = $details['loan_type'];
    $api_version = $details['api_version'];
    $merchant_external_reference = $details['merchant_external_reference'];
    //merchant
    $merchant = $details['merchant'];
    if (count($merchant) > 0) {
        $public_api_key = $merchant['public_api_key'];
        $user_cancel_url = $merchant['user_cancel_url'];
        $user_confirmation_url = $merchant['user_confirmation_url'];
        $name = $merchant['name'];
        $user_confirmation_url_action = $merchant['user_confirmation_url_action'];
    }
    //billing
    $billing = $details['billing'];
    if (count($billing) > 0) {
        $phone_number = $billing['phone_number'];
        $email = $billing['email'];
        $name = $billing['name'];
        $full = $name['full'];
        $last = $name['last'];
        $first = $name['first'];
        $address = $billing['address'];
        $city = $address['city'];
        $country = $address['country'];
        $zipcode = $address['zipcode'];
        $line1 = $address['line1'];
        $line2 = $address['line2'];
        $state = $address['state'];
    }
    //discounts
    $discounts = $details['discounts'];
    if (count($discounts) > 0) {
        $discountDEF456 = $discounts['discountDEF456'];
        $discount_display_name456 = $discountDEF456['discount_display_name'];
        $discount_amount456 = $discountDEF456['discount_amount'];
        $discountABC123 = $discounts['discountABC123'];
        $discount_display_name123 = $discountABC123['discount_display_name'];
        $discount_amount123 = $discountABC123['discount_amount'];
    }
    //items
    $items = $details['items'];
    if (count($items) > 0) {
        $D45E6F = $items['4D5E6F'];
        $skud = $D45E6F['sku'];
        $item_urld = $D45E6F['item_url'];
        $display_named = $D45E6F['display_name'];
        $unit_priced = $D45E6F['unit_price'];
        $qtyd = $D45E6F['qty'];
        $item_typed = $D45E6F['item_type'];
        $item_image_urld = $D45E6F['item_image_url'];
        $A12B3C = $items['1A2B3C'];
        $skua = $A12B3C['sku'];
        $item_urla = $A12B3C['item_url'];
        $display_namea = $A12B3C['display_name'];
        $unit_pricea = $A12B3C['unit_price'];
        $qtya = $A12B3C['qty'];
        $item_typea = $A12B3C['item_type'];
        $item_image_urla = $A12B3C['item_image_url'];
    }
    //metadata
    $metadata = $details['metadata'];
    if (count($metadata) > 0) {
        $shipping_type = $metadata['shipping_type'];
        $checkout_channel_type = $metadata['checkout_channel_type'];
    }
    //shipping
    $shipping = $details['shipping'];
    if (count($shipping) > 0) {
        $phone_number = $shipping['phone_number'];
        $email = $shipping['email'];
        $name = $shipping['name'];
        $full = $name['full'];
        $last = $name['last'];
        $first = $name['first'];
        $address = $shipping['address'];
        $city = $address['city'];
        $country = $address['country'];
        $zipcode = $address['zipcode'];
        $line1 = $address['line1'];
        $line2 = $address['line2'];
        $state = $address['state'];
    }
    //meta
    $meta = $details['meta'];
    if (count($meta) > 0) {
        $release = $meta['release'];
        $user_timezone = $meta['user_timezone'];
        $__affirm_tracking_uuid = $meta['__affirm_tracking_uuid'];
    }
    //mfp_rule_input_data
    $mfp_rule_input_data = $details['mfp_rule_input_data'];
    if (count($mfp_rule_input_data) > 0) {
        $total = $mfp_rule_input_data['total'];
        $items = $mfp_rule_input_data['items'];
        if (count($items) > 0) {
            $D45E6F = $items['4D5E6F'];
            $skud = $D45E6F['sku'];
            $item_urld = $D45E6F['item_url'];
            $display_named = $D45E6F['display_name'];
            $unit_priced = $D45E6F['unit_price'];
            $qtyd = $D45E6F['qty'];
            $item_typed = $D45E6F['item_type'];
            $item_image_urld = $D45E6F['item_image_url'];
            $A12B3C = $items['1A2B3C'];
            $skua = $A12B3C['sku'];
            $item_urla = $A12B3C['item_url'];
            $display_namea = $A12B3C['display_name'];
            $unit_pricea = $A12B3C['unit_price'];
            $qtya = $A12B3C['qty'];
            $item_typea = $A12B3C['item_type'];
            $item_image_urla = $A12B3C['item_image_url'];
        }

        $metadata = $mfp_rule_input_data['metadata'];
        if (count($metadata) > 0) {
            $shipping_type = $metadata['shipping_type'];
            $checkout_channel_type = $metadata['checkout_channel_type'];
        }
    }
    //config
    $config = $details['config'];
    if (count($config) > 0) {
        $financial_product_key = $config['financial_product_key'];
        $user_confirmation_url_action = $config['user_confirmation_url_action'];
    }
}
//events
$events = $response['events'];
if (count($events) > 0) {
    for ($i=0; $i < count($events); $i++) { 
        $id = $events[$i]['id'];
        $created = $events[$i]['created'];
        $currency = $events[$i]['currency'];
        $amount = $events[$i]['amount'];
        $type = $events[$i]['type'];
        $transaction_id = $events[$i]['transaction_id'];
    }
}

?>