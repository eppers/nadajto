<?php

  //Configuration
  $access = "3CBDFD6E18683396";
  $userid = "nadajto";
  $passwd = "Test1234";
  $wsdl = "./wsdl/RateWS.wsdl";
  $operation = "ProcessRate";
  $endpointurl = 'https://wwwcie.ups.com/webservices/Rate';
  $outputFileName = "XOLTResult.xml";

  function processRate()
  {
      
   
      //create soap request
      $option['RequestOption'] = 'Rate';
      $option['RequestAction'] = 'Rate';
      $request['Request'] = $option;
      
      $pickuptype['Code'] = '03';
      $pickuptype['Description'] = 'Ground';
      $request['PickupType'] = $pickuptype;
      
      
      $shipper['Name'] = 'Imani Carr';
      $shipper['ShipperNumber'] = 'E4854X';
      $address['PostalCode'] = '43400';
      $address['CountryCode'] = 'PL';
      $address['AddressLine'] ='Southam Rd 4 Case Cour Apt 3B';
      $address['City'] = 'Cieszyn';
      
          
      $shipper['Address'] = $address;
      $shipment['Shipper'] = $shipper;
          
      $addressTo['PostalCode'] = '43300';
      $addressTo['CountryCode'] = 'PL';
      $shipto['Name'] = 'Imani Imaginarium';
      $addressTo['AddressLine'] = '21 ARGONAUT SUITE B';
      $addressTo['City'] = 'Bielsko-Biała';
      $shipto['Address'] = $addressTo;
      $shipment['ShipTo'] = $shipto;
      
          
      $shipfrom['Name'] = 'Imani Imaginarium';
      $addressFrom['AddressLine'] = 'Southam Rd 4 Case Court pt 3B';
      $addressFrom['City'] = 'Cieszyn';
      $addressFrom['PostalCode'] = '43400';
      $addressFrom['CountryCode'] = 'PL';
      $shipfrom['Address'] = $addressFrom;
      $shipment['ShipFrom'] = $shipfrom;
      
          
      $service['Code'] = '03';
      $service['Description'] = 'GND';
      $shipment['Service'] = $service;
          

$packaging1['Code'] = '02';
      //$packaging1['Description'] = 'Rate';
      $package1['PackagingType'] = $packaging1;
      $dunit1['Code'] = 'CM';
      //$dunit1['Description'] = 'inches';
      $dimensions1['Length'] = '5';
      $dimensions1['Width'] = '4';
      $dimensions1['Height'] = '10';
      $dimensions1['UnitOfMeasurement'] = $dunit1;
      $package1['Dimensions'] = $dimensions1;
      $punit1['Code'] = 'KGS';
      //$punit1['Description'] = 'Pounds';
      $packageweight1['Weight'] = '1';
      $packageweight1['UnitOfMeasurement'] = $punit1;
      $package1['PackageWeight'] = $packageweight1;

      $packaging2['Code'] = '02';
      //$packaging2['Description'] = 'Rate';
      $package2['PackagingType'] = $packaging2;
      $dunit2['Code'] = 'CM';
      //$dunit2['Description'] = 'inches';
      $dimensions2['Length'] = '3';
      $dimensions2['Width'] = '5';
      $dimensions2['Height'] = '8';
      $dimensions2['UnitOfMeasurement'] = $dunit2;
      $package2['Dimensions'] = $dimensions2;
      $punit2['Code'] = 'KGS';
      //$punit2['Description'] = 'Pounds';
      $packageweight2['Weight'] = '2';
      $packageweight2['UnitOfMeasurement'] = $punit2;
      $package2['PackageWeight'] = $packageweight2;

 /*     $option['RequestOption'] = 'Shop';
      $request['Request'] = $option;

      $pickuptype['Code'] = '01';
      $pickuptype['Description'] = 'Daily Pickup';
      $request['PickupType'] = $pickuptype;

      $customerclassification['Code'] = '01';
      $customerclassification['Description'] = 'Classfication';
      $request['CustomerClassification'] = $customerclassification;

      $shipper['Name'] = 'Imani Carr';
      $shipper['ShipperNumber'] = 'E4854X';
      $address['AddressLine'] = array
      (
          'Southam Rd',
          '4 Case Cour',
          'Apt 3B'
      );
      $address['City'] = 'Cieszyn';
      //$address['StateProvinceCode'] = 'MD';
      $address['PostalCode'] = '43400';
      $address['CountryCode'] = 'PL';
      $shipper['Address'] = $address;
      $shipment['Shipper'] = $shipper;

      $shipto['Name'] = 'Imani Imaginarium';
      $addressTo['AddressLine'] = '21 ARGONAUT SUITE B';
      $addressTo['City'] = 'Bielsko-Biała';
      //$addressTo['StateProvinceCode'] = 'CA';
      $addressTo['PostalCode'] = '43300';
      $addressTo['CountryCode'] = 'PL';
      $addressTo['ResidentialAddressIndicator'] = '';
      $shipto['Address'] = $addressTo;
      $shipment['ShipTo'] = $shipto;

      $shipfrom['Name'] = 'Imani Imaginarium';
      $addressFrom['AddressLine'] = array
      (
          'Southam Rd',
          '4 Case Court',
          'Apt 3B'
      );
      $addressFrom['City'] = 'Cieszyn';
      //$addressFrom['StateProvinceCode'] = 'MD';
      $addressFrom['PostalCode'] = '43400';
      $addressFrom['CountryCode'] = 'PL';
      $shipfrom['Address'] = $addressFrom;
          $shipment['ShipTo'] = $shipto;
      $shipment['ShipFrom'] = $shipfrom;

      $service['Code'] = '03';
      $service['Description'] = 'GND';
      $shipment['Service'] = $service;

      $billshipper['AccountNumber'] = $shipper['ShipperNumber'];
      $shipmentcharge['BillShipper'] = $billshipper;
      
      $packaging1['Code'] = '02';
      $packaging1['Description'] = 'Rate';
      $package1['PackagingType'] = $packaging1;
      $dunit1['Code'] = 'IN';
      $dunit1['Description'] = 'inches';
      $dimensions1['Length'] = '5';
      $dimensions1['Width'] = '4';
      $dimensions1['Height'] = '10';
      $dimensions1['UnitOfMeasurement'] = $dunit1;
      $package1['Dimensions'] = $dimensions1;
      $punit1['Code'] = 'LBS';
      $punit1['Description'] = 'Pounds';
      $packageweight1['Weight'] = '1';
      $packageweight1['UnitOfMeasurement'] = $punit1;
      $package1['PackageWeight'] = $packageweight1;

      $packaging2['Code'] = '02';
      $packaging2['Description'] = 'Rate';
      $package2['PackagingType'] = $packaging2;
      $dunit2['Code'] = 'IN';
      $dunit2['Description'] = 'inches';
      $dimensions2['Length'] = '3';
      $dimensions2['Width'] = '5';
      $dimensions2['Height'] = '8';
      $dimensions2['UnitOfMeasurement'] = $dunit2;
      $package2['Dimensions'] = $dimensions2;
      $punit2['Code'] = 'LBS';
      $punit2['Description'] = 'Pounds';
      $packageweight2['Weight'] = '2';
      $packageweight2['UnitOfMeasurement'] = $punit2;
      $package2['PackageWeight'] = $packageweight2;

      $paymentinformation['ShipmentCharge'] = $shipmentcharge;

      $shipment['Package'] = array(	$package1 , $package2 );
      $shipment['ShipmentServiceOptions'] = '';
      $shipment['LargePackageIndicator'] = '';
      $request['Shipment'] = $shipment;
  
  */
      $shipment['Package'] = array(	$package1 , $package2 );
       $request['Shipment'] = $shipment;
      echo "Request.......\n";
      echo wddx_serialize_value($request);
      echo "\n\n";
      return $request;
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


    //get response
  	$resp = $client->__soapCall($operation ,array(processRate()));

    //get status
    echo "Response Status: " . $resp->Response->ResponseStatus->Description ."\n";

    //save soap request and response to file
    $fw = fopen($outputFileName , 'w');
    fwrite($fw , "Request: \n" . $client->__getLastRequest() . "\n");
    fwrite($fw , "Response: \n" . $client->__getLastResponse() . "\n");
    fclose($fw);

  }
  catch(Exception $ex)
  {
      echo "\n\nResponse...\n";
  	print_r ($ex);
  }

?>
