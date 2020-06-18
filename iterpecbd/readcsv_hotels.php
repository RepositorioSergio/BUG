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
echo "COMECOU READ CSV<br/>";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.iterpec.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = "data/cangooroo_data_cache_hotels_v2.csv";
$object = fopen($filename, 'r');
$line = 0;

while ($data = fgetcsv($object, 0, "|")) {
    if ($line > 0) {
        $id = $data[0];
        $cityid = $data[1];
        $name = $data[2];
        $hotelchain = $data[3];
        $address = $data[4];
        $phone = $data[5];
        $stars = $data[6];
        $zipcode = $data[7];
        $website = $data[8];
        $urlthumb = $data[9];
        $fax = $data[10];
        $date = $data[11];

        try {
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('hotels');
            $select->where(array(
                'id' => $id
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $result->buffer();
            $customers = array();
            if ($result->valid()) {
                $data = $result->current();
                $id = (int)$data['id'];
                if ($id > 0) {
                    $sql = new Sql($db);
                    $data = array(
                        'datetime_created' => time(),
                        'datetime_updated' => 1,
                        'cityid' => $cityid,
                        'name' => $name,
                        'hotelchain' => $hotelchain,
                        'address' => $address,
                        'telephone' => $phone,
                        'stars' => $stars,
                        'zipcode' => $zipcode,
                        'website' => $website,
                        'urlthumb' => $urlthumb,
                        'fax' => $fax,
                        'date' => $date 
                    );
                    $where['id = ?'] = $id;
                    $update = $sql->update('hotels', $data, $where);
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('hotels');
                    $insert->values(array(
                        'id' => $id,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'cityid' => $cityid,
                        'name' => $name,
                        'hotelchain' => $hotelchain,
                        'address' => $address,
                        'telephone' => $phone,
                        'stars' => $stars,
                        'zipcode' => $zipcode,
                        'website' => $website,
                        'urlthumb' => $urlthumb,
                        'fax' => $fax,
                        'date' => $date  
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
                $insert->into('hotels');
                $insert->values(array(
                    'id' => $id,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'cityid' => $cityid,
                    'name' => $name,
                    'hotelchain' => $hotelchain,
                    'address' => $address,
                    'telephone' => $phone,
                    'stars' => $stars,
                    'zipcode' => $zipcode,
                    'website' => $website,
                    'urlthumb' => $urlthumb,
                    'fax' => $fax,
                    'date' => $date 
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
    $line = $line + 1;
}
fclose($filename);

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>