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
echo "COMECOU CITIES";
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
$sql = "select value from settings where name='enableGetaroom' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_getaroom = $affiliate_id;
} else {
    $affiliate_id_getaroom = 0;
}
$sql = "select value from settings where name='GetaroomAuth' and affiliate_id=$affiliate_id_getaroom";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomAuth = $row_settings['value'];
}
$sql = "select value from settings where name='GetaroomAPIKey' and affiliate_id=$affiliate_id_getaroom";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomAPIKey = $row_settings['value'];
}
echo $return;
echo $GetaroomAPIKey;
echo $return;
$sql = "select value from settings where name='GetaroomBookServiceURL' and affiliate_id=$affiliate_id_getaroom";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomBookServiceURL = $row_settings['value'];
}
echo $return;
echo $GetaroomBookServiceURL;
echo $return;
$sql = "select value from settings where name='GetaroomAuthorizationToken' and affiliate_id=$affiliate_id_getaroom";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomAuthorizationToken = $row_settings['value'];
}

echo $return;
echo $GetaroomAuth;
echo $return;

$config = new \Zend\Config\Config(include '../config/autoload/global.getaroom.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$count = 0;
$sql = "SELECT id FROM hoteis";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}

$url = 'https://supply.integration2.testaroom.com/';
$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $hotel_id = $row->id;
            echo $return;
            echo $hotel_id;
            echo $return;
            // CONTENT
            //$hotel_id = "2ea3bae7-2cb1-47d4-8a57-d1bf75db24a2";
            $raw = 'hotel/api/properties/' . $hotel_id . '.json?api_key=' . $GetaroomAPIKey . '&auth_token=' . $GetaroomAuth . '';
            $startTime = microtime();
            echo $return;
            echo $url . $raw;
            echo $return;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url . $raw);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $GetaroomAPIKey . ":" . $GetaroomAuth);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            $endTime = microtime();

            /* echo $return;
            echo $response;
            echo $return; */
            $response = json_decode($response, true);
            echo $return;
            echo "PASSOU";
            echo $return;
            if($response === false || $response === null){
                echo $return;
                echo "NOT DECODE";
                echo $return;
            }
        
        
            echo "<xmp>";
            var_dump($response);
            echo "</xmp>";

            $config = new \Zend\Config\Config(include '../config/autoload/global.getaroom.php');
            $config = [
                'driver' => $config->db->driver,
                'database' => $config->db->database,
                'username' => $config->db->username,
                'password' => $config->db->password,
                'hostname' => $config->db->hostname
            ];
            $db = new \Zend\Db\Adapter\Adapter($config);
            
            $id = $response['id'];
            echo $return;
            echo $id;
            echo $return;
            $currency_code = $response['currency_code'];
            $time_zone = $response['time_zone'];
            $star_rating = $response['star_rating'];
            $phone_number = $response['phone_number'];
            $content_updated_at = $response['content_updated_at'];
            $name = $response['name'];
            $enname = $name['en'];
            $location_description = $response['location_description'];
            $enld = $location_description['en'];
            $description = $response['description'];
            $endescription = $description['en'];
            
            $address = $response['address'];
            $street_address = $address['street_address'];
            $enst = $street_address['en'];
            $locality = $address['locality'];
            $enlocality = $locality['en'];
            $region = $address['region'];
            $enregion = $region['en'];
            $postal_code = $address['postal_code'];
            $country_code = $address['country_code'];
            $latitude = $address['latitude'];
            $longitude = $address['longitude'];
            
            $brand = $response['brand'];
            $idbrand = $brand['id'];
            $namebrand = $brand['name'];
            $enbrand = $namebrand['en'];
            
            $chain = $response['chain'];
            $idchain = $chain['id'];
            $namechain = $brand['name'];
            $enchain = $namechain['en'];
            
            try {
                $sql = new Sql($db);
              $select = $sql->select();
              $select->from('contents');
              $select->where(array(
              'id' => $id
              ));
              $statement = $sql->prepareStatementForSqlObject($select);
              $result = $statement->execute();
              $result->buffer();
              $customers = array();
              if ($result->valid()) {
              $data = $result->current();
              $id = $data['id'];
              if (strlen($id) > 0) {
              $sql = new Sql($db);
              $data = array(
                'id' => $id,
                'currency_code' => $currency_code,
                'time_zone' => $time_zone,
                'star_rating' => $star_rating,
                'phone_number' => $phone_number,
                'content_updated_at' => $content_updated_at,
                'enname' => $enname,
                'enld' => $enld,
                'endescription' => $endescription,
                'enst' => $enst,
                'enlocality' => $enlocality,
                'enregion' => $enregion,
                'postal_code' => $postal_code,
                'country_code' => $country_code,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'idbrand' => $idbrand,
                'enbrand' => $enbrand,
                'idchain' => $idchain,
                'enchain' => $enchain
              );
              $where['id = ?'] = $id;
              $update = $sql->update('contents', $data, $where);
              $db->getDriver()
              ->getConnection()
              ->disconnect();
              } else {
              $sql = new Sql($db);
              $insert = $sql->insert();
              $insert->into('contents');
              $insert->values(array(
                'id' => $id,
                'currency_code' => $currency_code,
                'time_zone' => $time_zone,
                'star_rating' => $star_rating,
                'phone_number' => $phone_number,
                'content_updated_at' => $content_updated_at,
                'enname' => $enname,
                'enld' => $enld,
                'endescription' => $endescription,
                'enst' => $enst,
                'enlocality' => $enlocality,
                'enregion' => $enregion,
                'postal_code' => $postal_code,
                'country_code' => $country_code,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'idbrand' => $idbrand,
                'enbrand' => $enbrand,
                'idchain' => $idchain,
                'enchain' => $enchain
              ), $insert::VALUES_MERGE);
              $statement = $sql->prepareStatementForSqlObject($insert);
              $results = $statement->execute();
              $db->getDriver()
              ->getConnection()
              ->disconnect();
              }
             } else {
              $sql = new Sql($db);
              $insert = $sql->insert();
              $insert->into('contents');
              $insert->values(array(
                'id' => $id,
                'currency_code' => $currency_code,
                'time_zone' => $time_zone,
                'star_rating' => $star_rating,
                'phone_number' => $phone_number,
                'content_updated_at' => $content_updated_at,
                'enname' => $enname,
                'enld' => $enld,
                'endescription' => $endescription,
                'enst' => $enst,
                'enlocality' => $enlocality,
                'enregion' => $enregion,
                'postal_code' => $postal_code,
                'country_code' => $country_code,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'idbrand' => $idbrand,
                'enbrand' => $enbrand,
                'idchain' => $idchain,
                'enchain' => $enchain
              ), $insert::VALUES_MERGE);
              $statement = $sql->prepareStatementForSqlObject($insert);
              $results = $statement->execute();
              $db->getDriver()
              ->getConnection()
              ->disconnect();
              }
            } catch (\Exception $e) {
                echo $return;
                echo "Error: " . $e;
                echo $return;
            }
              
            echo $return;
            echo "ANTES ";
            echo $return;
            
            $merged_ids = $response['merged_ids'];
            for ($k = 0; $k < count($merged_ids); $k ++) {
                echo $return;
                echo "ENTROU ";
                echo $return;
                $merged_ids = $merged_ids[$k];
                echo $return;
                echo "merged_ids: " . $merged_ids;
                echo $return;
                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('merge');
                    $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'merged_ids' => $merged_ids,
                    'id_content' => $id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error2: " . $e;
                    echo $return;
                }
                

            }
            
            $amenities = $response['amenities'];
            for ($j = 0; $j < count($amenities); $j ++) {
                $idamenities = $amenities[$j]['id'];
                $nameamenities = $amenities[$j]['name'];
                $enamenities = $nameamenities['en'];
                try {
                    $sql = new Sql($db);
                  $select = $sql->select();
                  $select->from('amenities');
                  $select->where(array(
                  'id' => $idamenities
                  ));
                  $statement = $sql->prepareStatementForSqlObject($select);
                  $result = $statement->execute();
                  $result->buffer();
                  $customers = array();
                  if ($result->valid()) {
                  $data = $result->current();
                  $id = $data['idamenities'];
                  if (strlen($id) > 0) {
                  $sql = new Sql($db);
                  $data = array(
                  'id' => $idamenities,
                  'name' => $enamenities,
                  'id_content' => $id
                  );
                  $where['idrt = ?'] = $idrt;
                  $update = $sql->update('amenities', $data, $where);
                  $db->getDriver()
                  ->getConnection()
                  ->disconnect();
                  } else {
                  $sql = new Sql($db);
                  $insert = $sql->insert();
                  $insert->into('amenities');
                  $insert->values(array(
                  'id' => $idamenities,
                  'name' => $enamenities,
                  'id_content' => $id
                  ), $insert::VALUES_MERGE);
                  $statement = $sql->prepareStatementForSqlObject($insert);
                  $results = $statement->execute();
                  $db->getDriver()
                  ->getConnection()
                  ->disconnect();
                  }
                  } else {
                  $sql = new Sql($db);
                  $insert = $sql->insert();
                  $insert->into('amenities');
                  $insert->values(array(
                  'id' => $idamenities,
                  'name' => $enamenities,
                  'id_content' => $id
                  ), $insert::VALUES_MERGE);
                  $statement = $sql->prepareStatementForSqlObject($insert);
                  $results = $statement->execute();
                  $db->getDriver()
                  ->getConnection()
                  ->disconnect();
                  }
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error3: " . $e;
                    echo $return;
                }
                
                  
            }
            
            $room_types = $response['room_types'];
            for ($i = 0; $i < count($room_types); $i ++) {
                $idrt = $room_types[$i]['id'];
                $namert = $room_types[$i]['name'];
                $ennamert = $namert['en'];
                $descriptionrt = $room_types[$i]['description'];
                $endescriptionrt = $descriptionrt['en'];
                
                try {
                    $sql = new Sql($db);
                  $select = $sql->select();
                  $select->from('roomtypes');
                  $select->where(array(
                  'id' => $idrt
                  ));
                  $statement = $sql->prepareStatementForSqlObject($select);
                  $result = $statement->execute();
                  $result->buffer();
                  $customers = array();
                  if ($result->valid()) {
                        $data = $result->current();
                        $id = $data['idrt'];
                        if (strlen($id) > 0) {
                            $sql = new Sql($db);
                            $data = array(
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'idrt' => $idrt,
                                'name' => $ennamert,
                                'description' => $endescriptionrt,
                                'id_content' => $id
                            );
                            $where['idrt = ?'] = $idrt;
                            $update = $sql->update('roomtypes', $data, $where);
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                        } else {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('roomtypes');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'idrt' => $idrt,
                                'name' => $ennamert,
                                'description' => $endescriptionrt,
                                'id_content' => $id
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                        }
                  } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('roomtypes');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'idrt' => $idrt,
                        'name' => $ennamert,
                        'description' => $endescriptionrt,
                        'id_content' => $id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                  }
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error4: " . $e;
                    echo $return;
                }
                
            }
            //$count = $count + 1;
        //}
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>