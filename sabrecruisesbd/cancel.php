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
echo "COMECOU CANCEL<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
$access_token = "T1RLAQJZi0nR/Q9+880Jq2UK1v76ggsAJRCmheZlZJqS6TNvR47IjuUQAADAigAfu4oZE3tYVejeO+/R7aqJUVjlRus3tvBKeFxOiHu/YvNNMlm/10mWVUhLrFowve8+CnRmXV7zcSokvmmlyqd//2OLVlD84CUnn5Sqit/TGgKDOaY0mnv/aM86UPnQ0O5BaQwuiZG6qh6PDBgXi7zcGfN8xEfeXlOex3a2a8o/l+4TgB2RSmQW0/gCRU8+eMHT1KfObFk94Bngt6/b3PqoCU9L2u5AS/N0kXsbp2yRhyvNRqss8AgMfxwoZqSG";

$url = 'https://api-crt.cert.havail.sabre.com/v1/cruise/orders/cancel';

$raw = '{
    "agencyPOS": {
        "pcc": "IA8H",
        "branchPcc": "IA8H",
        "branchPhoneNum": "999999999",
        "currencyCode": "USD"
    },
    "reservationInfo": {
      "vendorCode": "RC",
      "reservationId": "H8737TWI",
      "agencyGroupId": "43562",
      "cancelReason": "Medical reason"
    }
  }';

echo '<xmp>';
var_dump($raw);
echo '</xmp>';

$headers = array(
    "Accept: application/json",
    "Content-Type: application/json",
    "Accept-Encoding: gzip",
    'Authorization: Bearer ' . $access_token,
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_USERPWD, $ipcc . ":" . $password);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$response = json_decode($response, true);

$cancellationInfo = $response['cancellationInfo'];
$totalPaymentReceived = $cancellationInfo['totalPaymentReceived'];
$bookingPenalty = $cancellationInfo['bookingPenalty'];
$commissionDue = $cancellationInfo['commissionDue'];
$refundAmount = $cancellationInfo['refundAmount'];
$cancellationRefNum = $cancellationInfo['cancellationRefNum'];
$groupCancellationInfos = $cancellationInfo['groupCancellationInfos'];
if (count($groupCancellationInfos) > 0) {
    for ($i=0; $i < count($groupCancellationInfos); $i++) { 
        $bookingPenalty = $groupCancellationInfos[$i]['bookingPenalty'];
        $noPenaltyDate = $groupCancellationInfos[$i]['noPenaltyDate'];
        $cancellationRefNum = $groupCancellationInfos[$i]['cancellationRefNum'];
    }
}
$cancellationReason = $cancellationInfo['cancellationReason'];
if (count($cancellationReason) > 0) {
    $reason = "";
    for ($j=0; $j < count($cancellationReason); $j++) { 
        $reason = $cancellationReason[$j];
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
