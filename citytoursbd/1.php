<?php
$xml = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <ServiceSearch xmlns="http://tempuri.org/"><OTA_TourActivitySearchAvailRQ AltLangID="en-us" Version="3.0" xmlns="http://www.opentravel.org/OTA/2003/05" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
	<POS>		<Source PseudoCityCode="NONE">
			<RequestorID ID="TESTID" Type="TD"/>
			<TPA_Extensions>
				<Provider xmlns="">
					<System>Test</System>
					<Userid>SCCM-024</Userid>
					<Password>844ed7pxi5</Password>	
					<AgencyCode>ATCM-024</AgencyCode>			
				</Provider>
			</TPA_Extensions>
		</Source>
	</POS>
	<SearchCriteria>
		<BasicInfo Name="" />
		<CategoryTypePref>
			<Type Code="" />
		</CategoryTypePref>
		<CustomerCounts Age="35" Quantity="1">
			<QualifierInfo>Adult</QualifierInfo>
		</CustomerCounts>
		<DateTimePref Start="2019-03-26" />
		<LocationPref>
			<Address>
				<CityName>New York City</CityName>
				<StateProv StateCode="NYC" />
				<CountryName Code="US" />
			</Address>
		</LocationPref>
	</SearchCriteria></OTA_TourActivitySearchAvailRQ> </ServiceSearch>
  </soap:Body>
</soap:Envelope>';
    
        $headers = array(
                        "Content-type: text/xml;charset=\"utf-8\"",
                        "Accept: text/xml",
                        "Cache-Control: no-cache",
                        "Pragma: no-cache",
                        "SOAPAction: http://tempuri.org/ServiceSearch",
                        "Content-length: ".strlen($xml),
                    ); //SOAPAction: your op URL

            $url = "http://services.staging.ct-hub.com/Servico.asmx?WSDL";

            // PHP cURL  for https connection with auth
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
echo 2;
            // converting
            $response = curl_exec($ch);
            curl_close($ch);
    echo $response;
