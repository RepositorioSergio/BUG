<?php
require '../vendor/autoload.php';
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Metadata;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Config;
use Zend\Log\Logger;
use Zend\Log\Writer;
echo "COMECOU ";
if (! $_SERVER['DOCUMENT_ROOT']) {
    // On Command Line
    $return = "\r\n";
} else {
    // HTTP Browser
    $return = "<br>";
}
$config = new \Zend\Config\Config(include '../config/autoload/global.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

    
$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://api-sandbox.rezserver.com/api/car/getBookRequest';

$raw = '{
	"limit": 10,
	"send_to_email": 10021316476,
	"sid": "5797e7b2514cad9773a1195da7439c1a",
	"requested_currency": "CAD",
	"cust_first_name": "James",
	"cust_last_name": "Bond",
	"cust_email": "shaken@notStirred.com",
	"cust_phone": 2041354848,
	"cust_country_code": "US",
	"cust_city": "New York City",
	"cust_state_code": "NY",
	"cust_street": "23 Test street",
	"cust_post_code": 12345,
	"cust_dob_mo": 7,
	"cust_dob_dy": 11,
	"cust_dob_yr": 1975,
	"cc_type": "VISA",
	"cc_number": 4012000033330026,
	"cc_mo": 7,
	"cc_yr": 2022,
	"cc_code": 456,
	"fulfillment_method": "EMAIL",
	"payment_method": "CC",
	"insured_type": "PRIMARY",
	"test_mode": 0,
	"ppn_bundle": "LV2--_eJwBUAev_pIJczd6f4C473NydwZHvE_fWGO2JywbMy8gQtpxyAXzMVf6zcJpbWd1Kjt45IsED4QEb_pBjXREdMmTKNFRDt2BuNM0g8_p3PEP5aYkfi60lWymKrZm88V_p86ZPWdKpCWGqR_plakuvxQWLhba55cl3E6dEKh29baIv7ZmQeaJ6xfGd3ZWrSumbzkPvHMnjbiNz9fC91ZYZRXjhgDTzeHRTfkNe3EPzx2SUGs6z6h_fJe4fSrNcV3tjAqJ0do5slE3XlT3pvVElskleVJP2Fo4CS5XPlhLVGpU54iCndZGfOJFhYaEtdlSfAdKbx3cw0u8oSbnklsWWJUic0NBbWLB0hQZvTl_fIhw6_f_fH7GOprdJCO7qFVabOhT6YWs2iyvk3Zrmn00DEmXKYiE0xPr5TwzEv5sJbFJB7nzuQ5kwl66Ll_pJiP_pyPVG5gAMlrgg4L_fkgcSQQ2_f_fyAJQzbwvSkLwTmJI7L34IxuHHIzgWb9gcC7R_pam0QJdDaMViYtgtgyAwW8S0oYGFknNdxrR2d451k06FINtKWxOfeiiARBmJXfnGrtqkQilV4ncSkOsoOxl2pTYQYovXcalLIlxeuk_fN73iTERQ1jSYGlsD1NOg1gpGBv2THcy4GyCil2_fzuJKW1ERlOurKHExlN4agFCk1uX6OkR26Fadv_pyMBI50_pWPSEzKh2qazWh54uGXGmQMKRx9IBeO9mdzagtuBI6GtoyRsCyD0jvqzgwN9Yq_fmlxUfS3pCPN6lRD3XABjNplWtkFnpyV7lzn54g1bSX5uX2E1T_fhAneiUV8Hi7oGGYUmD6JgbCdGuvytOZpxj8dUo0uU_fnaPtr7b5mnrqQjmZ6fd6STYQ25R6qcAu71yjMesYa8JcBEKR41lRBqTsvbcWPfBRvKLfoWy8dDw9jNaDx2mqydIL47KJERq9cmVbOLQmOhpwRTIUg21KTfBxwxt3nJHo_peVoWEoWHwFlS3C_fJfNONoBL7gl9yvwIYoH0joIt6Hq76dd9NbF4shdP38Ok2619akc6Tum_pIS5bD5zWP2hmMgy_pLPCdI0vD5iqWR6xoPdmHRRR04h1W7XBYFpS3NEy4iA3L_fyNGQBS19D7Rub1D94QEVQbiwL5Jyugk8ocNEVxNtXmxTYiXcdX6jsErpxg9MVdulCr00iuXky82b5MAiGkIUludE3yAv_f_fMOM2LjNA2m5lOY7zLsPBwlJMDF19JjY_fBk2j7tYVla2CRdsix_flKWFiP4H82EtN8SSqUiYGV4rEmBxqLPDd41vfn_fJUoTEF6N5XyQJaAnUJrGeMGyad1Hwl0qc0cffGkCHUeKr_fy53AJPNuauLqCm7F4Yoofmf71qW15Hkimfzl2QExQ_fxBE_fmF95MOQuKeovkseTdovF1bEKsKoP6rhT7ZW5nu5_pKEmvowLimgLcWT_pNKJANkaFfmMSF2TqlfHgBEmd6o3o3qa_p_frLWQhGk2hdyEmZFthkVX_fE_f0dZ5t7aYf4_ppSIbTg4rLP9brslCu1ZmhXsfzm3OG7QY3qY1fYQ_fqV8ClzN7k75dcJeNzqTie2rJzdS4JpcUACbfokdLhlLhdhR5RAls27P0DvYUpUPT_fhYfl0XncxS6B2_fre2gUbQQNGVAvnSdSzJJFi5EQ3CaHGhDVMXAkUn11mimD_prZfCvG9hXfJtMMdORXtp_p8UeTfSnbx3hWTrjGk6J_pPWeC1ct3NpGSB8uxDhAVd_fzYPveOr85xwCsbcPYKbkKzWB9XnayDSbdE3JfhJg6CqijGBG3_f_fnZXOtARusDahKSOS0jTc5QQ9VIQUJ2gGQmWfsRt7_flwlNpSkcuvmK8jwEJm88LSN10GZZK5Wpttol_fyhn5Fx4oBz6Vr6QqT4M5_f_fcl59V8JftrUIyTkKfKoDYcmszZI6Gd6c7cQbrTJnjxj2jeXcwWzv4UmPOWbNZcPxj40SRWpK8SvKs1cE_f3oz5hK8PEQbRkbrZz0OFh8PmJq_fIdthd1_fvxX5xsalpIwravURkdMJY1wwcSLpEZHgeFk4a6nqGqLPcRYhem8s8L7guNfcl0ONyR_pvgdxpPb5DMiWClSEMaOcHU9JFm1I0fiZLhOQwwVNuvmdD_f8h2UNNGm51fRrnA_pFY4V2qnTRjda1E17CfEn3s5YOb_fvwHzug4YipirCdXkAzUzxB7CY42IRzu5v3rwR8zbTnJ9M3EKit1jViGaAyLv1ovBXB1cPOaRxCKIOESJO9J9ci_p7B7jbKDJnlzQJ7_ps2SUy3Nw_foCKdEGzIZ6_pJ_fvRwjrN3_plz1fahBT8nvTUEFBRXqMZDyaGPhORO1x4qkvVE_pp63YGnmeB_p0NcvBQ_pwVeDEHyyOjIw3cqRkDe1BauGnPl0_pLyM2l5qQMU78LzYXi85j_ppQo67AqnF9VsGV1oVGgxsp9rpBPeJyhbbe_pQ3PWPXn2yF2m12hveIbS5_fzFBHnRZ9erV_pZIGmWtR8AU0rkDtFqX4VV95XCVO_psmtvmds5xaD_fbooqeCf_fxzcbcXNqa4",
	"airline_code": "AA",
	"flight_number": 2345,
	"cardholder_first_name": "M",
	"cardholder_last_name": "Q",
	"vehicle_option_code": [
		"SKI"
	],
	"vehicle_spec_quantity": [
		1
	],
	"cdw_selected": 0,
	"driver_first_name": "Optimus",
	"driver_last_name": "Prime"
}';
$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Length' => strlen($raw),
    'Content-Type' => 'application/json;charset=utf-8'
));
$client->setUri($url);
$client->setMethod('POST');
$client->setRawBody($raw);
$response = $client->send();
if ($response->isSuccess()) {
    $response = $response->getBody();
} else {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($client->getUri());
    $logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
    echo $return;
    echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
    echo $return;
    die();
}

echo $return;
echo $response;
echo $return; 
$response = json_decode($response, true);


$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
    
$getCarBookRequest = $response['getCarBookRequest'];
$results = $getCarBookRequest['results'];
$status = $results['status'];
$status_code = $results['status_code'];
$inventory = $results['inventory'];
$booking_status = $results['booking_status'];
$statusCode = $results['statusCode'];
$reasonCode = $results['reasonCode'];
$booking_id = $results['booking_id'];
$offerToken = $results['offerToken'];
$email = $results['email'];
$est_commission = $results['est_commission'];
$baseline_est_commission = $results['baseline_est_commission'];
$ti_est_commission = $results['ti_est_commission'];
$ti_baseline_est_commission = $results['ti_baseline_est_commission'];
$combined_est_commission = $results['combined_est_commission'];
$baseline_combined_est_commission = $results['baseline_combined_est_commission'];
$cdw = $results['cdw'];
$selected = $cdw['selected'];
$status_code = $cdw['status_code'];
$status = $cdw['status'];
$confirmation_id = $cdw['confirmation_id'];



// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>