<?php 
$response = '<TransferValuedAvailRS xsi:schemaLocation="http://www.hotelbeds.com/schemas/2005/06/messages TransferValuedAvailRS.xsd" totalItems="12" echoToken="TransferValuedAvailRQ">
    <AuditData>
        <ProcessTime>7199</ProcessTime>
        <Timestamp>2016-08-22 16:40:55.903</Timestamp>
        <RequestHost>10.162.42.164</RequestHost>
        <ServerName>FORM</ServerName>
        <ServerId>FO</ServerId>
        <SchemaRelease>2005/06</SchemaRelease>
        <HydraCoreRelease>3.11.4.20160619</HydraCoreRelease>
        <HydraEnumerationsRelease>2#kCINFIi/Q8+f39DC51jisg</HydraEnumerationsRelease>
        <MerlinRelease>0</MerlinRelease>
    </AuditData>
    <ServiceTransfer xsi:type="ServiceTransfer" transferType="IN" availToken="blxaUbk45qzfNxHkr34ZAA==">
        <ContractList>
            <Contract>
                <Name>BUS W 16/18 AGP</Name>
                <IncomingOffice code="51"/>
            </Contract>
        </ContractList>
        <DateFrom date="20161016" time="0950"/>
        <Currency code="EUR"/>
        <TotalAmount>95.100</TotalAmount>
        <SellingPrice mandatory="N">95.100</SellingPrice>
        <TransferInfo xsi:type="ProductTransfer">
            <Code>0|0|0</Code>
            <DescriptionList>
                <Description type="GENERAL" languageCode="ENG">Private hire with driver</Description>
                <Description type="PRODUCT" languageCode="ENG">Premium product type</Description>
                <Description type="VEHICLE" languageCode="ENG">Bus</Description>
            </DescriptionList>
            <ImageList>
                <Image>
                    <Type>S</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/small/prvt-prm-bs.png</Url>
                </Image>
                <Image>
                    <Type>M</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/medium/prvt-prm-bs.png</Url>
                </Image>
                <Image>
                    <Type>L</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/large/prvt-prm-bs.png</Url>
                </Image>
                <Image>
                    <Type>XL</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/extralarge/prvt-prm-bs.png</Url>
                </Image>
            </ImageList>
            <Type code="P"/>
            <VehicleType code="0"/>
            <TransferSpecificContent id="1305">
                <GenericTransferGuidelinesList>
                    <TransferBulletPoint id="STFF">
                        <Description>CAN"T FIND STAFF </Description>
                        <DetailedDescription>In the event of being unable to locate a staff member, please call the emergency number indicated in this voucher.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="SPLU">
                        <Description>SPECIAL LUGGAGE</Description>
                        <DetailedDescription>In the event of extra luggage or sport equipment being checked in, please contact us, as this may carry an extra charge.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CHIN">
                        <Description>CHECK INFORMATION</Description>
                        <DetailedDescription>If the details do not correspond with the reservation, please contact your agency immediately.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="DRVR">
                        <Description>CANT FIND DRIVER</Description>
                        <DetailedDescription>In the event of being unable to locate the driver, please call the emergency number indicated in this voucher.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CBBS">
                        <Description>CHILDBOOSTER / BABY SEAT</Description>
                        <DetailedDescription>Child car seats and boosters are not included unless specified in your booking and can carry an extra cost. Should you need to book them, please contact your point of sale prior to travelling.</DetailedDescription>
                    </TransferBulletPoint>
                </GenericTransferGuidelinesList>
                <MaximumWaitingTimeSupplierDomestic time="60">minutes</MaximumWaitingTimeSupplierDomestic>
                <MaximumWaitingTimeSupplierInternational time="90">minutes</MaximumWaitingTimeSupplierInternational>
            </TransferSpecificContent>
        </TransferInfo>
        <Paxes>
            <AdultCount>1</AdultCount>
            <ChildCount>0</ChildCount>
        </Paxes>
        <PickupLocation xsi:type="ProductTransferTerminal">
            <Code>AGP</Code>
            <Name>Málaga, Málaga Costa del Sol Airport</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>AGP</Code>
            </TransferZone>
            <TerminalType>A</TerminalType>
        </PickupLocation>
        <DestinationLocation xsi:type="ProductTransferGPSPoint">
            <Code>MALAGA</Code>
            <Name>Malaga</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>MALAGA</Code>
            </TransferZone>
            <Coordinates latitude="36.7141056834158" longitude="-4.42281045239258"/>
            <Description>Med Playa Hotel Riviera</Description>
            <Address>Med Playa Hotel Riviera</Address>
            <City>Malaga</City>
            <ZipCode>12345</ZipCode>
            <Country>ES</Country>
        </DestinationLocation>
        <RetailPrice>95.100</RetailPrice>
        <ProductSpecifications>
            <MasterServiceType code="PRVT" name="Private"/>
            <MasterProductType code="PRM" name="Premium"/>
            <MasterVehicleType code="BS" name="Bus"/>
            <TransferGeneralInfoList>
                <TransferBulletPoint id="ER" order="1">
                    <Description>Exclusive ride for you</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="DTDS" order="2">
                    <Description>Door to door service</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="AV247" order="3">
                    <Description>Available 24/7</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="M&GS" order="4">
                    <Description>Meet & Greet service</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BA" order="10">
                    <Description>1 piece of baggage allowed per person</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BAHB" order="11">
                    <Description>1 item of hand baggage allowed per person</Description>
                </TransferBulletPoint>
            </TransferGeneralInfoList>
        </ProductSpecifications>
        <TransferPickupInformation>
            <Description>Once you have collected your luggage, a staff member will be waiting for you at the Arrivals Hall with a sign with your name on it. If you are unable to locate the driver/agent, please call DESTINATION SERVICES on +34 672610274. Languages spoken at the call centre: English, Spanish.</Description>
        </TransferPickupInformation>
        <ArrivalTravelInfo>
            <ArrivalInfo xsi:type="ProductTransferTerminal">
                <Code>AGP</Code>
                <Name>Málaga, Málaga Costa del Sol Airport</Name>
                <TransferZone xsi:type="ProductZone">
                    <Code>AGP</Code>
                </TransferZone>
                <DateTime date="20161016" time="0950"/>
                <TerminalType>A</TerminalType>
            </ArrivalInfo>
        </ArrivalTravelInfo>
    </ServiceTransfer>
    <ServiceTransfer xsi:type="ServiceTransfer" transferType="IN" availToken="blxaUbk45qzfNxHkr34ZAA==">
        <ContractList>
            <Contract>
                <Name>S W 16/18 AGP</Name>
                <IncomingOffice code="51"/>
            </Contract>
        </ContractList>
        <DateFrom date="20161016" time="0950"/>
        <Currency code="EUR"/>
        <TotalAmount>6.850</TotalAmount>
        <SellingPrice mandatory="N">6.850</SellingPrice>
        <TransferInfo xsi:type="ProductTransfer">
            <Code>0|0|1</Code>
            <DescriptionList>
                <Description type="GENERAL" languageCode="ENG">With other passengers</Description>
                <Description type="PRODUCT" languageCode="ENG">Standard product type</Description>
                <Description type="VEHICLE" languageCode="ENG">Bus</Description>
            </DescriptionList>
            <ImageList>
                <Image>
                    <Type>S</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/small/shrd-stnd-bs.png</Url>
                </Image>
                <Image>
                    <Type>M</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/medium/shrd-stnd-bs.png</Url>
                </Image>
                <Image>
                    <Type>L</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/large/shrd-stnd-bs.png</Url>
                </Image>
                <Image>
                    <Type>XL</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/extralarge/shrd-stnd-bs.png</Url>
                </Image>
            </ImageList>
            <Type code="B"/>
            <VehicleType code="S"/>
            <TransferSpecificContent id="783">
                <GenericTransferGuidelinesList>
                    <TransferBulletPoint id="SPLU">
                        <Description>SPECIAL LUGGAGE</Description>
                        <DetailedDescription>In the event of extra luggage or sport equipment being checked in, please contact us, as this may carry an extra charge.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="LGPR">
                        <Description>LUGGAGE PROBLEMS</Description>
                        <DetailedDescription>In the event of a problem with customs or luggage,  please call the emergency number in order to advise of the delay and take the necessary steps.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="MTPT">
                        <Description>CANT FIND MEETING POINT</Description>
                        <DetailedDescription>In the event of being unable to locate the meeting point, please call the emergency number indicated in this voucher.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CHIN">
                        <Description>CHECK INFORMATION</Description>
                        <DetailedDescription>If the details do not correspond with the reservation, please contact your agency immediately.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CBBS">
                        <Description>CHILDBOOSTER / BABY SEAT</Description>
                        <DetailedDescription>Child car seats and boosters are not included unless specified in your booking and can carry an extra cost. Should you need to book them, please contact your point of sale prior to travelling.</DetailedDescription>
                    </TransferBulletPoint>
                </GenericTransferGuidelinesList>
                <MaximumWaitingTime time="60">minutes</MaximumWaitingTime>
                <MaximumWaitingTimeSupplierDomestic time="60">minutes</MaximumWaitingTimeSupplierDomestic>
                <MaximumWaitingTimeSupplierInternational time="90">minutes</MaximumWaitingTimeSupplierInternational>
                <MaximumNumberStops maxstops="13"/>
            </TransferSpecificContent>
        </TransferInfo>
        <Paxes>
            <AdultCount>1</AdultCount>
            <ChildCount>0</ChildCount>
        </Paxes>
        <PickupLocation xsi:type="ProductTransferTerminal">
            <Code>AGP</Code>
            <Name>Málaga, Málaga Costa del Sol Airport</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>AGP</Code>
            </TransferZone>
            <TerminalType>A</TerminalType>
        </PickupLocation>
        <DestinationLocationList>
            <TransferLocation xsi:type="ProductTransferHotel">
                <Code>14179</Code>
                <Name>Soho Bahía Málaga</Name>
                <TransferZone xsi:type="ProductZone">
                    <Code>MALAGA</Code>
                </TransferZone>
                <BestMatchOrder>1</BestMatchOrder>
                <LocationInformation distance="167">
                    <Address>SOMERA</Address>
                    <Number>8</Number>
                    <Town>MALAGA</Town>
                    <Zip>29001</Zip>
                    <GPSPoint latitude="36.7154941010865" longitude="-4.42355498670167"/>
                    <Description>Soho Bahía Málaga</Description>
                </LocationInformation>
            </TransferLocation>
            <TransferLocation xsi:type="ProductTransferHotel">
                <Code>69543</Code>
                <Name>Room Mate Lola</Name>
                <TransferZone xsi:type="ProductZone">
                    <Code>MALAGA</Code>
                </TransferZone>
                <BestMatchOrder>2</BestMatchOrder>
                <LocationInformation distance="210">
                    <Address>CASAS DE CAMPOS</Address>
                    <Number>17</Number>
                    <Town>MALAGA</Town>
                    <Zip>29001</Zip>
                    <GPSPoint latitude="36.715921969059956" longitude="-4.4234745204312276"/>
                    <Description>Room Mate Lola</Description>
                </LocationInformation>
            </TransferLocation>
            <TransferLocation xsi:type="ProductTransferHotel">
                <Code>394301</Code>
                <Name>LIFE APARTMENTS ALAMEDA COLON</Name>
                <TransferZone xsi:type="ProductZone">
                    <Code>MALAGA</Code>
                </TransferZone>
                <BestMatchOrder>3</BestMatchOrder>
                <LocationInformation distance="258">
                    <Address>ALAMEDA COLON</Address>
                    <Number>7</Number>
                    <Town>Málaga</Town>
                    <Zip>29001</Zip>
                    <GPSPoint latitude="36.715806" longitude="-4.424795"/>
                    <Description>LIFE APARTMENTS ALAMEDA COLON</Description>
                </LocationInformation>
            </TransferLocation>
        </DestinationLocationList>
        <RetailPrice>6.850</RetailPrice>
        <ProductSpecifications>
            <MasterServiceType code="SHRD" name="Shared - Shuttle"/>
            <MasterProductType code="STND" name="Standard"/>
            <MasterVehicleType code="BS" name="Bus"/>
            <TransferGeneralInfoList>
                <TransferBulletPoint id="BA" order="9">
                    <Description>1 piece of baggage allowed per person</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BAHB" order="10">
                    <Description>1 item of hand baggage allowed per person</Description>
                </TransferBulletPoint>
            </TransferGeneralInfoList>
        </ProductSpecifications>
        <TransferPickupInformation>
            <Description>Once you have collected your luggage, a staff member will be waiting for you at the airport exit with a sign with your name on it. If you are unable to locate the driver/agent, please call DESTINATION SERVICES on +34 672610274 .Languages spoken at the call centre: English, Spanish.</Description>
        </TransferPickupInformation>
        <ArrivalTravelInfo>
            <ArrivalInfo xsi:type="ProductTransferTerminal">
                <Code>AGP</Code>
                <Name>Málaga, Málaga Costa del Sol Airport</Name>
                <TransferZone xsi:type="ProductZone">
                    <Code>AGP</Code>
                </TransferZone>
                <DateTime date="20161016" time="0950"/>
                <TerminalType>A</TerminalType>
            </ArrivalInfo>
        </ArrivalTravelInfo>
    </ServiceTransfer>
    <ServiceTransfer xsi:type="ServiceTransfer" transferType="IN" availToken="blxaUbk45qzfNxHkr34ZAA==">
        <ContractList>
            <Contract>
                <Name>P W 16/18 AGP</Name>
                <IncomingOffice code="51"/>
            </Contract>
        </ContractList>
        <DateFrom date="20161016" time="0950"/>
        <Currency code="EUR"/>
        <TotalAmount>71.860</TotalAmount>
        <SellingPrice mandatory="N">71.860</SellingPrice>
        <TransferInfo xsi:type="ProductTransfer">
            <Code>0|0|2</Code>
            <DescriptionList>
                <Description type="GENERAL" languageCode="ENG">Private hire with driver</Description>
                <Description type="PRODUCT" languageCode="ENG">Premium product type</Description>
                <Description type="VEHICLE" languageCode="ENG">Bus bicycle carriage included</Description>
            </DescriptionList>
            <ImageList>
                <Image>
                    <Type>S</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/small/prvt-prm-bseb.png</Url>
                </Image>
                <Image>
                    <Type>M</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/medium/prvt-prm-bseb.png</Url>
                </Image>
                <Image>
                    <Type>L</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/large/prvt-prm-bseb.png</Url>
                </Image>
                <Image>
                    <Type>XL</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/extralarge/prvt-prm-bseb.png</Url>
                </Image>
            </ImageList>
            <Type code="P"/>
            <VehicleType code="1"/>
            <TransferSpecificContent id="1529">
                <GenericTransferGuidelinesList>
                    <TransferBulletPoint id="DRVR">
                        <Description>CANT FIND DRIVER</Description>
                        <DetailedDescription>In the event of being unable to locate the driver, please call the emergency number indicated in this voucher.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CHIN">
                        <Description>CHECK INFORMATION</Description>
                        <DetailedDescription>If the details do not correspond with the reservation, please contact your agency immediately.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CBBS">
                        <Description>CHILDBOOSTER / BABY SEAT</Description>
                        <DetailedDescription>Child car seats and boosters are not included unless specified in your booking and can carry an extra cost. Should you need to book them, please contact your point of sale prior to travelling.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="EBIKE">
                        <Description>EXTRA BICYCLES</Description>
                        <DetailedDescription>Bicycles need to arrive in boxes 178 x 87 x 20 cm</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="EXLU">
                        <Description>EXCESS LUGGAGE</Description>
                        <DetailedDescription>If you arrive at the destination with an excess of luggage, you will have to pay an additional charge for the extra undeclared weight. </DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="VOUC">
                        <Description>VOUCHER </Description>
                        <DetailedDescription>Remember to bring this voucher and valid photo ID with you</DetailedDescription>
                    </TransferBulletPoint>
                </GenericTransferGuidelinesList>
                <SpecificTransferInfoList>
                    <TransferBulletPoint xsi:type="TransferBulletPointNumber" id="BAEP" order="1">
                        <Description>Extra bicycle allowed per person</Description>
                        <Value>1</Value>
                        <Metric>Bicycle/s</Metric>
                    </TransferBulletPoint>
                </SpecificTransferInfoList>
                <MaximumWaitingTimeSupplierDomestic time="60">minutes</MaximumWaitingTimeSupplierDomestic>
                <MaximumWaitingTimeSupplierInternational time="90">minutes</MaximumWaitingTimeSupplierInternational>
            </TransferSpecificContent>
        </TransferInfo>
        <Paxes>
            <AdultCount>1</AdultCount>
            <ChildCount>0</ChildCount>
        </Paxes>
        <PickupLocation xsi:type="ProductTransferTerminal">
            <Code>AGP</Code>
            <Name>Málaga, Málaga Costa del Sol Airport</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>AGP</Code>
            </TransferZone>
            <TerminalType>A</TerminalType>
        </PickupLocation>
        <DestinationLocation xsi:type="ProductTransferGPSPoint">
            <Code>MALAGA</Code>
            <Name>Malaga</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>MALAGA</Code>
            </TransferZone>
            <Coordinates latitude="36.7141056834158" longitude="-4.42281045239258"/>
            <Description>Med Playa Hotel Riviera</Description>
            <Address>Med Playa Hotel Riviera</Address>
            <City>Malaga</City>
            <ZipCode>12345</ZipCode>
            <Country>ES</Country>
        </DestinationLocation>
        <RetailPrice>71.860</RetailPrice>
        <ProductSpecifications>
            <MasterServiceType code="PRVT" name="Private"/>
            <MasterProductType code="PRM" name="Premium"/>
            <MasterVehicleType code="BSEB" name="Bus bicycle carriage included"/>
            <TransferGeneralInfoList>
                <TransferBulletPoint id="ER" order="1">
                    <Description>Exclusive ride for you</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="DTDS" order="2">
                    <Description>Door to door service</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="AV247" order="3">
                    <Description>Available 24/7</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="M&GS" order="4">
                    <Description>Meet & Greet service</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BA" order="10">
                    <Description>1 piece of baggage allowed per person</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BAHB" order="11">
                    <Description>1 item of hand baggage allowed per person</Description>
                </TransferBulletPoint>
            </TransferGeneralInfoList>
        </ProductSpecifications>
        <TransferPickupInformation>
            <Description>Once you have collected your luggage, a staff member will be waiting for you at the Arrivals Hall with a sign with your name on it. If you are unable to locate the driver/agent, please call DESTINATION SERVICES on +34 672610274. Languages spoken at the call centre: English, Spanish.</Description>
        </TransferPickupInformation>
        <ArrivalTravelInfo>
            <ArrivalInfo xsi:type="ProductTransferTerminal">
                <Code>AGP</Code>
                <Name>Málaga, Málaga Costa del Sol Airport</Name>
                <TransferZone xsi:type="ProductZone">
                    <Code>AGP</Code>
                </TransferZone>
                <DateTime date="20161016" time="0950"/>
                <TerminalType>A</TerminalType>
            </ArrivalInfo>
        </ArrivalTravelInfo>
    </ServiceTransfer>
    <ServiceTransfer xsi:type="ServiceTransfer" transferType="IN" availToken="blxaUbk45qzfNxHkr34ZAA==">
        <ContractList>
            <Contract>
                <Name>P W 16/18 AGP</Name>
                <IncomingOffice code="51"/>
            </Contract>
        </ContractList>
        <DateFrom date="20161016" time="0950"/>
        <Currency code="EUR"/>
        <TotalAmount>20.840</TotalAmount>
        <SellingPrice mandatory="N">20.840</SellingPrice>
        <TransferInfo xsi:type="ProductTransfer">
            <Code>0|0|3</Code>
            <DescriptionList>
                <Description type="GENERAL" languageCode="ENG">Private hire with driver</Description>
                <Description type="PRODUCT" languageCode="ENG">Premium product type</Description>
                <Description type="VEHICLE" languageCode="ENG">Car</Description>
            </DescriptionList>
            <ImageList>
                <Image>
                    <Type>S</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/small/prvt-prm-cr.png</Url>
                </Image>
                <Image>
                    <Type>M</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/medium/prvt-prm-cr.png</Url>
                </Image>
                <Image>
                    <Type>L</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/large/prvt-prm-cr.png</Url>
                </Image>
                <Image>
                    <Type>XL</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/extralarge/prvt-prm-cr.png</Url>
                </Image>
            </ImageList>
            <Type code="P"/>
            <VehicleType code="C"/>
            <TransferSpecificContent id="1462">
                <GenericTransferGuidelinesList>
                    <TransferBulletPoint id="SPLU">
                        <Description>SPECIAL LUGGAGE</Description>
                        <DetailedDescription>In the event of extra luggage or sport equipment being checked in, please contact us, as this may carry an extra charge.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CBBS">
                        <Description>CHILDBOOSTER / BABY SEAT</Description>
                        <DetailedDescription>Child car seats and boosters are not included unless specified in your booking and can carry an extra cost. Should you need to book them, please contact your point of sale prior to travelling.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="LGPR">
                        <Description>LUGGAGE PROBLEMS</Description>
                        <DetailedDescription>In the event of a problem with customs or luggage,  please call the emergency number in order to advise of the delay and take the necessary steps.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CHIN">
                        <Description>CHECK INFORMATION</Description>
                        <DetailedDescription>If the details do not correspond with the reservation, please contact your agency immediately.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="DRVR">
                        <Description>CANT FIND DRIVER</Description>
                        <DetailedDescription>In the event of being unable to locate the driver, please call the emergency number indicated in this voucher.</DetailedDescription>
                    </TransferBulletPoint>
                </GenericTransferGuidelinesList>
                <MaximumWaitingTimeSupplierDomestic time="60">minutes</MaximumWaitingTimeSupplierDomestic>
                <MaximumWaitingTimeSupplierInternational time="90">minutes</MaximumWaitingTimeSupplierInternational>
            </TransferSpecificContent>
        </TransferInfo>
        <Paxes>
            <AdultCount>1</AdultCount>
            <ChildCount>0</ChildCount>
        </Paxes>
        <PickupLocation xsi:type="ProductTransferTerminal">
            <Code>AGP</Code>
            <Name>Málaga, Málaga Costa del Sol Airport</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>AGP</Code>
            </TransferZone>
            <TerminalType>A</TerminalType>
        </PickupLocation>
        <DestinationLocation xsi:type="ProductTransferGPSPoint">
            <Code>MALAGA</Code>
            <Name>Malaga</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>MALAGA</Code>
            </TransferZone>
            <Coordinates latitude="36.7141056834158" longitude="-4.42281045239258"/>
            <Description>Med Playa Hotel Riviera</Description>
            <Address>Med Playa Hotel Riviera</Address>
            <City>Malaga</City>
            <ZipCode>12345</ZipCode>
            <Country>ES</Country>
        </DestinationLocation>
        <RetailPrice>20.840</RetailPrice>
        <ProductSpecifications>
            <MasterServiceType code="PRVT" name="Private"/>
            <MasterProductType code="PRM" name="Premium"/>
            <MasterVehicleType code="CR" name="Car"/>
            <TransferGeneralInfoList>
                <TransferBulletPoint id="ER" order="1">
                    <Description>Exclusive ride for you</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="DTDS" order="2">
                    <Description>Door to door service</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="AV247" order="3">
                    <Description>Available 24/7</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="M&GS" order="4">
                    <Description>Meet & Greet service</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BA" order="10">
                    <Description>1 piece of baggage allowed per person</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BAHB" order="11">
                    <Description>1 item of hand baggage allowed per person</Description>
                </TransferBulletPoint>
            </TransferGeneralInfoList>
        </ProductSpecifications>
        <TransferPickupInformation>
            <Description>Once you have collected your luggage, a staff member will be waiting for you at the Arrivals Hall with a sign with your name on it. If you are unable to locate the driver/agent, please call DESTINATION SERVICES on +34 672610274. Languages spoken at the call centre: English, Spanish.</Description>
        </TransferPickupInformation>
        <ArrivalTravelInfo>
            <ArrivalInfo xsi:type="ProductTransferTerminal">
                <Code>AGP</Code>
                <Name>Málaga, Málaga Costa del Sol Airport</Name>
                <TransferZone xsi:type="ProductZone">
                    <Code>AGP</Code>
                </TransferZone>
                <DateTime date="20161016" time="0950"/>
                <TerminalType>A</TerminalType>
            </ArrivalInfo>
        </ArrivalTravelInfo>
    </ServiceTransfer>
    <ServiceTransfer xsi:type="ServiceTransfer" transferType="IN" availToken="blxaUbk45qzfNxHkr34ZAA==">
        <ContractList>
            <Contract>
                <Name>P W 16/18 AGP</Name>
                <IncomingOffice code="51"/>
            </Contract>
        </ContractList>
        <DateFrom date="20161016" time="0950"/>
        <Currency code="EUR"/>
        <TotalAmount>28.090</TotalAmount>
        <SellingPrice mandatory="N">28.090</SellingPrice>
        <TransferInfo xsi:type="ProductTransfer">
            <Code>0|0|4</Code>
            <DescriptionList>
                <Description type="GENERAL" languageCode="ENG">Private hire with driver</Description>
                <Description type="PRODUCT" languageCode="ENG">Premium product type</Description>
                <Description type="VEHICLE" languageCode="ENG">Minivan</Description>
            </DescriptionList>
            <ImageList>
                <Image>
                    <Type>S</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/small/prvt-prm-mv.png</Url>
                </Image>
                <Image>
                    <Type>M</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/medium/prvt-prm-mv.png</Url>
                </Image>
                <Image>
                    <Type>L</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/large/prvt-prm-mv.png</Url>
                </Image>
                <Image>
                    <Type>XL</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/extralarge/prvt-prm-mv.png</Url>
                </Image>
            </ImageList>
            <Type code="P"/>
            <VehicleType code="V"/>
            <TransferSpecificContent id="1304">
                <GenericTransferGuidelinesList>
                    <TransferBulletPoint id="SPLU">
                        <Description>SPECIAL LUGGAGE</Description>
                        <DetailedDescription>In the event of extra luggage or sport equipment being checked in, please contact us, as this may carry an extra charge.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CHIN">
                        <Description>CHECK INFORMATION</Description>
                        <DetailedDescription>If the details do not correspond with the reservation, please contact your agency immediately.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="DRVR">
                        <Description>CANT FIND DRIVER</Description>
                        <DetailedDescription>In the event of being unable to locate the driver, please call the emergency number indicated in this voucher.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="LGPR">
                        <Description>LUGGAGE PROBLEMS</Description>
                        <DetailedDescription>In the event of a problem with customs or luggage,  please call the emergency number in order to advise of the delay and take the necessary steps.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CBBS">
                        <Description>CHILDBOOSTER / BABY SEAT</Description>
                        <DetailedDescription>Child car seats and boosters are not included unless specified in your booking and can carry an extra cost. Should you need to book them, please contact your point of sale prior to travelling.</DetailedDescription>
                    </TransferBulletPoint>
                </GenericTransferGuidelinesList>
                <MaximumWaitingTimeSupplierDomestic time="60">minutes</MaximumWaitingTimeSupplierDomestic>
                <MaximumWaitingTimeSupplierInternational time="90">minutes</MaximumWaitingTimeSupplierInternational>
            </TransferSpecificContent>
        </TransferInfo>
        <Paxes>
            <AdultCount>1</AdultCount>
            <ChildCount>0</ChildCount>
        </Paxes>
        <PickupLocation xsi:type="ProductTransferTerminal">
            <Code>AGP</Code>
            <Name>Málaga, Málaga Costa del Sol Airport</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>AGP</Code>
            </TransferZone>
            <TerminalType>A</TerminalType>
        </PickupLocation>
        <DestinationLocation xsi:type="ProductTransferGPSPoint">
            <Code>MALAGA</Code>
            <Name>Malaga</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>MALAGA</Code>
            </TransferZone>
            <Coordinates latitude="36.7141056834158" longitude="-4.42281045239258"/>
            <Description>Med Playa Hotel Riviera</Description>
            <Address>Med Playa Hotel Riviera</Address>
            <City>Malaga</City>
            <ZipCode>12345</ZipCode>
            <Country>ES</Country>
        </DestinationLocation>
        <RetailPrice>28.090</RetailPrice>
        <ProductSpecifications>
            <MasterServiceType code="PRVT" name="Private"/>
            <MasterProductType code="PRM" name="Premium"/>
            <MasterVehicleType code="MV" name="Minivan"/>
            <TransferGeneralInfoList>
                <TransferBulletPoint id="ER" order="1">
                    <Description>Exclusive ride for you</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="DTDS" order="2">
                    <Description>Door to door service</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="AV247" order="3">
                    <Description>Available 24/7</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="M&GS" order="4">
                    <Description>Meet & Greet service</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BA" order="10">
                    <Description>1 piece of baggage allowed per person</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BAHB" order="11">
                    <Description>1 item of hand baggage allowed per person</Description>
                </TransferBulletPoint>
            </TransferGeneralInfoList>
        </ProductSpecifications>
        <TransferPickupInformation>
            <Description>Once you have collected your luggage, a staff member will be waiting for you at the Arrivals Hall with a sign with your name on it. If you are unable to locate the driver/agent, please call DESTINATION SERVICES on +34 672610274. Languages spoken at the call centre: English, Spanish.</Description>
        </TransferPickupInformation>
        <ArrivalTravelInfo>
            <ArrivalInfo xsi:type="ProductTransferTerminal">
                <Code>AGP</Code>
                <Name>Málaga, Málaga Costa del Sol Airport</Name>
                <TransferZone xsi:type="ProductZone">
                    <Code>AGP</Code>
                </TransferZone>
                <DateTime date="20161016" time="0950"/>
                <TerminalType>A</TerminalType>
            </ArrivalInfo>
        </ArrivalTravelInfo>
    </ServiceTransfer>
    <ServiceTransfer xsi:type="ServiceTransfer" transferType="IN" availToken="blxaUbk45qzfNxHkr34ZAA==">
        <ContractList>
            <Contract>
                <Name>P W 16/18 AGP</Name>
                <IncomingOffice code="51"/>
            </Contract>
        </ContractList>
        <DateFrom date="20161016" time="0950"/>
        <Currency code="EUR"/>
        <TotalAmount>36.560</TotalAmount>
        <SellingPrice mandatory="N">36.560</SellingPrice>
        <TransferInfo xsi:type="ProductTransfer">
            <Code>0|0|5</Code>
            <DescriptionList>
                <Description type="GENERAL" languageCode="ENG">Private hire with driver</Description>
                <Description type="PRODUCT" languageCode="ENG">Special product type</Description>
                <Description type="VEHICLE" languageCode="ENG">Disabled</Description>
            </DescriptionList>
            <ImageList>
                <Image>
                    <Type>S</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/small/prvt-spcl-dsbld.png</Url>
                </Image>
                <Image>
                    <Type>M</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/medium/prvt-spcl-dsbld.png</Url>
                </Image>
                <Image>
                    <Type>L</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/large/prvt-spcl-dsbld.png</Url>
                </Image>
                <Image>
                    <Type>XL</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/extralarge/prvt-spcl-dsbld.png</Url>
                </Image>
            </ImageList>
            <Type code="P"/>
            <VehicleType code="H"/>
            <TransferSpecificContent id="1426">
                <GenericTransferGuidelinesList>
                    <TransferBulletPoint id="SPLU">
                        <Description>SPECIAL LUGGAGE</Description>
                        <DetailedDescription>In the event of extra luggage or sport equipment being checked in, please contact us, as this may carry an extra charge.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="LGPR">
                        <Description>LUGGAGE PROBLEMS</Description>
                        <DetailedDescription>In the event of a problem with customs or luggage,  please call the emergency number in order to advise of the delay and take the necessary steps.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="MTPT">
                        <Description>CANT FIND MEETING POINT</Description>
                        <DetailedDescription>In the event of being unable to locate the meeting point, please call the emergency number indicated in this voucher.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CBBS">
                        <Description>CHILDBOOSTER / BABY SEAT</Description>
                        <DetailedDescription>Child car seats and boosters are not included unless specified in your booking and can carry an extra cost. Should you need to book them, please contact your point of sale prior to travelling.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CHIN">
                        <Description>CHECK INFORMATION</Description>
                        <DetailedDescription>If the details do not correspond with the reservation, please contact your agency immediately.</DetailedDescription>
                    </TransferBulletPoint>
                </GenericTransferGuidelinesList>
                <MaximumWaitingTimeSupplierDomestic time="60">minutes</MaximumWaitingTimeSupplierDomestic>
                <MaximumWaitingTimeSupplierInternational time="90">minutes</MaximumWaitingTimeSupplierInternational>
            </TransferSpecificContent>
        </TransferInfo>
        <Paxes>
            <AdultCount>1</AdultCount>
            <ChildCount>0</ChildCount>
        </Paxes>
        <PickupLocation xsi:type="ProductTransferTerminal">
            <Code>AGP</Code>
            <Name>Málaga, Málaga Costa del Sol Airport</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>AGP</Code>
            </TransferZone>
            <TerminalType>A</TerminalType>
        </PickupLocation>
        <DestinationLocation xsi:type="ProductTransferGPSPoint">
            <Code>MALAGA</Code>
            <Name>Malaga</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>MALAGA</Code>
            </TransferZone>
            <Coordinates latitude="36.7141056834158" longitude="-4.42281045239258"/>
            <Description>Med Playa Hotel Riviera</Description>
            <Address>Med Playa Hotel Riviera</Address>
            <City>Malaga</City>
            <ZipCode>12345</ZipCode>
            <Country>ES</Country>
        </DestinationLocation>
        <RetailPrice>36.560</RetailPrice>
        <ProductSpecifications>
            <MasterServiceType code="PRVT" name="Private"/>
            <MasterProductType code="SPCL" name="Special"/>
            <MasterVehicleType code="DSBLD" name="Disabled"/>
            <TransferGeneralInfoList>
                <TransferBulletPoint id="ER" order="1">
                    <Description>Exclusive ride for you</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="DTDS" order="2">
                    <Description>Door to door service</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="AV247" order="3">
                    <Description>Available 24/7</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BA" order="9">
                    <Description>1 piece of baggage allowed per person</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BAHB" order="10">
                    <Description>1 item of hand baggage allowed per person</Description>
                </TransferBulletPoint>
            </TransferGeneralInfoList>
        </ProductSpecifications>
        <TransferPickupInformation>
            <Description>Once you have collected your luggage, a staff member will be waiting for you at the Arrivals Hall with a sign with your name on it. If you are unable to locate the driver/agent, please call DESTINATION SERVICES on +34 672610274. Languages spoken at the call centre: English, Spanish.</Description>
        </TransferPickupInformation>
        <ArrivalTravelInfo>
            <ArrivalInfo xsi:type="ProductTransferTerminal">
                <Code>AGP</Code>
                <Name>Málaga, Málaga Costa del Sol Airport</Name>
                <TransferZone xsi:type="ProductZone">
                    <Code>AGP</Code>
                </TransferZone>
                <DateTime date="20161016" time="0950"/>
                <TerminalType>A</TerminalType>
            </ArrivalInfo>
        </ArrivalTravelInfo>
    </ServiceTransfer>
    <ServiceTransfer xsi:type="ServiceTransfer" transferType="OUT" availToken="blxaUbk45qzfNxHkr34ZAA==">
        <ContractList>
            <Contract>
                <Name>BUS W 16/18 AGP</Name>
                <IncomingOffice code="51"/>
            </Contract>
        </ContractList>
        <DateFrom date="20161023" time="1055"/>
        <Currency code="EUR"/>
        <TotalAmount>95.100</TotalAmount>
        <SellingPrice mandatory="N">95.100</SellingPrice>
        <TransferInfo xsi:type="ProductTransfer">
            <Code>1|0|0</Code>
            <DescriptionList>
                <Description type="GENERAL" languageCode="ENG">Private hire with driver</Description>
                <Description type="PRODUCT" languageCode="ENG">Premium product type</Description>
                <Description type="VEHICLE" languageCode="ENG">Bus</Description>
            </DescriptionList>
            <ImageList>
                <Image>
                    <Type>S</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/small/prvt-prm-bs.png</Url>
                </Image>
                <Image>
                    <Type>M</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/medium/prvt-prm-bs.png</Url>
                </Image>
                <Image>
                    <Type>L</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/large/prvt-prm-bs.png</Url>
                </Image>
                <Image>
                    <Type>XL</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/extralarge/prvt-prm-bs.png</Url>
                </Image>
            </ImageList>
            <Type code="P"/>
            <VehicleType code="0"/>
            <TransferSpecificContent id="1305">
                <GenericTransferGuidelinesList>
                    <TransferBulletPoint id="SPLU">
                        <Description>SPECIAL LUGGAGE</Description>
                        <DetailedDescription>In the event of extra luggage or sport equipment being checked in, please contact us, as this may carry an extra charge.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CHIN">
                        <Description>CHECK INFORMATION</Description>
                        <DetailedDescription>If the details do not correspond with the reservation, please contact your agency immediately.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="DRVR">
                        <Description>CANT FIND DRIVER</Description>
                        <DetailedDescription>In the event of being unable to locate the driver, please call the emergency number indicated in this voucher.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CBBS">
                        <Description>CHILDBOOSTER / BABY SEAT</Description>
                        <DetailedDescription>Child car seats and boosters are not included unless specified in your booking and can carry an extra cost. Should you need to book them, please contact your point of sale prior to travelling.</DetailedDescription>
                    </TransferBulletPoint>
                </GenericTransferGuidelinesList>
                <MaximumWaitingTime time="30">minutes</MaximumWaitingTime>
                <MaximumWaitingTimeSupplierDomestic time="15">minutes</MaximumWaitingTimeSupplierDomestic>
            </TransferSpecificContent>
        </TransferInfo>
        <Paxes>
            <AdultCount>1</AdultCount>
            <ChildCount>0</ChildCount>
        </Paxes>
        <PickupLocation xsi:type="ProductTransferGPSPoint">
            <Code>MALAGA</Code>
            <Name>Malaga</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>MALAGA</Code>
            </TransferZone>
            <Coordinates latitude="36.7141056834158" longitude="-4.42281045239258"/>
            <Description>Med Playa Hotel Riviera</Description>
            <Address>Med Playa Hotel Riviera</Address>
            <City>Malaga</City>
            <ZipCode>12345</ZipCode>
            <Country>ES</Country>
        </PickupLocation>
        <DestinationLocation xsi:type="ProductTransferTerminal">
            <Code>AGP</Code>
            <Name>Málaga, Málaga Costa del Sol Airport</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>AGP</Code>
            </TransferZone>
            <TerminalType>A</TerminalType>
        </DestinationLocation>
        <RetailPrice>95.100</RetailPrice>
        <EstimatedTransferDuration>15</EstimatedTransferDuration>
        <ProductSpecifications>
            <MasterServiceType code="PRVT" name="Private"/>
            <MasterProductType code="PRM" name="Premium"/>
            <MasterVehicleType code="BS" name="Bus"/>
            <TransferGeneralInfoList>
                <TransferBulletPoint id="ER" order="1">
                    <Description>Exclusive ride for you</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="DTDS" order="2">
                    <Description>Door to door service</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="AV247" order="3">
                    <Description>Available 24/7</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="M&GS" order="4">
                    <Description>Meet & Greet service</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BA" order="10">
                    <Description>1 piece of baggage allowed per person</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BAHB" order="11">
                    <Description>1 item of hand baggage allowed per person</Description>
                </TransferBulletPoint>
            </TransferGeneralInfoList>
        </ProductSpecifications>
        <TransferPickupInformation>
            <Description>We will pick you up at the address indicated when making your reservation. If the vehicle is unable to access this spot (pedestrian zone, etc.) then please get in contact with us.</Description>
        </TransferPickupInformation>
        <DepartureTravelInfo>
            <DepartInfo xsi:type="ProductTransferTerminal">
                <Code>AGP</Code>
                <Name>Málaga, Málaga Costa del Sol Airport</Name>
                <TransferZone xsi:type="ProductZone">
                    <Code>AGP</Code>
                </TransferZone>
                <DateTime date="20161023" time="1055"/>
                <TerminalType>A</TerminalType>
            </DepartInfo>
        </DepartureTravelInfo>
    </ServiceTransfer>
    <ServiceTransfer xsi:type="ServiceTransfer" transferType="OUT" availToken="blxaUbk45qzfNxHkr34ZAA==">
        <ContractList>
            <Contract>
                <Name>S W 16/18 AGP</Name>
                <IncomingOffice code="51"/>
            </Contract>
        </ContractList>
        <DateFrom date="20161023" time="1055"/>
        <Currency code="EUR"/>
        <TotalAmount>6.850</TotalAmount>
        <SellingPrice mandatory="N">6.850</SellingPrice>
        <TransferInfo xsi:type="ProductTransfer">
            <Code>1|0|1</Code>
            <DescriptionList>
                <Description type="GENERAL" languageCode="ENG">With other passengers</Description>
                <Description type="PRODUCT" languageCode="ENG">Standard product type</Description>
                <Description type="VEHICLE" languageCode="ENG">Bus</Description>
            </DescriptionList>
            <ImageList>
                <Image>
                    <Type>S</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/small/shrd-stnd-bs.png</Url>
                </Image>
                <Image>
                    <Type>M</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/medium/shrd-stnd-bs.png</Url>
                </Image>
                <Image>
                    <Type>L</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/large/shrd-stnd-bs.png</Url>
                </Image>
                <Image>
                    <Type>XL</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/extralarge/shrd-stnd-bs.png</Url>
                </Image>
            </ImageList>
            <Type code="B"/>
            <VehicleType code="S"/>
            <TransferSpecificContent id="783">
                <GenericTransferGuidelinesList>
                    <TransferBulletPoint id="SPLU">
                        <Description>SPECIAL LUGGAGE</Description>
                        <DetailedDescription>In the event of extra luggage or sport equipment being checked in, please contact us, as this may carry an extra charge.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="MTPT">
                        <Description>CANT FIND MEETING POINT</Description>
                        <DetailedDescription>In the event of being unable to locate the meeting point, please call the emergency number indicated in this voucher.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CHIN">
                        <Description>CHECK INFORMATION</Description>
                        <DetailedDescription>If the details do not correspond with the reservation, please contact your agency immediately.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CBBS">
                        <Description>CHILDBOOSTER / BABY SEAT</Description>
                        <DetailedDescription>Child car seats and boosters are not included unless specified in your booking and can carry an extra cost. Should you need to book them, please contact your point of sale prior to travelling.</DetailedDescription>
                    </TransferBulletPoint>
                </GenericTransferGuidelinesList>
                <MaximumWaitingTime time="30">minutes</MaximumWaitingTime>
                <MaximumWaitingTimeSupplierDomestic time="5">minutes</MaximumWaitingTimeSupplierDomestic>
                <MaximumNumberStops maxstops="13"/>
            </TransferSpecificContent>
        </TransferInfo>
        <Paxes>
            <AdultCount>1</AdultCount>
            <ChildCount>0</ChildCount>
        </Paxes>
        <PickupLocationList>
            <TransferLocation xsi:type="ProductTransferHotel">
                <Code>14179</Code>
                <Name>Soho Bahía Málaga</Name>
                <TransferZone xsi:type="ProductZone">
                    <Code>MALAGA</Code>
                </TransferZone>
                <BestMatchOrder>1</BestMatchOrder>
                <LocationInformation distance="167">
                    <Address>SOMERA</Address>
                    <Number>8</Number>
                    <Town>MALAGA</Town>
                    <Zip>29001</Zip>
                    <GPSPoint latitude="36.7154941010865" longitude="-4.42355498670167"/>
                    <Description>Soho Bahía Málaga</Description>
                </LocationInformation>
            </TransferLocation>
            <TransferLocation xsi:type="ProductTransferHotel">
                <Code>69543</Code>
                <Name>Room Mate Lola</Name>
                <TransferZone xsi:type="ProductZone">
                    <Code>MALAGA</Code>
                </TransferZone>
                <BestMatchOrder>2</BestMatchOrder>
                <LocationInformation distance="210">
                    <Address>CASAS DE CAMPOS</Address>
                    <Number>17</Number>
                    <Town>MALAGA</Town>
                    <Zip>29001</Zip>
                    <GPSPoint latitude="36.715921969059956" longitude="-4.4234745204312276"/>
                    <Description>Room Mate Lola</Description>
                </LocationInformation>
            </TransferLocation>
            <TransferLocation xsi:type="ProductTransferHotel">
                <Code>394301</Code>
                <Name>LIFE APARTMENTS ALAMEDA COLON</Name>
                <TransferZone xsi:type="ProductZone">
                    <Code>MALAGA</Code>
                </TransferZone>
                <BestMatchOrder>3</BestMatchOrder>
                <LocationInformation distance="258">
                    <Address>ALAMEDA COLON</Address>
                    <Number>7</Number>
                    <Town>Málaga</Town>
                    <Zip>29001</Zip>
                    <GPSPoint latitude="36.715806" longitude="-4.424795"/>
                    <Description>LIFE APARTMENTS ALAMEDA COLON</Description>
                </LocationInformation>
            </TransferLocation>
        </PickupLocationList>
        <DestinationLocation xsi:type="ProductTransferTerminal">
            <Code>AGP</Code>
            <Name>Málaga, Málaga Costa del Sol Airport</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>AGP</Code>
            </TransferZone>
            <TerminalType>A</TerminalType>
        </DestinationLocation>
        <RetailPrice>6.850</RetailPrice>
        <ProductSpecifications>
            <MasterServiceType code="SHRD" name="Shared - Shuttle"/>
            <MasterProductType code="STND" name="Standard"/>
            <MasterVehicleType code="BS" name="Bus"/>
            <TransferGeneralInfoList>
                <TransferBulletPoint id="BA" order="9">
                    <Description>1 piece of baggage allowed per person</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BAHB" order="10">
                    <Description>1 item of hand baggage allowed per person</Description>
                </TransferBulletPoint>
            </TransferGeneralInfoList>
        </ProductSpecifications>
        <DepartureTravelInfo>
            <DepartInfo xsi:type="ProductTransferTerminal">
                <Code>AGP</Code>
                <Name>Málaga, Málaga Costa del Sol Airport</Name>
                <TransferZone xsi:type="ProductZone">
                    <Code>AGP</Code>
                </TransferZone>
                <DateTime date="20161023" time="1055"/>
                <TerminalType>A</TerminalType>
            </DepartInfo>
        </DepartureTravelInfo>
    </ServiceTransfer>
    <ServiceTransfer xsi:type="ServiceTransfer" transferType="OUT" availToken="blxaUbk45qzfNxHkr34ZAA==">
        <ContractList>
            <Contract>
                <Name>P W 16/18 AGP</Name>
                <IncomingOffice code="51"/>
            </Contract>
        </ContractList>
        <DateFrom date="20161023" time="1055"/>
        <Currency code="EUR"/>
        <TotalAmount>20.840</TotalAmount>
        <SellingPrice mandatory="N">20.840</SellingPrice>
        <TransferInfo xsi:type="ProductTransfer">
            <Code>1|0|2</Code>
            <DescriptionList>
                <Description type="GENERAL" languageCode="ENG">Private hire with driver</Description>
                <Description type="PRODUCT" languageCode="ENG">Premium product type</Description>
                <Description type="VEHICLE" languageCode="ENG">Car</Description>
            </DescriptionList>
            <ImageList>
                <Image>
                    <Type>S</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/small/prvt-prm-cr.png</Url>
                </Image>
                <Image>
                    <Type>M</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/medium/prvt-prm-cr.png</Url>
                </Image>
                <Image>
                    <Type>L</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/large/prvt-prm-cr.png</Url>
                </Image>
                <Image>
                    <Type>XL</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/extralarge/prvt-prm-cr.png</Url>
                </Image>
            </ImageList>
            <Type code="P"/>
            <VehicleType code="C"/>
            <TransferSpecificContent id="1462">
                <GenericTransferGuidelinesList>
                    <TransferBulletPoint id="SPLU">
                        <Description>SPECIAL LUGGAGE</Description>
                        <DetailedDescription>In the event of extra luggage or sport equipment being checked in, please contact us, as this may carry an extra charge.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CBBS">
                        <Description>CHILDBOOSTER / BABY SEAT</Description>
                        <DetailedDescription>Child car seats and boosters are not included unless specified in your booking and can carry an extra cost. Should you need to book them, please contact your point of sale prior to travelling.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CHIN">
                        <Description>CHECK INFORMATION</Description>
                        <DetailedDescription>If the details do not correspond with the reservation, please contact your agency immediately.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="DRVR">
                        <Description>CANT FIND DRIVER</Description>
                        <DetailedDescription>In the event of being unable to locate the driver, please call the emergency number indicated in this voucher.</DetailedDescription>
                    </TransferBulletPoint>
                </GenericTransferGuidelinesList>
                <MaximumWaitingTime time="30">minutes</MaximumWaitingTime>
                <MaximumWaitingTimeSupplierDomestic time="15">minutes</MaximumWaitingTimeSupplierDomestic>
            </TransferSpecificContent>
        </TransferInfo>
        <Paxes>
            <AdultCount>1</AdultCount>
            <ChildCount>0</ChildCount>
        </Paxes>
        <PickupLocation xsi:type="ProductTransferGPSPoint">
            <Code>MALAGA</Code>
            <Name>Malaga</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>MALAGA</Code>
            </TransferZone>
            <Coordinates latitude="36.7141056834158" longitude="-4.42281045239258"/>
            <Description>Med Playa Hotel Riviera</Description>
            <Address>Med Playa Hotel Riviera</Address>
            <City>Malaga</City>
            <ZipCode>12345</ZipCode>
            <Country>ES</Country>
        </PickupLocation>
        <DestinationLocation xsi:type="ProductTransferTerminal">
            <Code>AGP</Code>
            <Name>Málaga, Málaga Costa del Sol Airport</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>AGP</Code>
            </TransferZone>
            <TerminalType>A</TerminalType>
        </DestinationLocation>
        <RetailPrice>20.840</RetailPrice>
        <EstimatedTransferDuration>15</EstimatedTransferDuration>
        <ProductSpecifications>
            <MasterServiceType code="PRVT" name="Private"/>
            <MasterProductType code="PRM" name="Premium"/>
            <MasterVehicleType code="CR" name="Car"/>
            <TransferGeneralInfoList>
                <TransferBulletPoint id="ER" order="1">
                    <Description>Exclusive ride for you</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="DTDS" order="2">
                    <Description>Door to door service</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="AV247" order="3">
                    <Description>Available 24/7</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="M&GS" order="4">
                    <Description>Meet & Greet service</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BA" order="10">
                    <Description>1 piece of baggage allowed per person</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BAHB" order="11">
                    <Description>1 item of hand baggage allowed per person</Description>
                </TransferBulletPoint>
            </TransferGeneralInfoList>
        </ProductSpecifications>
        <TransferPickupInformation>
            <Description>We will pick you up at the address indicated when making your reservation. If the vehicle is unable to access this spot (pedestrian zone, etc.) then please get in contact with us.</Description>
        </TransferPickupInformation>
        <DepartureTravelInfo>
            <DepartInfo xsi:type="ProductTransferTerminal">
                <Code>AGP</Code>
                <Name>Málaga, Málaga Costa del Sol Airport</Name>
                <TransferZone xsi:type="ProductZone">
                    <Code>AGP</Code>
                </TransferZone>
                <DateTime date="20161023" time="1055"/>
                <TerminalType>A</TerminalType>
            </DepartInfo>
        </DepartureTravelInfo>
    </ServiceTransfer>
    <ServiceTransfer xsi:type="ServiceTransfer" transferType="OUT" availToken="blxaUbk45qzfNxHkr34ZAA==">
        <ContractList>
            <Contract>
                <Name>P W 16/18 AGP</Name>
                <IncomingOffice code="51"/>
            </Contract>
        </ContractList>
        <DateFrom date="20161023" time="1055"/>
        <Currency code="EUR"/>
        <TotalAmount>71.860</TotalAmount>
        <SellingPrice mandatory="N">71.860</SellingPrice>
        <TransferInfo xsi:type="ProductTransfer">
            <Code>1|0|3</Code>
            <DescriptionList>
                <Description type="GENERAL" languageCode="ENG">Private hire with driver</Description>
                <Description type="PRODUCT" languageCode="ENG">Premium product type</Description>
                <Description type="VEHICLE" languageCode="ENG">Bus bicycle carriage included</Description>
            </DescriptionList>
            <ImageList>
                <Image>
                    <Type>S</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/small/prvt-prm-bseb.png</Url>
                </Image>
                <Image>
                    <Type>M</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/medium/prvt-prm-bseb.png</Url>
                </Image>
                <Image>
                    <Type>L</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/large/prvt-prm-bseb.png</Url>
                </Image>
                <Image>
                    <Type>XL</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/extralarge/prvt-prm-bseb.png</Url>
                </Image>
            </ImageList>
            <Type code="P"/>
            <VehicleType code="1"/>
            <TransferSpecificContent id="1529">
                <GenericTransferGuidelinesList>
                    <TransferBulletPoint id="DRVR">
                        <Description>CANT FIND DRIVER</Description>
                        <DetailedDescription>In the event of being unable to locate the driver, please call the emergency number indicated in this voucher.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CHIN">
                        <Description>CHECK INFORMATION</Description>
                        <DetailedDescription>If the details do not correspond with the reservation, please contact your agency immediately.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CBBS">
                        <Description>CHILDBOOSTER / BABY SEAT</Description>
                        <DetailedDescription>Child car seats and boosters are not included unless specified in your booking and can carry an extra cost. Should you need to book them, please contact your point of sale prior to travelling.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="EBIKE">
                        <Description>EXTRA BICYCLES</Description>
                        <DetailedDescription>Bicycles need to arrive in boxes 178 x 87 x 20 cm</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="EXLU">
                        <Description>EXCESS LUGGAGE</Description>
                        <DetailedDescription>If you arrive at the destination with an excess of luggage, you will have to pay an additional charge for the extra undeclared weight. </DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="VOUC">
                        <Description>VOUCHER </Description>
                        <DetailedDescription>Remember to bring this voucher and valid photo ID with you</DetailedDescription>
                    </TransferBulletPoint>
                </GenericTransferGuidelinesList>
                <SpecificTransferInfoList>
                    <TransferBulletPoint xsi:type="TransferBulletPointNumber" id="BAEP" order="1">
                        <Description>Extra bicycle allowed per person</Description>
                        <Value>1</Value>
                        <Metric>Bicycle/s</Metric>
                    </TransferBulletPoint>
                </SpecificTransferInfoList>
                <MaximumWaitingTime time="30">minutes</MaximumWaitingTime>
                <MaximumWaitingTimeSupplierDomestic time="15">minutes</MaximumWaitingTimeSupplierDomestic>
            </TransferSpecificContent>
        </TransferInfo>
        <Paxes>
            <AdultCount>1</AdultCount>
            <ChildCount>0</ChildCount>
        </Paxes>
        <PickupLocation xsi:type="ProductTransferGPSPoint">
            <Code>MALAGA</Code>
            <Name>Malaga</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>MALAGA</Code>
            </TransferZone>
            <Coordinates latitude="36.7141056834158" longitude="-4.42281045239258"/>
            <Description>Med Playa Hotel Riviera</Description>
            <Address>Med Playa Hotel Riviera</Address>
            <City>Malaga</City>
            <ZipCode>12345</ZipCode>
            <Country>ES</Country>
        </PickupLocation>
        <DestinationLocation xsi:type="ProductTransferTerminal">
            <Code>AGP</Code>
            <Name>Málaga, Málaga Costa del Sol Airport</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>AGP</Code>
            </TransferZone>
            <TerminalType>A</TerminalType>
        </DestinationLocation>
        <RetailPrice>71.860</RetailPrice>
        <EstimatedTransferDuration>15</EstimatedTransferDuration>
        <ProductSpecifications>
            <MasterServiceType code="PRVT" name="Private"/>
            <MasterProductType code="PRM" name="Premium"/>
            <MasterVehicleType code="BSEB" name="Bus bicycle carriage included"/>
            <TransferGeneralInfoList>
                <TransferBulletPoint id="ER" order="1">
                    <Description>Exclusive ride for you</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="DTDS" order="2">
                    <Description>Door to door service</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="AV247" order="3">
                    <Description>Available 24/7</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="M&GS" order="4">
                    <Description>Meet & Greet service</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BA" order="10">
                    <Description>1 piece of baggage allowed per person</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BAHB" order="11">
                    <Description>1 item of hand baggage allowed per person</Description>
                </TransferBulletPoint>
            </TransferGeneralInfoList>
        </ProductSpecifications>
        <TransferPickupInformation>
            <Description>We will pick you up at the address indicated when making your reservation. If the vehicle is unable to access this spot (pedestrian zone, etc.) then please get in contact with us.</Description>
        </TransferPickupInformation>
        <DepartureTravelInfo>
            <DepartInfo xsi:type="ProductTransferTerminal">
                <Code>AGP</Code>
                <Name>Málaga, Málaga Costa del Sol Airport</Name>
                <TransferZone xsi:type="ProductZone">
                    <Code>AGP</Code>
                </TransferZone>
                <DateTime date="20161023" time="1055"/>
                <TerminalType>A</TerminalType>
            </DepartInfo>
        </DepartureTravelInfo>
    </ServiceTransfer>
    <ServiceTransfer xsi:type="ServiceTransfer" transferType="OUT" availToken="blxaUbk45qzfNxHkr34ZAA==">
        <ContractList>
            <Contract>
                <Name>P W 16/18 AGP</Name>
                <IncomingOffice code="51"/>
            </Contract>
        </ContractList>
        <DateFrom date="20161023" time="1055"/>
        <Currency code="EUR"/>
        <TotalAmount>28.090</TotalAmount>
        <SellingPrice mandatory="N">28.090</SellingPrice>
        <TransferInfo xsi:type="ProductTransfer">
            <Code>1|0|4</Code>
            <DescriptionList>
                <Description type="GENERAL" languageCode="ENG">Private hire with driver</Description>
                <Description type="PRODUCT" languageCode="ENG">Premium product type</Description>
                <Description type="VEHICLE" languageCode="ENG">Minivan</Description>
            </DescriptionList>
            <ImageList>
                <Image>
                    <Type>S</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/small/prvt-prm-mv.png</Url>
                </Image>
                <Image>
                    <Type>M</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/medium/prvt-prm-mv.png</Url>
                </Image>
                <Image>
                    <Type>L</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/large/prvt-prm-mv.png</Url>
                </Image>
                <Image>
                    <Type>XL</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/extralarge/prvt-prm-mv.png</Url>
                </Image>
            </ImageList>
            <Type code="P"/>
            <VehicleType code="V"/>
            <TransferSpecificContent id="1304">
                <GenericTransferGuidelinesList>
                    <TransferBulletPoint id="SPLU">
                        <Description>SPECIAL LUGGAGE</Description>
                        <DetailedDescription>In the event of extra luggage or sport equipment being checked in, please contact us, as this may carry an extra charge.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CHIN">
                        <Description>CHECK INFORMATION</Description>
                        <DetailedDescription>If the details do not correspond with the reservation, please contact your agency immediately.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="DRVR">
                        <Description>CANT FIND DRIVER</Description>
                        <DetailedDescription>In the event of being unable to locate the driver, please call the emergency number indicated in this voucher.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CBBS">
                        <Description>CHILDBOOSTER / BABY SEAT</Description>
                        <DetailedDescription>Child car seats and boosters are not included unless specified in your booking and can carry an extra cost. Should you need to book them, please contact your point of sale prior to travelling.</DetailedDescription>
                    </TransferBulletPoint>
                </GenericTransferGuidelinesList>
                <MaximumWaitingTime time="30">minutes</MaximumWaitingTime>
                <MaximumWaitingTimeSupplierDomestic time="15">minutes</MaximumWaitingTimeSupplierDomestic>
            </TransferSpecificContent>
        </TransferInfo>
        <Paxes>
            <AdultCount>1</AdultCount>
            <ChildCount>0</ChildCount>
        </Paxes>
        <PickupLocation xsi:type="ProductTransferGPSPoint">
            <Code>MALAGA</Code>
            <Name>Malaga</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>MALAGA</Code>
            </TransferZone>
            <Coordinates latitude="36.7141056834158" longitude="-4.42281045239258"/>
            <Description>Med Playa Hotel Riviera</Description>
            <Address>Med Playa Hotel Riviera</Address>
            <City>Malaga</City>
            <ZipCode>12345</ZipCode>
            <Country>ES</Country>
        </PickupLocation>
        <DestinationLocation xsi:type="ProductTransferTerminal">
            <Code>AGP</Code>
            <Name>Málaga, Málaga Costa del Sol Airport</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>AGP</Code>
            </TransferZone>
            <TerminalType>A</TerminalType>
        </DestinationLocation>
        <RetailPrice>28.090</RetailPrice>
        <EstimatedTransferDuration>15</EstimatedTransferDuration>
        <ProductSpecifications>
            <MasterServiceType code="PRVT" name="Private"/>
            <MasterProductType code="PRM" name="Premium"/>
            <MasterVehicleType code="MV" name="Minivan"/>
            <TransferGeneralInfoList>
                <TransferBulletPoint id="ER" order="1">
                    <Description>Exclusive ride for you</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="DTDS" order="2">
                    <Description>Door to door service</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="AV247" order="3">
                    <Description>Available 24/7</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="M&GS" order="4">
                    <Description>Meet & Greet service</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BA" order="10">
                    <Description>1 piece of baggage allowed per person</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BAHB" order="11">
                    <Description>1 item of hand baggage allowed per person</Description>
                </TransferBulletPoint>
            </TransferGeneralInfoList>
        </ProductSpecifications>
        <TransferPickupInformation>
            <Description>We will pick you up at the address indicated when making your reservation. If the vehicle is unable to access this spot (pedestrian zone, etc.) then please get in contact with us.</Description>
        </TransferPickupInformation>
        <DepartureTravelInfo>
            <DepartInfo xsi:type="ProductTransferTerminal">
                <Code>AGP</Code>
                <Name>Málaga, Málaga Costa del Sol Airport</Name>
                <TransferZone xsi:type="ProductZone">
                    <Code>AGP</Code>
                </TransferZone>
                <DateTime date="20161023" time="1055"/>
                <TerminalType>A</TerminalType>
            </DepartInfo>
        </DepartureTravelInfo>
    </ServiceTransfer>
    <ServiceTransfer xsi:type="ServiceTransfer" transferType="OUT" availToken="blxaUbk45qzfNxHkr34ZAA==">
        <ContractList>
            <Contract>
                <Name>P W 16/18 AGP</Name>
                <IncomingOffice code="51"/>
            </Contract>
        </ContractList>
        <DateFrom date="20161023" time="1055"/>
        <Currency code="EUR"/>
        <TotalAmount>36.560</TotalAmount>
        <SellingPrice mandatory="N">36.560</SellingPrice>
        <TransferInfo xsi:type="ProductTransfer">
            <Code>1|0|5</Code>
            <DescriptionList>
                <Description type="GENERAL" languageCode="ENG">Private hire with driver</Description>
                <Description type="PRODUCT" languageCode="ENG">Special product type</Description>
                <Description type="VEHICLE" languageCode="ENG">Disabled</Description>
            </DescriptionList>
            <ImageList>
                <Image>
                    <Type>S</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/small/prvt-spcl-dsbld.png</Url>
                </Image>
                <Image>
                    <Type>M</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/medium/prvt-spcl-dsbld.png</Url>
                </Image>
                <Image>
                    <Type>L</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/large/prvt-spcl-dsbld.png</Url>
                </Image>
                <Image>
                    <Type>XL</Type>
                    <Url>http://transferstatic.hotelbeds.com/giata/transfers/TRD/extralarge/prvt-spcl-dsbld.png</Url>
                </Image>
            </ImageList>
            <Type code="P"/>
            <VehicleType code="H"/>
            <TransferSpecificContent id="1426">
                <GenericTransferGuidelinesList>
                    <TransferBulletPoint id="SPLU">
                        <Description>SPECIAL LUGGAGE</Description>
                        <DetailedDescription>In the event of extra luggage or sport equipment being checked in, please contact us, as this may carry an extra charge.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="MTPT">
                        <Description>CANT FIND MEETING POINT</Description>
                        <DetailedDescription>In the event of being unable to locate the meeting point, please call the emergency number indicated in this voucher.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CBBS">
                        <Description>CHILDBOOSTER / BABY SEAT</Description>
                        <DetailedDescription>Child car seats and boosters are not included unless specified in your booking and can carry an extra cost. Should you need to book them, please contact your point of sale prior to travelling.</DetailedDescription>
                    </TransferBulletPoint>
                    <TransferBulletPoint id="CHIN">
                        <Description>CHECK INFORMATION</Description>
                        <DetailedDescription>If the details do not correspond with the reservation, please contact your agency immediately.</DetailedDescription>
                    </TransferBulletPoint>
                </GenericTransferGuidelinesList>
                <MaximumWaitingTime time="20">minutes</MaximumWaitingTime>
                <MaximumWaitingTimeSupplierDomestic time="15">minutes</MaximumWaitingTimeSupplierDomestic>
            </TransferSpecificContent>
        </TransferInfo>
        <Paxes>
            <AdultCount>1</AdultCount>
            <ChildCount>0</ChildCount>
        </Paxes>
        <PickupLocation xsi:type="ProductTransferGPSPoint">
            <Code>MALAGA</Code>
            <Name>Malaga</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>MALAGA</Code>
            </TransferZone>
            <Coordinates latitude="36.7141056834158" longitude="-4.42281045239258"/>
            <Description>Med Playa Hotel Riviera</Description>
            <Address>Med Playa Hotel Riviera</Address>
            <City>Malaga</City>
            <ZipCode>12345</ZipCode>
            <Country>ES</Country>
        </PickupLocation>
        <DestinationLocation xsi:type="ProductTransferTerminal">
            <Code>AGP</Code>
            <Name>Málaga, Málaga Costa del Sol Airport</Name>
            <TransferZone xsi:type="ProductZone">
                <Code>AGP</Code>
            </TransferZone>
            <TerminalType>A</TerminalType>
        </DestinationLocation>
        <RetailPrice>36.560</RetailPrice>
        <EstimatedTransferDuration>15</EstimatedTransferDuration>
        <ProductSpecifications>
            <MasterServiceType code="PRVT" name="Private"/>
            <MasterProductType code="SPCL" name="Special"/>
            <MasterVehicleType code="DSBLD" name="Disabled"/>
            <TransferGeneralInfoList>
                <TransferBulletPoint id="ER" order="1">
                    <Description>Exclusive ride for you</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="DTDS" order="2">
                    <Description>Door to door service</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="AV247" order="3">
                    <Description>Available 24/7</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BA" order="9">
                    <Description>1 piece of baggage allowed per person</Description>
                </TransferBulletPoint>
                <TransferBulletPoint id="BAHB" order="10">
                    <Description>1 item of hand baggage allowed per person</Description>
                </TransferBulletPoint>
            </TransferGeneralInfoList>
        </ProductSpecifications>
        <TransferPickupInformation>
            <Description>We will pick you up at the address indicated when making your reservation. If the vehicle is unable to access this spot (pedestrian zone, etc.) then please get in contact with us.</Description>
        </TransferPickupInformation>
        <DepartureTravelInfo>
            <DepartInfo xsi:type="ProductTransferTerminal">
                <Code>AGP</Code>
                <Name>Málaga, Málaga Costa del Sol Airport</Name>
                <TransferZone xsi:type="ProductZone">
                    <Code>AGP</Code>
                </TransferZone>
                <DateTime date="20161023" time="1055"/>
                <TerminalType>A</TerminalType>
            </DepartInfo>
        </DepartureTravelInfo>
    </ServiceTransfer>
</TransferValuedAvailRS>';
?>