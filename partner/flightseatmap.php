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
echo "COMECOU BOOKING";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://api-sandbox.rezserver.com/api/air/getFlightSeatMap?format=json2&refid=10088&api_key=1f72bc370d770f4b7a6aea7758dfa31c&sid=abcEZas123&ppn_bundle=_eJwBUAOv_fHibu9_fbbmXPVBWytCL5GPWRm84GPkS4gDlg7_fKHEgDBFqPlHxU1u_f73eu_fHvc3jbkHHU0TjZwR0GcE48Tc7WtBnpSBYupCFKTo7EPSnmYtC5ul5YdRv_fA7Zvcuj3oLUaxF7qIVN9vqhvk9FhMQ4_f4Q3Jt9UbbKaUGlC5NnO047ShRFoRfztu_p88CeMTnjm7CgOEpnK03E_fnPiTJm_pQma4bUdcq8ZNbQ_pg1XtEaOvSuGrq4n3xJ2ca23vzGmhvGA5h3uNzat6X4PEVDk0rtZAptr_pzzZ0PIZPmAJpmfxm1wz0cZD_pne6OaNQF7y6h8ttKXRGfh1BYVEnwQ0mxKMdAc8iYZAACW5O_f8tEVhGe7gAaGJ2pDbmxKssT5Re3GF2ZvEpWCiEJs8fj4CIAS7OM1KAveR_fDaBRUSkFFvwvAvTfgUpgboeCN1Zl3zvwKeZtGNdcOzHyqJpuvbB892IliI58cywPci8aaVRpJIXj7alQjVxV_fOetbp9XMmN4G_f_fmNbiGH8GETDyz8BI_ftmyJlGSOBcVSyrw9lR_fXHI5Qb4QP0y6vcjNAB3K3M9AEHoBZBnF6TGS9H759jEBIjTEZj4x2uySXyTIKYn2aC6EjZ4XutSd_pEYaCx9jDPCJlLR9edOol3l2Zpo58bPSGqba9xZarNtTvpaWR2ksDsXXE8aHyvbaAnsdYoqQcnPm3AzBIB7qNSuQamvOwvBm_pI6At_f2CMW5tcq9d1jvmtydi8lqxu8_fan5Ua8yXIZSZxSE5xCN2ZP0hn1_pm9h6TJvVHoKRp1Ea8vHk0hlCV87qDI2MIrCc_f7bFvyhu5IMP_p2Vjj7EiEcjZH28TaZ_pvnDnCSlh6eBxgw7Q79DC5aDYEaBNBwIrlDKLTcNeIMMi2mqHyAZwowZNnjlP63Eqe3Y6407_p0RajlS9DTFBCeKnzaQyq28wBlEYqWu4OOd_fjTGoavGSXDqGi5XvcVB1W6_fBRQzsEmzjF79P44SM7FDgeBuQPCk4jQRCmHjn1tp2YTn34LDlLH_pZYK4S1E4pKBHuCc5H_f25D0lY2zdosHlugYQGivzL7VSysm5reLRmRGzeC2_fg9Jdj5kRmkJJUkBbCt16ahy4sMwVRLcwPlOxl12zOeKBdvMXdCanew';
$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Length' => strlen($raw),
    'Content-Type' => 'application/json;charset=utf-8'
));
$client->setUri($url);
$client->setMethod('GET');
//$client->setRawBody($raw);
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
}

echo $return;
echo $response;
echo $return; 
$response = json_decode($response, true);

$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
    
$getAirFlightSeatMap = $response['getAirFlightSeatMap'];
$results = $getAirFlightSeatMap['results'];
$status = $results['status'];
$status_code = $results['status_code'];
$result = $results['result'];
$sid = $result['sid'];
$flight_data = $result['flight_data'];
if (count($flight_data) > 0) {
    for ($i=0; $i < count($flight_data); $i++) { 
        $flight_info = $flight_data[$i]['flight_info'];
        $seatmap_id = $flight_info['seatmap_id'];
        $number = $flight_info['number'];
        $depart = $flight_info['depart'];
        $arrive = $flight_info['arrive'];

        $airplane_info = $flight_data[$i]['airplane_info'];
        $deck_count = $airplane_info['deck_count'];
        $aircraft = $airplane_info['aircraft'];

        $deck_data = $flight_data[$i]['deck_data'];
        if (count($deck_data) > 0) {
            for ($iAux=0; $iAux < count($deck_data); $iAux++) { 
                $deck = $deck_data[$iAux]['deck'];
                $class = $deck_data[$iAux]['class'];
                $row_data = $deck_data[$iAux]['row_data'];
                if (count($row_data) > 0) {
                    for ($iAux2=0; $iAux2 < count($row_data); $iAux2++) { 
                        $number = $row_data[$iAux2]['number'];
                        $exit = $row_data[$iAux2]['exit'];
                        $wing = $row_data[$iAux2]['wing'];
                        $seat_data = $row_data[$iAux2]['seat_data'];
                        if (count($seat_data) > 0) {
                            for ($iAux3=0; $iAux3 < count($seat_data); $iAux3++) { 
                                $type = $seat_data[$iAux3]['type'];
                                $seat_details = $seat_data[$iAux3]['seat_details'];
                                $bulkhead = $seat_data[$iAux3]['bulkhead'];
                                $handicap = $seat_data[$iAux3]['handicap'];
                                $preferred = $seat_data[$iAux3]['preferred'];
                                $paid = $seat_data[$iAux3]['paid'];
                                $infant_suitable = $seat_data[$iAux3]['infant_suitable'];
                                $status = $seat_data[$iAux3]['status'];
                                $aisle = $seat_data[$iAux3]['aisle'];
                                $code = $seat_data[$iAux3]['code'];
                            }
                        }
                    }
                }
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