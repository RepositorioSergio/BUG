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
echo "COMECOU RELEASE CABIN<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.convencional.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
$code = '38290196';
$agency = 'Costamar';
$password = 'C0sT2m2R';
$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Header>
    <Agency xmlns="http://schemas.costacrociere.com/WebAffiliation">
      <Code>38290196</Code>
    </Agency>
    <Partner xmlns="http://schemas.costacrociere.com/WebAffiliation">
      <Name>Costamar</Name>
      <Password>C0sT2m2R</Password>
    </Partner>
  </soap:Header>
  <soap:Body>
    <ReleaseCabin xmlns="http://schemas.costacrociere.com/WebAffiliation">
      <cruise>
        <Type>Cruise</Type>
        <Code>PA07201001</Code>
        <Cities>
          <City>
            <Code></Code>
            <Description></Description>
            <LocationType></LocationType>
            <AdditionalInfo xsi:nil="true" />
          </City>
          <City>
            <Code></Code>
            <Description></Description>
            <LocationType></LocationType>
            <AdditionalInfo xsi:nil="true" />
          </City>
        </Cities>
        <Fare>
          <Code>BASIC</Code>
          <Description>Basic</Description>
          <IsAllowedToBeChanged></IsAllowedToBeChanged>
          <AdditionalInfoRequired></AdditionalInfoRequired>
          <PromoFlights>
            <AvailablePromoFlights>true</AvailablePromoFlights>
            <PromoFlightsFees>true</PromoFlightsFees>
            <OnlyPromoFlight>true</OnlyPromoFlight>
            <FlightClass></FlightClass>
          </PromoFlights>
          <PromoBuses>
            <AvailablePromoBuses>true</AvailablePromoBuses>
          </PromoBuses>
          <SolutionIdAirPromo></SolutionIdAirPromo>
          <BestFlightPrice></BestFlightPrice>
          <SessionIdAirPromo></SessionIdAirPromo>
          <SolutionIdBusPromo></SolutionIdBusPromo>
          <BestBusPrice></BestBusPrice>
          <SessionIdBusPromo></SessionIdBusPromo>
          <Image></Image>
          <Label></Label>
          <Order></Order>
        </Fare>
        <Category>
          <Code>IV</Code>
          <Name>Internas</Name>
          <ShipCode></ShipCode>
          <Availability>true</Availability>
          <StatusCode>GA</StatusCode>
          <Price>
            <GuestPrice xsi:nil="true" />
            <GuestPrice xsi:nil="true" />
          </Price>
          <CabinLocation>Inside</CabinLocation>
          <Ship>
            <Code>PA</Code>
            <Name>Costa Pacifica</Name>
            <URL>http://</URL>
            <AdditionalInfo xsi:nil="true" />
            <Cabins>0</Cabins>
            <Crew>0</Crew>
            <Guests>0</Guests>
            <Width>0</Width>
            <Length>0</Length>
            <Tonnage>0</Tonnage>
            <MaxSpeed>0</MaxSpeed>
            <YearOfLaunch>0</YearOfLaunch>
            <MonthOfLaunch>0</MonthOfLaunch>
            <Description></Description>
            <LongDescription></LongDescription>
            <EBdescription></EBdescription>
            <Info></Info>
            <Categories xsi:nil="true" />
            <PublicAreas xsi:nil="true" />
            <AlternativeDescription></AlternativeDescription>
          </Ship>
          <URL>https://training.costaextra.it</URL>
          <AdditionalInfo>
            <WarningMessage></WarningMessage>
            <InfoMessage></InfoMessage>
          </AdditionalInfo>
          <IsSingleCabin>false</IsSingleCabin>
          <MaxOccupancy>4</MaxOccupancy>
          <MinOccupancy>1</MinOccupancy>
          <UpgradeCode></UpgradeCode>
          <Order>166</Order>
          <CabinAvailabilityInformation>
            <NumberOfCabins>1</NumberOfCabins>
          </CabinAvailabilityInformation>
          <AdditionalDescription></AdditionalDescription>
          <SuperCategoryType>1</SuperCategoryType>
          <CurrencyCode>USD</CurrencyCode>
          <Scores>
            <Score xsi:nil="true" />
            <Score xsi:nil="true" />
          </Scores>
        </Category>
        <Cabin>
          <Number>G00000</Number>
          <Category>
            <Code>IV</Code>
            <Name>Internas</Name>
            <ShipCode></ShipCode>
            <Availability>true</Availability>
            <StatusCode>GA</StatusCode>
            <Price xsi:nil="true" />
            <CabinLocation>Inside</CabinLocation>
            <Ship xsi:nil="true" />
            <URL>https://training.costaextra.it</URL>
            <AdditionalInfo xsi:nil="true" />
            <IsSingleCabin>false</IsSingleCabin>
            <MaxOccupancy>4</MaxOccupancy>
            <MinOccupancy>1</MinOccupancy>
            <UpgradeCode></UpgradeCode>
            <Order>0</Order>
            <CabinAvailabilityInformation xsi:nil="true" />
            <AdditionalDescription></AdditionalDescription>
            <SuperCategoryType>1</SuperCategoryType>
            <CurrencyCode>USD</CurrencyCode>
            <Scores xsi:nil="true" />
          </Category>
          <Status>GA</Status>
          <MinOccupancy>1</MinOccupancy>
          <MaxOccupancy>1</MaxOccupancy>
          <DeckName></DeckName>
          <DeckCode></DeckCode>
          <Beds>
            <Bed xsi:nil="true" />
            <Bed xsi:nil="true" />
          </Beds>
          <Facility>false</Facility>
          <DiningPreference>Unspecified</DiningPreference>
          <Cruise>
            <Code>PA07201001</Code>
            <Destination xsi:nil="true" />
            <Itinerary xsi:nil="true" />
            <DeparturePort xsi:nil="true" />
            <ArrivalPort xsi:nil="true" />
            <Description>PA07201001</Description>
            <Availability>true</Availability>
            <Sellability>true</Sellability>
            <DepartureDate>2020-10-01T02:00:00+02:00</DepartureDate>
            <Duration>7</Duration>
            <Ship xsi:nil="true" />
            <MaxOccupancy>5</MaxOccupancy>
            <AdditionalInfo xsi:nil="true" />
            <ImmediateConfirm xsi:nil="true" />
            <AirRemarks></AirRemarks>
            <CruiseRemarks></CruiseRemarks>
            <ShoppingByPriceData xsi:nil="true" />
          </Cruise>
          <URL></URL>
          <RateInformation>
            <MiscChargeAmt>0</MiscChargeAmt>
            <PortChargeAmt>0</PortChargeAmt>
          </RateInformation>
          <DiningWithInformation>
            <DiningWith xsi:nil="true" />
            <DiningWith xsi:nil="true" />
          </DiningWithInformation>
          <AdditionalInfo>
            <WarningMessage></WarningMessage>
            <InfoMessage></InfoMessage>
          </AdditionalInfo>
          <GuestsCabinInfo>
            <GuestCabinInfo xsi:nil="true" />
            <GuestCabinInfo xsi:nil="true" />
          </GuestsCabinInfo>
          <RestaurantInfo>
            <Code></Code>
            <TableSize></TableSize>
            <Description></Description>
            <DefaultRestaurant>true</DefaultRestaurant>
            <DiningInformation xsi:nil="true" />
          </RestaurantInfo>
          <DiningSatisfaction></DiningSatisfaction>
          <ServiceLevel></ServiceLevel>
        </Cabin>
        <Hotels>
          <Hotel>
            <Code></Code>
            <Description></Description>
            <RoomTypes xsi:nil="true" />
            <LongDescription></LongDescription>
            <CurrencyCode></CurrencyCode>
          </Hotel>
        </Hotels>
        <Insurance>false</Insurance>
        <InsuranceAvailableInd>false</InsuranceAvailableInd>
        <Mandatory>false</Mandatory>
        <Direction>None</Direction>
        <AdditionalInfo>
          <WarningMessage></WarningMessage>
          <InfoMessage></InfoMessage>
        </AdditionalInfo>
        <StatusCode></StatusCode>
        <ItemId></ItemId>
        <InsuranceType></InsuranceType>
        <ReferenceNumber>AAA0001</ReferenceNumber>
        <IsPromo></IsPromo>
        <FlightClasses>
          <FlightClass>
            <Code></Code>
            <GatewayCode></GatewayCode>
          </FlightClass>
        </FlightClasses>
      </cruise>
      <numberOfGuests>1</numberOfGuests>
    </ReleaseCabin>
  </soap:Body>
</soap:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "SOAPAction: http://schemas.costacrociere.com/WebAffiliation/ReleaseCabin",
    "Content-length: ".strlen($raw)
));
$url = "https://training.costaclick.net/WAWS_1_9/Booking.asmx";

$client->setUri($url);
$client->setMethod('POST');
$client->setRawBody($raw);
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
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.convencional.php');
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
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$ReleaseCabinResponse = $Body->item(0)->getElementsByTagName("ReleaseCabinResponse");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>