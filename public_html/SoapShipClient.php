<?php

  //Configuration
  $access = "3CBDFD6E18683396";
  $userid = "nadajto";
  $passwd = "Test1234";
  $wsdl = "./wsdl/Ship.wsdl";
  $operation = "ProcessShipment";
  $endpointurl = 'https://wwwcie.ups.com/webservices/Ship';
  $outputFileName = "XOLTResult.xml";

  function processShipment()
  {

      //create soap request
    $requestoption['RequestOption'] = 'nonvalidate';
    $requestoption['RequestAction'] = 'ShipConfirm';
    $request['Request'] = $requestoption;

    $shipment['Description'] = 'Ship WS test';
    $shipper['Name'] = 'ShipperName';
    $shipper['AttentionName'] = 'ShipperZs Attn Name';
    $shipper['TaxIdentificationNumber'] = '123456';
    $shipper['ShipperNumber'] = 'E4854X';
    $address['AddressLine'] = '2311 York Rd';
    $address['City'] = 'Cieszyn';
    $address['PostalCode'] = '43400';
    $address['CountryCode'] = 'PL';
    $shipper['Address'] = $address;
    $phone['Number'] = '1115554758';
    $phone['Extension'] = '1';
    $shipper['Phone'] = $phone;
    $shipment['Shipper'] = $shipper;

    $shipto['Name'] = 'Happy Dog Pet Supply';
    $shipto['AttentionName'] = '1160b_74';
    $addressTo['AddressLine'] = '123 Main St';
    $addressTo['City'] = 'Bielsko-Biała';
    $addressTo['PostalCode'] = '43300';
    $addressTo['CountryCode'] = 'PL';
    $phone2['Number'] = '9225377171';
    $shipto['Address'] = $addressTo;
    $shipto['Phone'] = $phone2;
    $shipment['ShipTo'] = $shipto;

    $shipfrom['Name'] = 'T and T Designs';
    $shipfrom['AttentionName'] = '1160b_74';
    $addressFrom['AddressLine'] = '2311 York Rd';
    $addressFrom['City'] = 'Cieszyn';
    $addressFrom['PostalCode'] = '43400';
    $addressFrom['CountryCode'] = 'PL';
    $phone3['Number'] = '1234567890';
    $shipfrom['Address'] = $addressFrom;
    $shipfrom['Phone'] = $phone3;
    $shipment['ShipFrom'] = $shipfrom;

    $shipmentcharge['Type'] = '01';

    $shipmentcharge['Type'] = '01';
    $billshipper['AccountNumber'] = $shipper['ShipperNumber'];

    $shipmentcharge['BillShipper'] = $billshipper;
    $paymentinformation['ShipmentCharge'] = $shipmentcharge;
    $shipment['PaymentInformation'] = $paymentinformation;

    $service['Code'] = '07'; //na stronie polskiej znajduja sie rodzaje przesylek w PL
    $service['Description'] = 'Ground';
    $shipment['Service'] = $service;

    $package['Description'] = '';
    $packaging['Code'] = '02';
    $packaging['Description'] = 'Nails';
    $package['Packaging'] = $packaging;
    $unit['Code'] = 'CM';
    $unit['Description'] = 'Centimeters,';
    $dimensions['UnitOfMeasurement'] = $unit;
    $dimensions['Length'] = '7';
    $dimensions['Width'] = '5';
    $dimensions['Height'] = '2';
    $package['Dimensions'] = $dimensions;
    $unit2['Code'] = 'KGS';
    $unit2['Description'] = 'Kilograms';
    $packageweight['UnitOfMeasurement'] = $unit2;
    $packageweight['Weight'] = '2';
    $package['PackageWeight'] = $packageweight;
    $shipment['Package'] = $package;

    $labelimageformat['Code'] = 'GIF';
    $labelimageformat['Description'] = 'GIF';
    $labelspecification['LabelImageFormat'] = $labelimageformat;
    $labelspecification['HTTPUserAgent'] = 'Mozilla/4.5';
    $shipment['LabelSpecification'] = $labelspecification;
    $request['Shipment'] = $shipment;

    echo "Request.......\n";
	print_r($request);
    echo "\n\n\n";
    return $request;

  }

  function processShipConfirm()
  {

    //create soap request

  }

  function processShipAccept()
  {
    //create soap request
  }

  try
  {

    $mode = array
    (
         'soap_version' => 'SOAP_1_1',  // use soap 1.1 client
         'trace' => 1
    );

    // initialize soap client
  	$client = new SoapClient($wsdl , $mode);

  	//set endpoint url
  	$client->__setLocation($endpointurl);


    //create soap header
    $usernameToken['Username'] = $userid;
    $usernameToken['Password'] = $passwd;
    $serviceAccessLicense['AccessLicenseNumber'] = $access;
    $upss['UsernameToken'] = $usernameToken;
    $upss['ServiceAccessToken'] = $serviceAccessLicense;

    $header = new SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0','UPSSecurity',$upss);
    $client->__setSoapHeaders($header);

    if(strcmp($operation,"ProcessShipment") == 0 )
    {
        //get response
  	$resp = $client->__soapCall('ProcessShipment',array(processShipment()));

         //get status
        echo "Response Status: " . $resp->Response->ResponseStatus->Description ."\n";

        //save soap request and response to file
        $fw = fopen($outputFileName , 'w');
        fwrite($fw , "Request: \n" . $client->__getLastRequest() . "\n");
        fwrite($fw , "Response: \n" . $client->__getLastResponse() . "\n");
        fclose($fw);
        
        print "\n\nresp..\n";
        print_r($resp);

    }
    else if (strcmp($operation , "ProcessShipConfirm") == 0)
    {
            //get response
  	$resp = $client->__soapCall('ProcessShipConfirm',array(processShipConfirm()));

         //get status
        echo "Response Status: " . $resp->Response->ResponseStatus->Description ."\n";

        //save soap request and response to file
        $fw = fopen($outputFileName , 'w');
        fwrite($fw , "Request: \n" . $client->__getLastRequest() . "\n");
        fwrite($fw , "Response: \n" . $client->__getLastResponse() . "\n");
        fclose($fw);

    }
    else
    {
        $resp = $client->__soapCall('ProcessShipeAccept',array(processShipAccept()));

        //get status
        echo "Response Status: " . $resp->Response->ResponseStatus->Description ."\n";

  	//save soap request and response to file
  	$fw = fopen($outputFileName ,'w');
  	fwrite($fw , "Request: \n" . $client->__getLastRequest() . "\n");
        fwrite($fw , "Response: \n" . $client->__getLastResponse() . "\n");
        fclose($fw);
    }

  }
  catch(Exception $ex)
  {
      print "Exception.. \n\n";
  	print_r ($ex);
  }

?>
