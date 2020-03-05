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
// Start
$affiliate_id = 0;
$branch_filter = "";

$config = new \Zend\Config\Config(include '../config/autoload/global.travelplan.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$token = "eyJhbGciOiJIUzI1NiIsInppcCI6IkdaSVAifQ.H4sIAAAAAAAAAKtWyiwuVrJSSs4vLknMTSxS0lHKTCxRsjI0tTA2NDE2MzLTUUqtKIAIGBkbGIAESotTi_ISc1NB-nIyk7MT81KS8vOzlWoB-5-piU4AAAA.VzDTmiRM59sXnm7y3mgpLLmXcjCfe27h4L9f40aOy7k";
$url = 'http://test-api-zneith.zdev.tech/pricing/rules';

$raw = '{
    "airRulesRQs": [
      {
        "ruleReqInfo": {
          "departureDate": "2020-06-10T00:15:00.000-0500",
          "marketingAirline": [
            {
              "code": "LA"
            }
          ],
          "departureAirport": {
            "locationCode": "LIM"
          },
          "arrivalAirport": {
            "locationCode": "MIA"
          },
          "fareReference": [
            {
              "resBookDesigCode": "O"
            }
          ],
          "routingNumber": 2466,
          "ruleNumber": "1",
          "ruleInfo": {},
          "fareInfo": [
            {
              "fareBasisCode": "OLESND0F",
              "fareType": "PUBLISHED"
            }
          ]
        },
        "primaryLangID": "es"
      },
      {
        "ruleReqInfo": {
          "departureDate": "2020-06-20T15:42:00.000-0500",
          "marketingAirline": [
            {
              "code": "LA"
            }
          ],
          "departureAirport": {
            "locationCode": "MIA"
          },
          "arrivalAirport": {
            "locationCode": "LIM"
          },
          "fareReference": [
            {
              "resBookDesigCode": "O"
            }
          ],
          "routingNumber": 2463,
          "ruleNumber": "2",
          "ruleInfo": {},
          "fareInfo": [
            {
              "fareBasisCode": "OLESND8F",
              "fareType": "PUBLISHED"
            }
          ]
        },
        "primaryLangID": "es"
      }
    ],
    "origin": "LIM",
    "destination": "MIA",
    "fareBreakdowns": {
      "ptcfareBreakdown": [
        {
          "passengerTypeQuantity": {
            "code": "ADT",
            "quantity": 1
          }
        }
      ]
    },
    "sourceType": "PUBLISHED",
    "pos": {
      "source": [
        {
          "airlineVendorID": "LA",
          "pseudoCityCode": "FLL6C2102",
          "erspuserID": "100"
        }
      ]
    }
  }';

/* $client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token,
    'Content-Length: ' . strlen($raw)
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
} */

$headers = array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token,
    'Content-Length: ' . strlen($raw)
); 
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo '<xmp>';
var_dump($response);
echo '</xmp>';
$config = new \Zend\Config\Config(include '../config/autoload/global.travelplan.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$id = $response['id'];


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>