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
echo "COMECOU MEALS";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.hoteldo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$url = 'http://xml.e-tsw.com/AffiliateService/V1.0/AffiliateService.svc/restful/GetMeals?a=DIVISAXML&l=esp';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo $return;
echo $error;
echo $return;

echo "<xmp>";
var_dump($response);
echo "</xmp>";

$config = new \Zend\Config\Config(include '../config/autoload/global.hoteldo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Meals = $inputDoc->getElementsByTagName("Meals");
if ($Meals->length > 0) {
    $Meal = $Meals->item(0)->getElementsByTagName("Meal");
    if ($Meal->length > 0) {
        for ($i=0; $i < $Meal->length; $i++) { 
            $Id = $Meal->item($i)->getElementsByTagName('Id');
            if ($Id->length > 0) {
                $Id = $Id->item(0)->nodeValue;
            } else {
                $Id = "";
            }
            $Name = $Meal->item($i)->getElementsByTagName('Name');
            if ($Name->length > 0) {
                $Name = $Name->item(0)->nodeValue;
            } else {
                $Name = "";
            }
    
            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('meals');
                $select->where(array(
                    'id' => $Id
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $id = (string)$data['id'];
                    if ($id != "") {
                        $sql = new Sql($db);
                        $data = array(
                            'id' => $Id,
                            'datetime_created' => time(),
                            'datetime_updated' => 1,
                            'name' => $Name 
                        );
                        $where['id = ?'] = $Id;
                        $update = $sql->update('meals', $data, $where);
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('meals');
                        $insert->values(array(
                            'id' => $Id,
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'name' => $Name 
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
                    $insert->into('meals');
                    $insert->values(array(
                        'id' => $Id,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'name' => $Name 
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                }
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO: ". $e;
                echo $return;
            }
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>
