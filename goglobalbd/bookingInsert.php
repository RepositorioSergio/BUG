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
echo "COMECOU BOOKING INSERT TARDE";
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
$config = new \Zend\Config\Config(include '../config/autoload/global.goglobal.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:gog="http://www.goglobal.travel/"><soapenv:Header/><soapenv:Body>
      <gog:MakeRequest>
         <gog:requestType>2</gog:requestType>
         <gog:xmlRequest><![CDATA[
            <Root>
            	<Header>
            		<Agency>1521636</Agency>
            		<User>CLUB1XML</User>
            		<Password>andrade1998</Password>
            		<Operation>BOOKING_INSERT_REQUEST</Operation>
            		<OperationType>Request</OperationType>
            	</Header>
            	<Main Version="2.0">
            		<AgentReference>Test AgRef</AgentReference>
            		<HotelSearchCode>9295202/4184386420788407204/551</HotelSearchCode>
            		<ArrivalDate>2019-06-20</ArrivalDate>
            		<Nights>2</Nights>
            		<NoAlternativeHotel>1</NoAlternativeHotel>
            		<Leader LeaderPersonID="1"/>
            		<Rooms>
            			<RoomType Adults="2" >
            				<Room RoomID="1">
            					<PersonName PersonID="1" Title="MR." FirstName="JOHN" LastName="DOE" />
            					<PersonName PersonID="2" Title="MRS." FirstName="JAYNE" LastName="DOE" />
            				</Room>
            			</RoomType>
            		</Rooms>
            		<Preferences>
            			<LateArrival>19:20</LateArrival>
            			<AdjoiningRooms>1</AdjoiningRooms>
            			<ConnectingRooms>1</ConnectingRooms>
            		</Preferences>
            		<Remark>Test Remark</Remark>
            	</Main>
            </Root>
         ]]></gog:xmlRequest>
      </gog:MakeRequest>
   </soapenv:Body>
</soapenv:Envelope>';
/*
 * $client = new Client();
 * $client->setOptions(array(
 * 'timeout' => 100,
 * 'sslverifypeer' => false,
 * 'sslverifyhost' => false
 * ));
 * $client->setHeaders(array(
 * "Content-Type: text/xml; charset=utf-8",
 * "Content-length: " . strlen($raw)
 * ));
 *
 * $client->setUri('http://xml.qa.goglobal.travel/XMLWebService.asmx');
 * $client->setMethod('POST');
 * $client->setRawBody($raw);
 * $response = $client->send();
 * if ($response->isSuccess()) {
 * $response = $response->getBody();
 * } else {
 * $logger = new Logger();
 * $writer = new Writer\Stream('/srv/www/htdocs/error_log');
 * $logger->addWriter($writer);
 * $logger->info($client->getUri());
 * $logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
 * echo $return;
 * echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
 * echo $return;
 * die();
 * }
 */

$GoGlobalServiceURL = 'http://xml.qa.goglobal.travel/XMLWebService.asmx';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $GoGlobalServiceURL);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: text/xml; charset=utf-8",
    "Content-length: " . strlen($raw)
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);

if ($error != "") {
    echo $return;
    echo "ERRO: " . $error;
    echo $return;
} else {
    echo $return;
    echo "NAO TEM ERROS.";
    echo $return;
}

curl_close($ch);
echo $return;
echo $response;
echo $return;

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$MakeRequestResult = $inputDoc->getElementsByTagName("MakeRequestResult");
if ($MakeRequestResult->length > 0) {
    $response = $MakeRequestResult->item(0)->nodeValue;
} else {
    $response = "";
}

$config = new \Zend\Config\Config(include '../config/autoload/global.goglobal.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$inputDoc2 = new DOMDocument();
$inputDoc2->loadXML($response);
$Root = $inputDoc2->getElementsByTagName("Root");
if ($Root->length > 0) {
    $Main = $Root->item(0)->getElementsByTagName("Main");
    if ($Main->length > 0) {
        $GoBookingCode = $Main->item(0)->getElementsByTagName("GoBookingCode");
        if ($GoBookingCode->length > 0) {
            $GoBookingCode = $GoBookingCode->item(0)->nodeValue;
        } else {
            $GoBookingCode = "";
        }
        $GoReference = $Main->item(0)->getElementsByTagName("GoReference");
        if ($GoReference->length > 0) {
            $GoReference = $GoReference->item(0)->nodeValue;
        } else {
            $GoReference = "";
        }
        $ClientBookingCode = $Main->item(0)->getElementsByTagName("ClientBookingCode");
        if ($ClientBookingCode->length > 0) {
            $ClientBookingCode = $ClientBookingCode->item(0)->nodeValue;
        } else {
            $ClientBookingCode = "";
        }
        $BookingStatus = $Main->item(0)->getElementsByTagName("BookingStatus");
        if ($BookingStatus->length > 0) {
            $BookingStatus = $BookingStatus->item(0)->nodeValue;
        } else {
            $BookingStatus = "";
        }
        $TotalPrice = $Main->item(0)->getElementsByTagName("TotalPrice");
        if ($TotalPrice->length > 0) {
            $TotalPrice = $TotalPrice->item(0)->nodeValue;
        } else {
            $TotalPrice = "";
        }
        $Currency = $Main->item(0)->getElementsByTagName("Currency");
        if ($Currency->length > 0) {
            $Currency = $Currency->item(0)->nodeValue;
        } else {
            $Currency = "";
        }
        $HotelId = $Main->item(0)->getElementsByTagName("HotelId");
        if ($HotelId->length > 0) {
            $HotelId = $HotelId->item(0)->nodeValue;
        } else {
            $HotelId = "";
        }
        $HotelName = $Main->item(0)->getElementsByTagName("HotelName");
        if ($HotelName->length > 0) {
            $HotelName = $HotelName->item(0)->nodeValue;
        } else {
            $HotelName = "";
        }
        $HotelSearchCode = $Main->item(0)->getElementsByTagName("HotelSearchCode");
        if ($HotelSearchCode->length > 0) {
            $HotelSearchCode = $HotelSearchCode->item(0)->nodeValue;
        } else {
            $HotelSearchCode = "";
        }
        $RoomType = $Main->item(0)->getElementsByTagName("RoomType");
        if ($RoomType->length > 0) {
            $RoomType = $RoomType->item(0)->nodeValue;
        } else {
            $RoomType = "";
        }
        $RoomBasis = $Main->item(0)->getElementsByTagName("RoomBasis");
        if ($RoomBasis->length > 0) {
            $RoomBasis = $RoomBasis->item(0)->nodeValue;
        } else {
            $RoomBasis = "";
        }
        $ArrivalDate = $Main->item(0)->getElementsByTagName("ArrivalDate");
        if ($ArrivalDate->length > 0) {
            $ArrivalDate = $ArrivalDate->item(0)->nodeValue;
        } else {
            $ArrivalDate = "";
        }
        $CancellationDeadline = $Main->item(0)->getElementsByTagName("CancellationDeadline");
        if ($CancellationDeadline->length > 0) {
            $CancellationDeadline = $CancellationDeadline->item(0)->nodeValue;
        } else {
            $CancellationDeadline = "";
        }
        $Nights = $Main->item(0)->getElementsByTagName("Nights");
        if ($Nights->length > 0) {
            $Nights = $Nights->item(0)->nodeValue;
        } else {
            $Nights = "";
        }
        $NoAlternativeHotel = $Main->item(0)->getElementsByTagName("NoAlternativeHotel");
        if ($GoBookingCode->length > 0) {
            $NoAlternativeHotel = $NoAlternativeHotel->item(0)->nodeValue;
        } else {
            $NoAlternativeHotel = "";
        }
        $Remark = $Main->item(0)->getElementsByTagName("Remark");
        if ($Remark->length > 0) {
            $Remark = $Remark->item(0)->nodeValue;
        } else {
            $Remark = "";
        }
        $Leader = $Main->item(0)->getElementsByTagName("Leader");
        if ($Leader->length > 0) {
            $LeaderPersonID = $Leader->item(0)->getAttribute("LeaderPersonID");
        } else {
            $LeaderPersonID = "";
        }

        $Preferences = $Main->item(0)->getElementsByTagName("Preferences");
        if ($Preferences->length > 0) {
            $AdjoiningRooms = $Preferences->item(0)->getElementsByTagName("AdjoiningRooms");
            if ($AdjoiningRooms->length > 0) {
                $AdjoiningRooms = $AdjoiningRooms->item(0)->nodeValue;
            } else {
                $AdjoiningRooms = "";
            }
            $ConnectingRooms = $Preferences->item(0)->getElementsByTagName("ConnectingRooms");
            if ($ConnectingRooms->length > 0) {
                $ConnectingRooms = $ConnectingRooms->item(0)->nodeValue;
            } else {
                $ConnectingRooms = "";
            }
            $LateArrival = $Preferences->item(0)->getElementsByTagName("LateArrival");
            if ($LateArrival->length > 0) {
                $LateArrival = $LateArrival->item(0)->nodeValue;
            } else {
                $LateArrival = "";
            }
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('bookingInsert');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'GoBookingCode' => $GoBookingCode,
                'GoReference' => $GoReference,
                'ClientBookingCode' => $ClientBookingCode,
                'BookingStatus' => $BookingStatus,
                'TotalPrice' => $TotalPrice,
                'Currency' => $Currency,
                'HotelId' => $HotelId,
                'HotelName' => $HotelName,
                'HotelSearchCode' => $HotelSearchCode,
                'RoomType' => $RoomType,
                'RoomBasis' => $RoomBasis,
                'ArrivalDate' => $ArrivalDate,
                'CancellationDeadline' => $CancellationDeadline,
                'Nights' => $Nights,
                'NoAlternativeHotel' => $NoAlternativeHotel,
                'Remark' => $Remark,
                'LeaderPersonID' => $LeaderPersonID,
                'AdjoiningRooms' => $AdjoiningRooms,
                'ConnectingRooms' => $ConnectingRooms,
                'LateArrival' => $LateArrival
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO ROOM: " . $e;
            echo $return;
        }

        $Rooms = $Main->item(0)->getElementsByTagName("Rooms");
        if ($Rooms->length > 0) {
            $RoomType = $Rooms->item(0)->getElementsByTagName("RoomType");
            if ($RoomType->length > 0) {
                $Adults = $RoomType->item(0)->getAttribute("Adults");
                $Room = $RoomType->item(0)->getElementsByTagName("Room");
                if ($Room->length > 0) {
                    $RoomID = $Room->item(0)->getAttribute("RoomID");
                    $Category = $Room->item(0)->getAttribute("Category");

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('roomtype');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'Adults' => $Adults,
                            'RoomID' => $RoomID,
                            'Category' => $Category,
                            'GoBookingCode' => $GoBookingCode
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO ROOM: " . $e;
                        echo $return;
                    }

                    $PersonName = $Room->item(0)->getElementsByTagName("PersonName");
                    if ($PersonName->length > 0) {
                        for ($i=0; $i < $PersonName->length; $i++) { 
                            $PersonID = $PersonName->item(0)->getAttribute("PersonID");
                            $Title = $PersonName->item(0)->getAttribute("Title");
                            $FirstName = $PersonName->item(0)->getAttribute("FirstName");
                            $LastName = $PersonName->item(0)->getAttribute("LastName");

                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('roomtype_person');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'PersonID' => $PersonID,
                                    'Title' => $Title,
                                    'FirstName' => $FirstName,
                                    'LastName' => $LastName,
                                    'GoBookingCode' => $GoBookingCode
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            } catch (\Exception $e) {
                                echo $return;
                                echo "ERRO ROOM PERSON: " . $e;
                                echo $return;
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