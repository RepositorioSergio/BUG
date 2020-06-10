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
echo "COMECOU PORTS<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.costa.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = 'ports.xml';
$response = file_get_contents($filename);
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$CostaPortCatalog = $inputDoc->getElementsByTagName("CostaPortCatalog");
if ($CostaPortCatalog->length > 0) {
    $Ports = $CostaPortCatalog->item(0)->getElementsByTagName("Ports");
    if ($Ports->length > 0) {
        $Port = $Ports->item(0)->getElementsByTagName("Port");
        if ($Port->length > 0) {
            for ($i=0; $i < $Port->length; $i++) { 
                $Code = $Port->item($i)->getAttribute("Code");
                echo $return;
                echo "Code: " . $Code;
                echo $return;
                $Description = $Port->item($i)->getAttribute("Description");
                $AlternativeDescription = $Port->item($i)->getAttribute("AlternativeDescription");
                $ShortDescription = $Port->item($i)->getAttribute("ShortDescription");
                $LongDescription = $Port->item($i)->getAttribute("LongDescription");
                $VeryLongDescription = $Port->item($i)->getAttribute("VeryLongDescription");
                $ImageUrl = $Port->item($i)->getAttribute("ImageUrl");
                echo $return;
                echo "IMAGE: " . $ImageUrl;
                echo $return;

                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('ports');
                $select->where(array(
                    'code' => $Code
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $id = (string) $data['code'];
                    if ($id != "") {
                        $sql = new Sql($db);
                        $data = array(
                            'code' => $Code,
                            'datetime_created' => time(),
                            'datetime_updated' => 1,
                            'description' => $Description,
                            'alternativedescription' => $AlternativeDescription,
                            'shortdescription' => $ShortDescription,
                            'longdescription' => $LongDescription,
                            'verylongdescription' => $VeryLongDescription,
                            'imageurl' => $ImageUrl
                            );
                            $where['code = ?']  = $Code;
                        $update = $sql->update('ports', $data, $where);
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();   
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('ports');
                        $insert->values(array(
                            'code' => $Code,
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'description' => $Description,
                            'alternativedescription' => $AlternativeDescription,
                            'shortdescription' => $ShortDescription,
                            'longdescription' => $LongDescription,
                            'verylongdescription' => $VeryLongDescription,
                            'imageurl' => $ImageUrl
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
                    $insert->into('ports');
                    $insert->values(array(
                        'code' => $Code,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'description' => $Description,
                        'alternativedescription' => $AlternativeDescription,
                        'shortdescription' => $ShortDescription,
                        'longdescription' => $LongDescription,
                        'verylongdescription' => $VeryLongDescription,
                        'imageurl' => $ImageUrl
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('ports');
                    $insert->values(array(
                        'code' => $Code,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'description' => $Description
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO 1: " . $e;
                    echo $return;
                }
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