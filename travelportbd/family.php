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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.travelport.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = "Family.csv";
$object = fopen($filename, 'r');
$line = 0;

while ($data = fgetcsv($object, 0, ",")) {
    if ($line > 0) {
        $id = $data[0];
        $carriercode = $data[1];
        $farefamilyname = $data[2];
        $farefamilynamesearch = $data[3];
        $newfamilyindicator = $data[4];
        $passenger_typecode = $data[5];
        $effective_searchdate = $data[6];
        $discontinued_searchdate = $data[7];
        $publicindicator = $data[8];
        $effective_traveldate = $data[9];
        $discontinued_traveldate = $data[10];
        $sequencenumber = $data[11];
        $atpcoprogramcode = $data[12];
        $atpcoprogramdescription = $data[13];
        $atpcosequencenumber = $data[14];
        $versionnumber = $data[15];
        $sourcetype = $data[16];
        $status = $data[17];
        $accounttypecode = $data[18];
        $iatacode = $data[19];
        $gds = $data[20];
        $pcc = $data[21];
        $securitylocationtypecode = $data[22];
        $securitylocationcode = $data[23];
        $permittedind = $data[24];
        $locationone_typecode = $data[25];
        $locationone = $data[26];
        $locationtwo_typecode = $data[27];
        $locationtwo = $data[28];
        $permittedindicator = $data[29];
        $directionapplicationcode = $data[30];
        $globalindicator = $data[31];

        try {
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('family');
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
                    $config = new \Zend\Config\Config(include '../config/autoload/global.travelport.php');
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
                        'carriercode' => $carriercode, 
                        'farefamilyname' => $farefamilyname,
                        'farefamilynamesearch' => $farefamilynamesearch,
                        'newfamilyindicator' => $newfamilyindicator,
                        'passenger_typecode' => $passenger_typecode,
                        'effective_searchdate' => $effective_searchdate,
                        'discontinued_searchdate' => $discontinued_searchdate,
                        'publicindicator' => $publicindicator,
                        'effective_traveldate' => $effective_traveldate,
                        'discontinued_traveldate' => $discontinued_traveldate,
                        'sequencenumber' => $sequencenumber,
                        'atpcoprogramcode' => $atpcoprogramcode,
                        'atpcoprogramdescription' => $atpcoprogramdescription,
                        'atpcosequencenumber' => $atpcosequencenumber,
                        'versionnumber' => $versionnumber,
                        'sourcetype' => $sourcetype,
                        'status' => $status,
                        'accounttypecode' => $accounttypecode,
                        'iatacode' => $iatacode,
                        'gds' => $gds,
                        'pcc' => $pcc,
                        'securitylocationtypecode' => $securitylocationtypecode,
                        'securitylocationcode' => $securitylocationcode,
                        'permittedind' => $permittedind,
                        'locationone_typecode' => $locationone_typecode,
                        'locationone' => $locationone,
                        'locationtwo_typecode' => $locationtwo_typecode,
                        'locationtwo' => $locationtwo,
                        'permittedindicator' => $permittedindicator,
                        'directionapplicationcode' => $directionapplicationcode,
                        'globalindicator' => $globalindicator
                    );
  
                    $sql    = new Sql($dbUpdate);
                    $update = $sql->update();
                    $update->table('family');
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
                    $insert->into('family');
                    $insert->values(array(
                        'id' => $id,
                        'datetime_updated' => time(),
                        'carriercode' => $carriercode, 
                        'farefamilyname' => $farefamilyname,
                        'farefamilynamesearch' => $farefamilynamesearch,
                        'newfamilyindicator' => $newfamilyindicator,
                        'passenger_typecode' => $passenger_typecode,
                        'effective_searchdate' => $effective_searchdate,
                        'discontinued_searchdate' => $discontinued_searchdate,
                        'publicindicator' => $publicindicator,
                        'effective_traveldate' => $effective_traveldate,
                        'discontinued_traveldate' => $discontinued_traveldate,
                        'sequencenumber' => $sequencenumber,
                        'atpcoprogramcode' => $atpcoprogramcode,
                        'atpcoprogramdescription' => $atpcoprogramdescription,
                        'atpcosequencenumber' => $atpcosequencenumber,
                        'versionnumber' => $versionnumber,
                        'sourcetype' => $sourcetype,
                        'status' => $status,
                        'accounttypecode' => $accounttypecode,
                        'iatacode' => $iatacode,
                        'gds' => $gds,
                        'pcc' => $pcc,
                        'securitylocationtypecode' => $securitylocationtypecode,
                        'securitylocationcode' => $securitylocationcode,
                        'permittedind' => $permittedind,
                        'locationone_typecode' => $locationone_typecode,
                        'locationone' => $locationone,
                        'locationtwo_typecode' => $locationtwo_typecode,
                        'locationtwo' => $locationtwo,
                        'permittedindicator' => $permittedindicator,
                        'directionapplicationcode' => $directionapplicationcode,
                        'globalindicator' => $globalindicator
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
                $insert->into('family');
                $insert->values(array(
                    'id' => $id,
                    'datetime_updated' => time(),
                    'carriercode' => $carriercode, 
                    'farefamilyname' => $farefamilyname,
                    'farefamilynamesearch' => $farefamilynamesearch,
                    'newfamilyindicator' => $newfamilyindicator,
                    'passenger_typecode' => $passenger_typecode,
                    'effective_searchdate' => $effective_searchdate,
                    'discontinued_searchdate' => $discontinued_searchdate,
                    'publicindicator' => $publicindicator,
                    'effective_traveldate' => $effective_traveldate,
                    'discontinued_traveldate' => $discontinued_traveldate,
                    'sequencenumber' => $sequencenumber,
                    'atpcoprogramcode' => $atpcoprogramcode,
                    'atpcoprogramdescription' => $atpcoprogramdescription,
                    'atpcosequencenumber' => $atpcosequencenumber,
                    'versionnumber' => $versionnumber,
                    'sourcetype' => $sourcetype,
                    'status' => $status,
                    'accounttypecode' => $accounttypecode,
                    'iatacode' => $iatacode,
                    'gds' => $gds,
                    'pcc' => $pcc,
                    'securitylocationtypecode' => $securitylocationtypecode,
                    'securitylocationcode' => $securitylocationcode,
                    'permittedind' => $permittedind,
                    'locationone_typecode' => $locationone_typecode,
                    'locationone' => $locationone,
                    'locationtwo_typecode' => $locationtwo_typecode,
                    'locationtwo' => $locationtwo,
                    'permittedindicator' => $permittedindicator,
                    'directionapplicationcode' => $directionapplicationcode,
                    'globalindicator' => $globalindicator
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