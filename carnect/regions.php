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
$sql = "select value from settings where name='enableCarnectCars' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_carnect = $affiliate_id;
} else {
    $affiliate_id_carnect = 0;
}
$sql = "select value from settings where name='CarnectLogin' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarnectLogin = $row_settings['value'];
}
$sql = "select value from settings where name='CarnectCarspassword' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarnectCarspassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='CarnectCarsDestinationsServicesURL' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $CarnectCarsDestinationsServicesURL = $row['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.carnect.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "SELECT ISO FROM countries";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}
$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $ISO = $row->ISO;
        echo $return;
        echo "ISO: " . $ISO;
        echo $return;

        $xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:ns="http://www.opentravel.org/OTA/2003/05">
        <soapenv:Header />
        <soapenv:Body>
            <ns:VehicleRegionRequest>
            <ns:Language>EN</ns:Language>
            <ns:CountryISO>' . $ISO . '</ns:CountryISO>
            </ns:VehicleRegionRequest>
        </soapenv:Body>
        </soapenv:Envelope>';
        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Accept-Encoding: gzip",
            "Content-length: " . strlen($xml_post_string)
        );
        //
        // PHP CURL for https connection with auth
        //
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $CarnectCarsDestinationsServicesURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $xmlresult = curl_exec($ch);
        curl_close($ch);
        echo $xmlresult;

        $config = new \Zend\Config\Config(include '../config/autoload/global.carnect.php');
        $config = [
            'driver' => $config->db->driver,
            'database' => $config->db->database,
            'username' => $config->db->username,
            'password' => $config->db->password,
            'hostname' => $config->db->hostname
        ];
        $db = new \Zend\Db\Adapter\Adapter($config);

        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($xmlresult);
        $Envelope = $inputDoc->getElementsByTagName("Envelope");
        $Body = $Envelope->item(0)->getElementsByTagName("Body");
        $VehicleRegionResponse = $Body->item(0)->getElementsByTagName('VehicleRegionResponse');
        $Regions = $VehicleRegionResponse->item(0)->getElementsByTagName('Regions');
        $node = $Regions->item(0)->getElementsByTagName('Region');
        echo $return;
        echo "TAM: " . $node->length;
        echo $return;
        for ($j=0; $j < $node->length; $j++) { 
            $region_id = $node->item($j)->getAttribute('id');
            echo $return;
            echo "region_id: " . $region_id;
            echo $return;
            $Name = $node->item($j)->getElementsByTagName('Name');
            if ($Name->length > 0) {
                $Name = $Name->item(0)->nodeValue;
            } else {
                $Name = "";
            }
            $countryID = $node->item($j)->getElementsByTagName('countryID');
            if ($countryID->length > 0) {
                $countryID = $countryID->item(0)->nodeValue;
            } else {
                $countryID = "";
            }

            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('regions');
                $select->where(array(
                    'region_id' => $region_id
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $id = (int) $data['region_id'];
                    if ($id > 0) {
                        $sql = new Sql($db);
                        $data = array(
                            'datetime_created' => time(),
                            'datetime_updated' => 1,
                            'region_id' => $region_id,
                            'Name' => $Name,
                            'countryID' => $countryID
                            );
                            $where['region_id = ?']  = $region_id;
                        $update = $sql->update('regions', $data, $where);
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();   
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('regions');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'region_id' => $region_id,
                            'Name' => $Name,
                            'countryID' => $countryID
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
                    $insert->into('regions');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'region_id' => $region_id,
                        'Name' => $Name,
                        'countryID' => $countryID
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                }
            } catch (Exception $e) {
                echo $return;
                echo "Exception: " . $e;
                echo $return;
            }


        }

    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>