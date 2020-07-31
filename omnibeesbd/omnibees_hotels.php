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
    
function readCSV(string $filename){
    $config = new \Zend\Config\Config(include '../config/autoload/global.globalia.php');
    $config = [
        'driver' => $config->db->driver,
        'database' => $config->db->database,
        'username' => $config->db->username,
        'password' => $config->db->password,
        'hostname' => $config->db->hostname
    ];
    $db = new \Zend\Db\Adapter\Adapter($config);

    $object = fopen($filename, 'r');
    $line = 0;

    while ($data = fgetcsv($object, 0, ";")) {
            $hotelid = $data[0];
            $name = $data[1];
            $rede = $data[2];
            $group = $data[3];
            $gestor = $data[4];
            $city = $data[5];
            $uf = $data[6];
            $country = $data[7];
            $cadastro = $data[8];
            $countrycode = $data[9];
            $stateprovcode = $data[10];
            $citycode = $data[11];
            $zonecode = $data[12];
            $latitude = $data[13];
            $longitude = $data[14];
            $razao_social = $data[15];
            $cnpj = $data[16];
            $address = $data[17];
            $complement = $data[18];
            $cep = $data[19];
            $contact = $data[20];
            $email = $data[21];
            $phone = $data[22];
            $stars = $data[23];
            $checkin = $data[24];
            $checkout = $data[25];
            $hotelstatus = $data[26];
            $taxes = $data[27];
            $chd = $data[28];

            $phone = utf8_encode($phone);
            $name = utf8_encode($name);
            $rede = utf8_encode($rede);
            $group = utf8_encode($group);
            $gestor = utf8_encode($gestor);
            $city = utf8_encode($city);
            $country = utf8_encode($country);
            $uf = utf8_encode($uf);
            $cep = utf8_encode($cep);
            $complement = utf8_encode($complement);
            $hotelstatus = utf8_encode($hotelstatus);
            $contact = utf8_encode($contact);
            $address = utf8_encode($address);
            $cnpj = utf8_encode($cnpj);
            $razao_social = utf8_encode($razao_social);
            $chd = utf8_encode($chd);

            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('omnibees_hotels');
                $select->where(array(
                    'id' => $hotelid
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $id = (string)$data['id'];
                    if ($id != "") {
                        $config = new \Zend\Config\Config(include '../config/autoload/global.globalia.php');
                        $config = [
                            'driver' => $config->db->driver,
                            'database' => $config->db->database,
                            'username' => $config->db->username,
                            'password' => $config->db->password,
                            'hostname' => $config->db->hostname
                        ];
                        $dbUpdate = new \Zend\Db\Adapter\Adapter($config);

                        $data = array(
                            'name' => $name,  
                            'rede' => $rede,
                            'group' => $group, 
                            'gestor' => $gestor,
                            'city' => $city, 
                            'uf' => $uf,
                            'country' => $country, 
                            'cadastro' => $cadastro,
                            'countrycode' => $countrycode, 
                            'stateprovcode' => $stateprovcode,
                            'citycode' => $citycode, 
                            'zonecode' => $zonecode,
                            'latitude' => $latitude, 
                            'longitude' => $longitude,
                            'razao_social' => $razao_social, 
                            'cnpj' => $cnpj,
                            'address' => $address, 
                            'complement' => $complement,
                            'cep' => $cep, 
                            'contact' => $contact,
                            'email' => $email, 
                            'phone' => $phone,
                            'stars' => $stars, 
                            'checkin' => $checkin,
                            'checkout' => $checkout, 
                            'hotelstatus' => $hotelstatus,
                            'taxes' => $taxes, 
                            'chd' => $chd
                        );
      
                        $sql    = new Sql($dbUpdate);
                        $update = $sql->update();
                        $update->table('omnibees_hotels');
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
                        $insert->into('omnibees_hotels');
                        $insert->values(array(
                            'id' => $hotelid,
                            'name' => $name,  
                            'rede' => $rede,
                            'group' => $group, 
                            'gestor' => $gestor,
                            'city' => $city, 
                            'uf' => $uf,
                            'country' => $country, 
                            'cadastro' => $cadastro,
                            'countrycode' => $countrycode, 
                            'stateprovcode' => $stateprovcode,
                            'citycode' => $citycode, 
                            'zonecode' => $zonecode,
                            'latitude' => $latitude, 
                            'longitude' => $longitude,
                            'razao_social' => $razao_social, 
                            'cnpj' => $cnpj,
                            'address' => $address, 
                            'complement' => $complement,
                            'cep' => $cep, 
                            'contact' => $contact,
                            'email' => $email, 
                            'phone' => $phone,
                            'stars' => $stars, 
                            'checkin' => $checkin,
                            'checkout' => $checkout, 
                            'hotelstatus' => $hotelstatus,
                            'taxes' => $taxes, 
                            'chd' => $chd
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
                    $insert->into('omnibees_hotels');
                    $insert->values(array(
                        'id' => $hotelid,
                        'name' => $name,  
                        'rede' => $rede,
                        'group' => $group, 
                        'gestor' => $gestor,
                        'city' => $city, 
                        'uf' => $uf,
                        'country' => $country, 
                        'cadastro' => $cadastro,
                        'countrycode' => $countrycode, 
                        'stateprovcode' => $stateprovcode,
                        'citycode' => $citycode, 
                        'zonecode' => $zonecode,
                        'latitude' => $latitude, 
                        'longitude' => $longitude,
                        'razao_social' => $razao_social, 
                        'cnpj' => $cnpj,
                        'address' => $address, 
                        'complement' => $complement,
                        'cep' => $cep, 
                        'contact' => $contact,
                        'email' => $email, 
                        'phone' => $phone,
                        'stars' => $stars, 
                        'checkin' => $checkin,
                        'checkout' => $checkout, 
                        'hotelstatus' => $hotelstatus,
                        'taxes' => $taxes, 
                        'chd' => $chd
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
    fclose($filename);
}

readCSV("Hoteis_Omnibees.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>