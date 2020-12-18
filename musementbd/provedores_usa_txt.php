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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.musement.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = "Proveedores_USA.csv";
$object = fopen($filename, 'r');
$line = 0;

while ($data = fgetcsv($object, 0, ";")) {
    if ($line > 0) {
        $id = utf8_encode($data[0]);
        $accountype = $data[1];
        $custtype = $data[2];
        $shortcode = $data[3];
        $sortname = utf8_encode($data[4]);
        $name = utf8_encode($data[5]);
        $addr1 = utf8_encode($data[6]);
        $addr2 = utf8_encode($data[7]);
        $addr3 = utf8_encode($data[8]);
        $city = utf8_encode($data[9]);
        $state = utf8_encode($data[10]);
        $zip = utf8_encode($data[11]);
        $country = utf8_encode($data[12]);
        $email = utf8_encode($data[13]);
        $busphone = utf8_encode($data[14]);
        $faxphone = utf8_encode($data[15]);
        $homephone = $data[16];
        $dateopen = $data[17];
        $datechanged = $data[18];
        $tag = $data[19];
        $compct = $data[20];

        $id = str_replace("'", "", $id);
        $accountype = str_replace("'", "", $accountype);
        $custtype = str_replace("'", "", $custtype);
        $shortcode = str_replace("'", "", $shortcode);
        $sortname = str_replace("'", "", $sortname);
        $name = str_replace("'", "", $name);
        $addr1 = str_replace("'", "", $addr1);
        $addr2 = str_replace("'", "", $addr2);
        $addr3 = str_replace("'", "", $addr3);
        $city = str_replace("'", "", $city);
        $state = str_replace("'", "", $state);
        $zip = str_replace("'", "", $zip);
        $country = str_replace("'", "", $country);
        $email = str_replace("'", "", $email);
        $busphone = str_replace("'", "", $busphone);
        $faxphone = str_replace("'", "", $faxphone);
        $homephone = str_replace("'", "", $homephone);
        $dateopen = str_replace("'", "", $dateopen);
        $datechanged = str_replace("'", "", $datechanged);
        $tag = str_replace("'", "", $tag);
        $compct = str_replace("'", "", $compct);
        echo $id . "<br>";

        try {
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('provedores_usa');
            $select->where(array(
                'id' => $id
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $result->buffer();
            $customers = array();
            if ($result->valid()) {
                $data = $result->current();
                $id = (string)$data['id'];
                if ($id != "") {
                    $config = new \Zend\Config\Config(include '../config/autoload/global.musement.php');
                    $config = [
                        'driver' => $config->db->driver,
                        'database' => $config->db->database,
                        'username' => $config->db->username,
                        'password' => $config->db->password,
                        'hostname' => $config->db->hostname
                    ];
                    $dbUpdate = new \Zend\Db\Adapter\Adapter($config);
                        $data = array(
                            'datetime_updated' => time(),
                            'accountype' => $accountype, 
                            'custtype' => $custtype,  
                            'shortcode' => $shortcode, 
                            'sortname' => $sortname, 
                            'name' => $name, 
                            'addr1' => $addr1, 
                            'addr2' => $addr2, 
                            'addr3' => $addr3, 
                            'city' => $city, 
                            'state' => $state, 
                            'zip' => $zip, 
                            'country' => $country, 
                            'email' => $email, 
                            'busphone' => $busphone, 
                            'faxphone' => $faxphone, 
                            'homephone' => $homephone, 
                            'dateopen' => $dateopen, 
                            'datechanged' => $datechanged, 
                            'tag' => $tag, 
                            'compct' => $compct
                            );
  
                    $sql    = new Sql($dbUpdate);
                    $update = $sql->update();
                    $update->table('provedores_usa');
                    $update->set($data);
                    $update->where(array('id' => $id));

                    $statement = $sql->prepareStatementForSqlObject($update);
                    $results = $statement->execute();
                    $dbUpdate->getDriver()
                    ->getConnection()
                    ->disconnect(); 
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('provedores_usa');
                    $insert->values(array(
                        'id' => $id,
                        'datetime_updated' => time(),
                        'accountype' => $accountype, 
                        'custtype' => $custtype,  
                        'shortcode' => $shortcode, 
                        'sortname' => $sortname, 
                        'name' => $name, 
                        'addr1' => $addr1, 
                        'addr2' => $addr2, 
                        'addr3' => $addr3, 
                        'city' => $city, 
                        'state' => $state, 
                        'zip' => $zip, 
                        'country' => $country, 
                        'email' => $email, 
                        'busphone' => $busphone, 
                        'faxphone' => $faxphone, 
                        'homephone' => $homephone, 
                        'dateopen' => $dateopen, 
                        'datechanged' => $datechanged, 
                        'tag' => $tag, 
                        'compct' => $compct
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
                $insert->into('provedores_usa');
                $insert->values(array(
                    'id' => $id,
                    'datetime_updated' => time(),
                    'accountype' => $accountype, 
                    'custtype' => $custtype,  
                    'shortcode' => $shortcode, 
                    'sortname' => $sortname, 
                    'name' => $name, 
                    'addr1' => $addr1, 
                    'addr2' => $addr2, 
                    'addr3' => $addr3, 
                    'city' => $city, 
                    'state' => $state, 
                    'zip' => $zip, 
                    'country' => $country, 
                    'email' => $email, 
                    'busphone' => $busphone, 
                    'faxphone' => $faxphone, 
                    'homephone' => $homephone, 
                    'dateopen' => $dateopen, 
                    'datechanged' => $datechanged, 
                    'tag' => $tag, 
                    'compct' => $compct
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