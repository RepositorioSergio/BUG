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
echo "COMECOU HOTELAVAIL<br/>";
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
$affiliate_id_palace = 0;
$branch_filter = "";


$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = "https://api.travelgatex.com/";

$raw = '{"query":"{\n hotelX {\n  hotels(criteria: {\n  access: \"422\",\n  maxSize: 500,\n  hotelCodes: [\"1\"]  },\n  relay: {}) {\n  token\n  edges {\n  node {\n  code\n  hotelData {\n code\n  hotelCode\n  hotelCodeSupplier\n  hotelName\n  categoryCode\n  rank\n  cardTypes\n  contact {\n  email\n  telephone\n  fax\n  web\n  }\n  property {\n  name\n  code\n  }\n chainCode\n  exclusiveDeal\n  rank\n cardTypes\n  location {\n  address\n  city\n  zipCode\n  country\n  coordinates {\n  latitude\n  longitude\n  }\n  closestDestination {\n  code\n  available\n  destinationLeaf\n parent\n  type\n  texts {\n  text\n  language\n  }\n  }\n  }\n  amenities {\n  code\n  type\n  texts {\n  text\n  language\n  }\n  }\n  medias {\n  code\n  order\n  type\n  updatedAt\n  url\n  texts {\n  text\n  language\n  }\n  }\n descriptions {\n  type\n  texts {\n  text\n language\n  }\n  }\n  }\n  error {\n  code\n  type\n description\n  }\n  createdAt\n  updatedAt\n  }\n  }\n  }\n  }\n  }"}';

 $headers = array(
    'Authorization: Apikey 64780338-49c8-4439-7c7d-d03c2033b145',
	'Accept-Encoding: gzip, deflate, br',
	'Content-Type: application/json',
	'Accept: application/json',
	'Connection: keep-alive',
	'DNT: 1',
	'Origin: https://api.travelgatex.com'
); 

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_ENCODING , "gzip");
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);

echo $response;

$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

$response = json_decode($response, true);
echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>'; 

$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$cardType = "";


$data = $response['data'];
$hotelX = $data['hotelX'];
$hotels = $hotelX['hotels'];
//token
$token = $hotels['token'];
//edges
$edges = $hotels['edges'];
for ($i=0; $i < count($edges); $i++) { 
    $node = $edges[$i]['node'];
    $code = $node['code'];
    $createdAt = $node['createdAt'];
    $updatedAt = $node['updatedAt'];

    $hotelData = $node['hotelData'];
    $hotelDatacode = $hotelData['code'];
    $hotelCode = $hotelData['hotelCode'];
    $hotelCodeSupplier = $hotelData['hotelCodeSupplier'];
    $hotelName = $hotelData['hotelName'];
    $categoryCode = $hotelData['categoryCode'];
    $chainCode = $hotelData['chainCode'];
    $exclusiveDeal = $hotelData['exclusiveDeal'];
    $amenities = $hotelData['amenities'];
    $rank = $hotelData['rank'];

    $contact = $hotelData['contact'];
    $email = $contact['email'];
    $telephone = $contact['telephone'];
    $fax = $contact['fax'];
    $web = $contact['web'];

    $property = $hotelData['property'];
    $name = $property['name'];
    $code = $property['code'];

    $location = $hotelData['location'];
    $address = $location['address'];
    $city = $location['city'];
    $zipCode = $location['zipCode'];
    $country = $location['country'];

    $coordinates = $location['coordinates'];
    $latitude = $coordinates['latitude'];
    $longitude = $coordinates['longitude'];

    $closestDestination = $location['closestDestination'];
    $closestDestinationcode = $closestDestination['code'];
    $available = $closestDestination['available'];
    $destinationLeaf = $closestDestination['destinationLeaf'];
    $parent = $closestDestination['parent'];
    $type = $closestDestination['type'];
    $texts = $closestDestination['texts'];
    for ($j=0; $j < count($texts); $j++) { 
        $text = $texts[$j]['text'];
        $language = $texts[$j]['language'];
    }

    $medias = $hotelData['medias'];
    for ($k=0; $k < count($medias); $k++) { 
        $code = $medias[$k]['code'];
        $order = $medias[$k]['order'];
        $type = $medias[$k]['type'];
        $updatedAt = $medias[$k]['updatedAt'];
        $url = $medias[$k]['url'];

        $texts = $medias[$k]['texts'];
        for ($kAux=0; $kAux < count($texts); $kAux++) { 
            $text = $texts[$kAux]['text'];
            $language = $texts[$kAux]['language'];
        }
    }

    $descriptions = $hotelData['descriptions'];
    for ($x=0; $x < count($descriptions); $x++) { 
        $type = $descriptions[$x]['type'];

        $texts = $descriptions[$k]['texts'];
        for ($kAux2=0; $kAux2 < count($texts); $kAux2++) { 
            $text = $texts[$kAux2]['text'];
            $language = $texts[$kAux2]['language'];
        }
    }

    $cardTypes = $hotelData['cardTypes'];
    for ($z=0; $z < count($cardTypes); $z++) { 
        $cardType = $cardTypes[$z];
    }

}



// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>