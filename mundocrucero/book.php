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
echo "COMECOU BOOK<br/>";
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
$sql = "select value from settings where name='enablemundocruceros' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_mundocruceros = $affiliate_id;
} else {
    $affiliate_id_mundocruceros = 0;
}
$sql = "select value from settings where name='mundocrucerosusername' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $mundocrucerosusername = $row_settings['value'];
}
$sql = "select value from settings where name='mundocrucerospassword' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $mundocrucerospassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='mundocrucerosServiceURL' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosServiceURL = $row['value'];
}
$sql = "select value from settings where name='mundocrucerosServiceURLBook' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosServiceURLBook = $row['value'];
}
$sql = "select value from settings where name='mundocrucerosServiceURLBook' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosServiceURLBook = $row['value'];
}
$sql = "select value from settings where name='mundocrucerosSID' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosSID = $row['value'];
}
$sql = "select value from settings where name='mundocrucerosWebsite' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosWebsite = $row['value'];
}

$sessionkey = '73B05D95-87DDp4FD7-90B0-8C0DED0DF5DA';
$basketcode = '7SRSCC';
$bedconfig = 'QN';
$tablesize = '';
$seating = '';

$raw = 'xml=<?xml version="1.0"?>
<request>
    <auth username="' . $mundocrucerosusername . '" password="' . $mundocrucerospassword . '" />
    <method action="book" sessionkey="' . $sessionkey . '" status="Test">
    <contact address1="37 Hawbank Road" address2="College Milton" city="East Kilbride" country="GB" county="Glasgow" email="richard@traveltek.net" firstname="Mary" lastname="Smith" postcode="G74 5EG" telephone="01355 246111" title="MISS" />
    <passengers>
        <passenger dob="1973-07-24" title="MISS" firstname="Mary" lastname="Smith" paxno="1" paxtype="adult" nationality="GB" passport="26347891107" travelling="" />
        <passenger dob="1973-07-24" title="MR" firstname="Richard" lastname="Smith" paxno="2" paxtype="adult" nationality="GB" passport="26347891214" travelling=""/>         
    </passengers>
    <allocation>
        <requests basketcode="' . $basketcode . '" request="BOOKING ONLY" />
        <bedconfig basketcode="' . $basketcode . '" bedconfig="' . $bedconfig . '"/>
    </allocation>
    <deposits paydepositonly="N" />
</method>
</request>';

$raw2 = 'xml=<?xml version="1.0"?>
<request>
        <auth password="' . $mundocrucerospassword . '" username="' . $mundocrucerosusername . '" />
        <method action="book" sessionkey="' . $sessionkey . '" status="Test">
        <allocation>
            <requests basketcode="' . $basketcode . '" request="TEST BOOKING ONLY" />
        </allocation>
        <contact address1="37 Hawbank Road" address2="College Milton" city="East Kilbride" country="GB" county="Glasgow" email="noreply@traveltek.net" firstname="Mary" lastname="Smith" postcode="G74 5EG" telephone="01355 246111" title="MISS" />
        <creditcard address1="Hawbank" cardno="4444444444444444" cardtype="VIS" city="Glasgow" country="UK" county="Glasgow" cvv="000" expirymonth="01" expiryyear="2022" firstname="Richard" lastname="Smith" nameoncard="Richard Smith" postcode="G74 5EG" startmonth="01" startyear="2012" title="Mr" />
        <deposits paydepositonly="Y" />
        <passengers>
            <passenger dob="1973-07-24" title="MISS" firstname="Mary" lastname="Smith" paxno="1" paxtype="adult" nationality="GB" passport="26347891107" travelling="" />
            <passenger dob="1973-07-24" title="MR" firstname="Richard" lastname="Smith" paxno="2" paxtype="adult" nationality="GB" passport="26347891214" travelling=""/>  
        </passengers>
        <paymentschedule cardtype="VIS" lowdeposit="Y" token="abcd1234" totaldeposit="9952">
            <schedule>
                <item amount="9000" completed="Y" duedate="2021-02-01" type="lowdeposit" />
                <item amount="9952" completed="Y" duedate="2021-03-01" type="deposit" />
            </schedule>
        </paymentschedule>
        </method>
</request>';

echo "<xmp>";
echo $raw;
echo "</xmp>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $mundocrucerosServiceURLBook);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate");
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-type: application/x-www-form-urlencoded",
    "Accept-Encoding: gzip, deflate",
    "Content-length: " . strlen($raw)
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<xmp>";
echo $response;
echo "</xmp>";


$config = new \Zend\Config\Config(include '../config/autoload/global.mundocruceros.php');
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
$results = $inputDoc->getElementsByTagName("results");
$node = $results->item(0)->getElementsByTagName("region");
$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$response = $inputDoc->getElementsByTagName("response");
$sessionkey = $response->item(0)->getAttribute("sessionkey");
$success = $response->item(0)->getAttribute("success");
if ($success == 'Y') {
    $request = $response->item(0)->getElementsByTagName("request");
    if ($request->length > 0) {
        $method = $request->item(0)->getElementsByTagName("method");
        if ($method->length > 0) {
            $sessionkey = $method->item(0)->getAttribute("sessionkey");
        }
    }
    $results = $response->item(0)->getElementsByTagName("results");
    if ($results->length > 0) {
        $book = $results->item(0)->getElementsByTagName("book");
        if ($book->length > 0) {
            $reservation = $book->item(0)->getAttribute("reservation");
            $status = $book->item(0)->getAttribute("status");
            $bookingdetails = $book->item(0)->getElementsByTagName("bookingdetails");
            if ($bookingdetails->length > 0) {
                $address1 = $bookingdetails->item(0)->getAttribute("address1");
                $address2 = $bookingdetails->item(0)->getAttribute("address2");
                $address3 = $bookingdetails->item(0)->getAttribute("address3");
                $address4 = $bookingdetails->item(0)->getAttribute("address4");
                $address5 = $bookingdetails->item(0)->getAttribute("address5");
                $affiliate = $bookingdetails->item(0)->getAttribute("affiliate");
                $altphone = $bookingdetails->item(0)->getAttribute("altphone");
                $balancepaymentdate = $bookingdetails->item(0)->getAttribute("balancepaymentdate");
                $bookingforbranchid = $bookingdetails->item(0)->getAttribute("bookingforbranchid");
                $bookingforteamid = $bookingdetails->item(0)->getAttribute("bookingforteamid");
                $bookingforuserid = $bookingdetails->item(0)->getAttribute("bookingforuserid");
                $branchcategory1id = $bookingdetails->item(0)->getAttribute("branchcategory1id");
                $branchcategory2id = $bookingdetails->item(0)->getAttribute("branchcategory2id");
                $branchcategory3id = $bookingdetails->item(0)->getAttribute("branchcategory3id");
                $branchid = $bookingdetails->item(0)->getAttribute("branchid");
                $city = $bookingdetails->item(0)->getAttribute("city");
                $country = $bookingdetails->item(0)->getAttribute("country");
                $county = $bookingdetails->item(0)->getAttribute("county");
                $currency = $bookingdetails->item(0)->getAttribute("currency");
                $datebooked = $bookingdetails->item(0)->getAttribute("datebooked");
                $departdate = $bookingdetails->item(0)->getAttribute("departdate");
                $depositbooking = $bookingdetails->item(0)->getAttribute("depositbooking");
                $depositduedate = $bookingdetails->item(0)->getAttribute("depositduedate");
                $deposittotal = $bookingdetails->item(0)->getAttribute("deposittotal");
                $email = $bookingdetails->item(0)->getAttribute("email");
                $enquiryid = $bookingdetails->item(0)->getAttribute("enquiryid");
                $firstname = $bookingdetails->item(0)->getAttribute("firstname");
                $gender = $bookingdetails->item(0)->getAttribute("gender");
                $id = $bookingdetails->item(0)->getAttribute("id");
                $internalref = $bookingdetails->item(0)->getAttribute("internalref");
                $ipaddress = $bookingdetails->item(0)->getAttribute("ipaddress");
                $language = $bookingdetails->item(0)->getAttribute("language");
                $lastname = $bookingdetails->item(0)->getAttribute("lastname");
                $lowdeposit = $bookingdetails->item(0)->getAttribute("lowdeposit");
                $lowdepositamount = $bookingdetails->item(0)->getAttribute("lowdepositamount");
                $lowdepositduedate = $bookingdetails->item(0)->getAttribute("lowdepositduedate");
                $loyaltypoints = $bookingdetails->item(0)->getAttribute("loyaltypoints");
                $loyaltyprogramid = $bookingdetails->item(0)->getAttribute("loyaltyprogramid");
                $middlename = $bookingdetails->item(0)->getAttribute("middlename");
                $mobile = $bookingdetails->item(0)->getAttribute("mobile");
                $offersbyemail = $bookingdetails->item(0)->getAttribute("offersbyemail");
                $ownerid = $bookingdetails->item(0)->getAttribute("ownerid");
                $passengermobile = $bookingdetails->item(0)->getAttribute("passengermobile");
                $paypal = $bookingdetails->item(0)->getAttribute("paypal");
                $portfolioid = $bookingdetails->item(0)->getAttribute("portfolioid");
                $postcode = $bookingdetails->item(0)->getAttribute("postcode");
                $promocode = $bookingdetails->item(0)->getAttribute("promocode");
                $returndate = $bookingdetails->item(0)->getAttribute("returndate");
                $session = $bookingdetails->item(0)->getAttribute("session");
                $teamid = $bookingdetails->item(0)->getAttribute("teamid");
                $telephone = $bookingdetails->item(0)->getAttribute("telephone");
                $title = $bookingdetails->item(0)->getAttribute("title");
                $toppercurrency = $bookingdetails->item(0)->getAttribute("toppercurrency");
                $topperexchangerate = $bookingdetails->item(0)->getAttribute("topperexchangerate");
                $totalcommission = $bookingdetails->item(0)->getAttribute("totalcommission");
                $totalprice = $bookingdetails->item(0)->getAttribute("totalprice");

                $items = $bookingdetails->item(0)->getElementsByTagName("items");
                if ($items->length > 0) {
                    $item = $items->item(0)->getElementsByTagName("item");
                    if ($item->length > 0) {
                        $codetocruiseid = $item->item(0)->getAttribute("codetocruiseid");
                        $airportcode = $item->item(0)->getAttribute("airportcode");
                        $bedconfig = $item->item(0)->getAttribute("bedconfig");
                        $bosuppliercode = $item->item(0)->getAttribute("bosuppliercode");
                        $cabinbedtype = $item->item(0)->getAttribute("cabinbedtype");
                        $cabincode = $item->item(0)->getAttribute("cabincode");
                        $cabindesc = $item->item(0)->getAttribute("cabindesc");
                        $cabinextra = $item->item(0)->getAttribute("cabinextra");
                        $cabinid = $item->item(0)->getAttribute("cabinid");
                        $cabinlocation = $item->item(0)->getAttribute("cabinlocation");
                        $cabinname = $item->item(0)->getAttribute("cabinname");
                        $cabinno = $item->item(0)->getAttribute("cabinno");
                        $cabinposition = $item->item(0)->getAttribute("cabinposition");
                        $cruisename = $item->item(0)->getAttribute("cruisename");
                        $deckid = $item->item(0)->getAttribute("deckid");
                        $deckname = $item->item(0)->getAttribute("deckname");
                        $diningseating = $item->item(0)->getAttribute("diningseating");
                        $diningsmoking = $item->item(0)->getAttribute("diningsmoking");
                        $diningwith = $item->item(0)->getAttribute("diningwith");
                        $enddate = $item->item(0)->getAttribute("enddate");
                        $engine = $item->item(0)->getAttribute("engine");
                        $finalpaymentdate = $item->item(0)->getAttribute("finalpaymentdate");
                        $grossexchangerate = $item->item(0)->getAttribute("grossexchangerate");
                        $groupallocationid = $item->item(0)->getAttribute("groupallocationid");
                        $ibossuppliercodeid = $item->item(0)->getAttribute("ibossuppliercodeid");
                        $ibossupplierid = $item->item(0)->getAttribute("ibossupplierid");
                        $lineid = $item->item(0)->getAttribute("lineid");
                        $linename = $item->item(0)->getAttribute("linename");
                        $modified = $item->item(0)->getAttribute("modified");
                        $ncf = $item->item(0)->getAttribute("ncf");
                        $nettexchangerate = $item->item(0)->getAttribute("nettexchangerate");
                        $nights = $item->item(0)->getAttribute("nights");
                        $obccurrency = $item->item(0)->getAttribute("obccurrency");
                        $onboardcredit = $item->item(0)->getAttribute("onboardcredit");
                        $optionexpirydate = $item->item(0)->getAttribute("optionexpirydate");
                        $originalnettdue = $item->item(0)->getAttribute("originalnettdue");
                        $ownstockid = $item->item(0)->getAttribute("ownstockid");
                        $price = $item->item(0)->getAttribute("price");
                        $pricecode = $item->item(0)->getAttribute("pricecode");
                        $request = $item->item(0)->getAttribute("request");
                        $reservation = $item->item(0)->getAttribute("reservation");
                        $returndate = $item->item(0)->getAttribute("returndate");
                        $saildate = $item->item(0)->getAttribute("saildate");
                        $sailnights = $item->item(0)->getAttribute("sailnights");
                        $shipid = $item->item(0)->getAttribute("shipid");
                        $shipname = $item->item(0)->getAttribute("shipname");
                        $startdate = $item->item(0)->getAttribute("startdate");
                        $status = $item->item(0)->getAttribute("status");
                        $suppliername = $item->item(0)->getAttribute("suppliername");
                        $tablesize = $item->item(0)->getAttribute("tablesize");
                        $type = $item->item(0)->getAttribute("type");
                        $voyagecode = $item->item(0)->getAttribute("voyagecode");

                        $rewards = $item->item(0)->getElementsByTagName("rewards");
                        if ($rewards->length > 0) {
                            $passengerid = $rewards->item(0)->getAttribute("passengerid");
                            $value = $rewards->item(0)->getAttribute("value");
                        }
                    }
                }
                $passengers = $bookingdetails->item(0)->getElementsByTagName("passengers");
                if ($passengers->length > 0) {
                    $passenger = $passengers->item(0)->getElementsByTagName("passenger");
                    if ($passenger->length > 0) {
                        for ($x=0; $x < $passenger->length; $x++) { 
                            $age = $passenger->item($x)->getAttribute("age");
                            $bookingid = $passenger->item($x)->getAttribute("bookingid");
                            $cancelled = $passenger->item($x)->getAttribute("cancelled");
                            $country = $passenger->item($x)->getAttribute("country");
                            $dob = $passenger->item($x)->getAttribute("dob");
                            $emergencyemail = $passenger->item($x)->getAttribute("emergencyemail");
                            $emergencyname = $passenger->item($x)->getAttribute("emergencyname");
                            $emergencyphone = $passenger->item($x)->getAttribute("emergencyphone");
                            $firstname = $passenger->item($x)->getAttribute("firstname");
                            $gender = $passenger->item($x)->getAttribute("gender");
                            $insuranceassistancecompany = $passenger->item($x)->getAttribute("insuranceassistancecompany");
                            $insurancecompany = $passenger->item($x)->getAttribute("insurancecompany");
                            $insurancepolicynumber = $passenger->item($x)->getAttribute("insurancepolicynumber");
                            $insurancetelnumber = $passenger->item($x)->getAttribute("insurancetelnumber");
                            $lastname = $passenger->item($x)->getAttribute("lastname");
                            $mealoption = $passenger->item($x)->getAttribute("mealoption");
                            $middlename = $passenger->item($x)->getAttribute("middlename");
                            $nokaddress1 = $passenger->item($x)->getAttribute("nokaddress1");
                            $nokaddress2 = $passenger->item($x)->getAttribute("nokaddress2");
                            $nokaddress3 = $passenger->item($x)->getAttribute("nokaddress3");
                            $nokaddress4 = $passenger->item($x)->getAttribute("nokaddress4");
                            $nokname = $passenger->item($x)->getAttribute("nokname");
                            $nokphone = $passenger->item($x)->getAttribute("nokphone");
                            $nokrelationship = $passenger->item($x)->getAttribute("nokrelationship");
                            $ownerid = $passenger->item($x)->getAttribute("ownerid");
                            $passportauthority = $passenger->item($x)->getAttribute("passportauthority");
                            $passportexpiry = $passenger->item($x)->getAttribute("passportexpiry");
                            $passportplaceofissue = $passenger->item($x)->getAttribute("passportplaceofissue");
                            $passportstart = $passenger->item($x)->getAttribute("passportstart");
                            $paxno = $passenger->item($x)->getAttribute("paxno");
                            $paxtype = $passenger->item($x)->getAttribute("paxtype");
                            $placeofbirth = $passenger->item($x)->getAttribute("placeofbirth");
                            $redress = $passenger->item($x)->getAttribute("redress");
                            $specialservices = $passenger->item($x)->getAttribute("specialservices");
                            $title = $passenger->item($x)->getAttribute("title");
                            $travelling = $passenger->item($x)->getAttribute("travelling");
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
echo '<br/>Done';
?>
