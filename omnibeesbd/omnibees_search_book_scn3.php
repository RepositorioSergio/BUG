<?php
if (! $_SERVER['DOCUMENT_ROOT']) {
    // On Command Line
    $return = "\r\n";
} else {
    // HTTP Browser
    $return = "<br>";
}

echo $return;
echo "Starting Cancel Reservation...";
echo $return;
$url = "https://pullcert.omnibees.com/PullService.svc?wsdl";
try {
    $client = new SoapClient($url, array(
        'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
        "trace" => 1,
        "exceptions" => true,
        'soap_version' => SOAP_1_1
    ));
} catch (Exception $e) {
    echo $e->getMessage();
}
//
// var_dump($client->__getFunctions());
//
$from = "2019-07-05T00:00:00";
$to = "2019-07-07T00:00:00";
$rooms = 1;

$params = array();
$params['login']['UserName'] = "BugSoftware";
$params['login']['Password'] = "WO5bYE2A";
$params['ota_CancelRQ']['PrimaryLangID'] = "en";
$params['ota_CancelRQ']['EchoToken'] = "1154782d-ea51-478e-a2c2-02b66b5339c2";
$params['ota_CancelRQ']['TimeStamp'] = strftime("%Y-%m-%dT%H:%m:%S", time());
$params['ota_CancelRQ']['Target'] = "Test";
$params['ota_CancelRQ']['Version'] = "2.6";
$params['ota_CancelRQ']['UniqueID']['UniqueID'][0]["ID"] = "RES034807-1053";
$params['ota_CancelRQ']['UniqueID']['UniqueID'][0]["Reason"] = "Cancelation reason";
$params['ota_CancelRQ']['UniqueID']['UniqueID'][0]["Type"] = "Reservation";
$params['ota_CancelRQ']['Verification']['email'] = "paulo@corp.bug-software.com";
$params['ota_CancelRQ']['Verification']['HotelRef']['HotelCode'] = "1053";
$params['ota_CancelRQ']['Verification']['HotelRef']['ChainCode'] = "986";
$params['ota_CancelRQ']['Verification']['ReservationTimeSpan']["End"] = $to;
$params['ota_CancelRQ']['Verification']['ReservationTimeSpan']["Start"] = $from;
try {
    $client->__soapCall('SendHotelResCancel', array(
        $params
    ));
} catch (Exception $e) {
    echo $e->getMessage();
}
$xmlrequest = $client->__getLastRequest();
echo $return;
echo $return;
$xmlresult = $client->__getLastResponse();
echo $xmlrequest;
echo $return;
echo $return;
echo $xmlresult;
echo $return;
echo $return;
echo $return;
echo $return;
echo $return;
echo "End";
echo $return;
?>