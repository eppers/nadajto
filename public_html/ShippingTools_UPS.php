<?php
// interface with UPS
class ShippingTools_UPS{
  //Configuration
  	public $to_address;
  	public $shipment_details;
  	public $carrier,$residential,$signatureRequired,$saturdayDelivery;
  	
	public $from_address;
	public $ordernum;
	public $trackingnumber,$shipcost;
	public $EPLlabel='EPL';	//0 if PNG,1 if EPL;
	public $pkgweight;
        public $pkgdimensions;
  
	public $access = "3CBDFD6E18683396";
  	public $userid = "nadajto";
  	public $passwd = "Test1234";
  	public $accountnum='E4854X';
        public $outputFileName = "XOLTResult.xml";

        private $shippingmethod;




    function set_tracking_num($response){
            $this->trackingnumber=$response->ShipmentResults->ShipmentIdentificationNumber;
            Label::set_tracking($this->trackingnumber);
    }

    function set_pkg_cost($response){
            $this->cost=$response->ShipmentResults->ShipmentCharges->TotalCharges->MonetaryValue;
    }
        
    //called by processShipment to set from address
    function set_shipper(){
    //$shipper['AttentionName'] = 'ShipperZs Attn Name';
    //$shipper['TaxIdentificationNumber'] = '123456';

            $shipper['Name'] = $this->from_address->company;
            $shipper['ShipperNumber'] = $this->accountnum;
            $address['AddressLine'] = $this->from_address->addr;
            $address['City'] = $this->from_address->city;
            $address['PostalCode'] = $this->from_address->zip;
            $address['CountryCode'] = $this->from_address->country;
            $shipper['Address'] = $address;
            $phone['Number'] = $this->from_address->phone;
        //$phone['Extension'] = '1';
            $shipper['Phone'] = $phone;	
            return $shipper;
    }
    //called by processShipment to set from address
    function set_ship_to(){
    $shipto['Name'] = $this->to_address->name;
		$shipto['AttentionName'] = $this->to_address->company;
		$addressTo['AddressLine'] = array($this->to_address->addr,$this->to_address->addr2);
		$addressTo['City'] = $this->to_address->city;
		$addressTo['PostalCode'] = $this->to_address->zip;
		$addressTo['CountryCode'] = $this->to_address->country;

		$phone2['Number'] = $this->to_address->phone;
		$shipto['Address'] = $addressTo;
		$shipto['Phone'] = $phone2;
		return $shipto;
    }
    //called by processShipment to set from address
    function set_ship_from(){
    //$shipfrom['AttentionName'] = '1160b_74';
		$addressFrom['AddressLine'] = $this->from_address->addr;
		$addressFrom['City'] = $this->from_address->city;
		$addressFrom['PostalCode'] = $this->from_address->zip;
		$addressFrom['CountryCode'] = $this->from_address->country;
		$phone3['Number'] = $this->from_address->phone;
	  $shipfrom['Name'] = $this->from_address->company;
		$shipfrom['Address'] = $addressFrom;
		$shipfrom['Phone'] = $phone3;
		return $shipfrom;
    }
    
    function process_shipment($p_method){
            //create soap request
            $requestoption['RequestOption'] = 'nonvalidate';
            $request['Request'] = $requestoption;

            $shipment['Shipper'] = $this->set_shipper();
            $shipment['ShipTo'] =  $this->set_ship_to();
            $shipment['ShipFrom'] = $this->set_ship_from();

            $shipmentcharge['Type'] = '01';
            $billshipper['AccountNumber'] = $this->accountnum;
            $shipmentcharge['BillShipper'] = $billshipper;
            $paymentinformation['ShipmentCharge'] = $shipmentcharge;
            $shipment['PaymentInformation'] = $paymentinformation;

            $service['Code'] = $this->get_service_code($p_method);
            $shipment['Service'] = $service;

    //    $package['Description'] = '';
            $packaging['Code'] = '02';
            $package['Packaging'] = $packaging;
            $unit['Code'] = 'CM';
            $unit['Description'] = 'Centimeters,';
            $dimensions['UnitOfMeasurement'] = $unit;
            $dimensions['Length'] = $this->pkgdimensions->length;
            $dimensions['Width'] = $this->pkgdimensions->width;
            $dimensions['Height'] = $this->pkgdimensions->height;
            $package['Dimensions'] = $dimensions;
    
            $unit2['Code'] = 'KGS';
            $unit2['Description'] = 'Kilograms';
            $packageweight['UnitOfMeasurement'] = $unit2;
            $packageweight['Weight'] = $this->pkgweight;
            $package['PackageWeight'] = $packageweight;

//		$referencenumber['Value']=$this->ordernum;
//		$referencenumber['Code']="IK";

//		$package['ReferenceNumber']=$referencenumber;
//		if($this->signatureRequired){
//			$deliveryconfirmation['DCISType']=3; //adult signature required
//			$packageserviceoptions['DeliveryConfirmation']=$deliveryconfirmation;
//		}

            $shipment['Package'] = $package;
            $label=($this->EPLlabel=='PNG')? Label::set_png() : Label::set_epl();	
            $request['LabelSpecification'] = $label;
            $shipmentserviceoptions=null;
            if($this->saturdayDelivery){
                $shipmentserviceoptions['SaturdayDeliveryIndicator']=1;
            }
            $shipmentratingoptions['NegotiatedRatesIndicator']=1;
            $shipment['ShipmentRatingOptions']=$shipmentratingoptions;
            if (!is_null($shipmentserviceoptions)) $shipment['ShipmentServiceOptions']=$shipmentserviceoptions;	
            $request['Shipment'] = $shipment;
      print_r($request);
            return $request;
  	}
        
  function get_service_code($service){
  	switch($service){
  		case 'st':
  			return '11';
  			break;
  		case 'ex':
  			return '07';
  			break;
  		case 'tex':
  			return '85';
  			break;
  		case 'texs':
  			return '86';
  			break;
  		case 'ts':
  			return '82';
  			break;
  		case 'exp':
  			return '54';
  			break;
  	}
  }
  

    public function ship($pMethod,$emailAddress='customerservice@kosherwine.com'){

            if($pMethod=='g' && $this->residential) $pMethod='gr';//if the residential flag is set to true and we are shipping ground then modify the shipping method to home ground.
            $this->shippingmethod=$this->get_service_code($pMethod);
            $wsdl = "./wsdl/Ship.wsdl";
            $operation = "ProcessShipment";
            $endpointurl = 'https://wwwcie.ups.com/webservices/Ship';

          try {
            $client=$this->generate_soap($wsdl,$endpointurl);
            if(strcmp($operation,"ProcessShipment") == 0 )
                    $resp = $client->__soapCall('ProcessShipment',array($this->process_shipment($pMethod)));
            else if (strcmp($operation , "ProcessShipConfirm") == 0)
                    $resp = $client->__soapCall('ProcessShipConfirm',array($this->process_shipConfirm()));
            else
                    $resp = $client->__soapCall('ProcessShipAccept',array($this->process_shipAccept()));
print "\n resp...\n\n";
print_r($resp);
		$this->set_tracking_num($resp);
		$this->set_pkg_cost($resp);
		$labelName=Label::save($resp);

          $fw = fopen($this->outputFileName , 'w');
          fwrite($fw , "Request: \n" . $client->__getLastRequest() . "\n");
          fwrite($fw , "Response: \n" . $client->__getLastResponse() . "\n");
          fclose($fw);
	  }
	  catch(Exception $ex)
	  {
			print_r ($ex);
	  }
	  return $labelName;
    }

    public function process_void($tracknum){
            //create soap request
            $tref['CustomerContext'] = 'Add description here';
            $req['TransactionReference'] = $tref;
            $request['Request'] = $req;
            $voidshipment['ShipmentIdentificationNumber'] = $tracknum;
            $request['VoidShipment'] = $voidshipment;
            return $request;
    }

    public function delete_shipment($ptracknum){
		$wsdl = "c:/inetpub/wwwroot/cgi-bin/IncludeCode/UPS/Void.wsdl";
	  	$operation = "ProcessVoid";
  		$endpointurl = 'https://wwwcie.ups.com/webservices/Void';
		try
		{
			$client=$this->generate_soap($wsdl,$endpointurl);
			$resp = $client->__soapCall($operation ,array($this->processVoid($ptracknum)));
			return "Success";
		}
		catch(Exception $ex)
		{
			print_r ($ex);
			return false;
		}
	}  

	//helper function to format Address Correction request.
	function process_XAV($address){
      //create soap request
      $option['RequestOption'] = '3';
      $request['Request'] = $option;
      $addrkeyfrmt['ConsigneeName'] = $address->company;
      $addrkeyfrmt['AddressLine'] = array
      (
         $address->addr,
 	     $address->addr2,
      );
 	  $addrkeyfrmt['PoliticalDivision2'] = $address->city;
 	  $addrkeyfrmt['PoliticalDivision1'] = $address->state;
 	  $addrkeyfrmt['PostcodePrimaryLow'] = $address->zipcode;
 	  $addrkeyfrmt['CountryCode'] = 'US';
 	  $request['AddressKeyFormat'] = $addrkeyfrmt;
      return $request;
  	}
  	public function address_changed($orignial_addr,$orignial_addr2,$orignial_city,$orignial_state,$orignial_zipcode){
		$changed=false;
  		if(strtolower($this->toaddr)!=strtolower($orignial_addr)) $changed=true;
  		if(strtolower($this->toaddr2)!=strtolower($orignial_addr2)) $changed=true;
  		if(strtolower($this->tocity)!=strtolower($orignial_city)) $changed=true;
  		if(strtolower($this->tostate)!=strtolower($orignial_state)) $changed=true;
  		if(strtolower($this->tozip)!=strtolower($orignial_zipcode)) $changed=true;
  		if(strtolower($this->tocountry)!=strtolower($orignial_addr)) $changed=true;  		
  		return $changed;
  	}
	public function address_checker($address){
		$wsdl = "c:/inetpub/wwwroot/cgi-bin/IncludeCode/UPS/XAV.wsdl";
	  	$operation = "ProcessXAV";
	  	$endpointurl = "https://wwwcie.ups.com/webservices/XAV";
		try
		{
			$client=$this->generate_soap($wsdl,$endpointurl);
			$resp = $client->__soapCall($operation ,array($this->processXAV($address)));
			$valid_address=1;
			$multiple_address=0;
			$valid_address=1;
			$multiple_address=1;
			$candidate=count($resp->Candidate)>1 ? $resp->Candidate[0] : $resp->Candidate;
			$address=$candidate->AddressKeyFormat;
			if (is_array($address->AddressLine)){
				$this->toaddr=$address->AddressLine[0];
				$this->toaddr2=$address->AddressLine[1];
			}
			else{
				$this->toaddr=$address->AddressLine;
			}

			$this->tocity=$address->PoliticalDivision2;
			$this->tostate=$address->PoliticalDivision1;
			$this->tozip=$address->PostcodePrimaryLow;
			$this->tocountry=$address->CountryCode;
			$this->residential=$candidate->AddressClassification->Code==2 ? 1 : 0; //if code==2 then this is residential
			//if a single address is returned we still need to check if this address has been changed.
			if(!$multiple_address)
				$multiple_address=$this->address_changed($address->addr,$address->addr2,$address->city,$address->state,$address->zipcode);

			$returnarr=array('confirmed'=>$valid_address,'removed'=>$multiple_address);
		}
		catch(Exception $ex)
		{
			print_r ($ex);
		}
		return($returnarr);
	}

	//check that there is a valid return
	//find ground service and return number of transit days
	public function transit_time_helper($response){
		if (!property_exists($response,'TransitResponse'))
			return 99;
		$services=$response->TransitResponse->ServiceSummary;
		foreach($services as $service){
			if($service->Service->Code=='GND')
				return $service->EstimatedArrival->BusinessDaysInTransit;
		}
		return 99;
	}

	//shipDate must be in the format YYYY-MM-DD HH:MM:SS using a 24 hour clock
	//date('c') returns a format that is compatible.
	//returns days for ground to arrive at destination
	public function transit_time($shipDate){
	  $wsdl = "c:/inetpub/wwwroot/cgi-bin/IncludeCode/UPS/TNTWS.wsdl";
	  $operation = "ProcessTimeInTransit";
	  $endpointurl = "https://wwwcie.ups.com/webservices/TimeInTransit";
	  try
		{
			$client=$this->generate_soap($wsdl,$endpointurl);
			$requestoption['RequestOption'] = 'TNT';
			$request['Request'] = $requestoption;
			$addressFrom['CountryCode'] = 'US';
			$addressFrom['PostalCode'] = $this->from_address->zip;
			$shipFrom['Address'] = $addressFrom;
			$request['ShipFrom'] = $shipFrom;
			$addressTo['CountryCode'] = 'US';
			$addressTo['PostalCode'] = $this->tozip;
			$shipTo['Address'] = $addressTo;
			$request['ShipTo'] = $shipTo;
			$pickup['Date'] = date('Ymd',strtotime($shipDate));
			$request['Pickup'] = $pickup;
			$unitOfMeasurement['Code'] = 'LBS';
			$shipmentWeight['UnitOfMeasurement'] = $unitOfMeasurement;
			$shipmentWeight['Weight'] = '10';
			$request['ShipmentWeight'] = $shipmentWeight;
			$request['TotalPackagesInShipment'] = '1';
			$request['MaximumListSize'] = '1';
			$resp = $client->__soapCall($operation ,array($request));
			return $this->transit_time_helper($resp);
  		}
  	catch(Exception $ex)
		{
			return 99;
		}
	return 99;
	}

	//this sets up the soap client. All api calls must use this before they execute their specific api call.
	function generate_soap($wsdl,$endpointurl){
		$mode = array
			(
					 'soap_version' => 'SOAP_1_1',  // use soap 1.1 client
					 'trace' => 1
			);	
		$client = new SoapClient($wsdl , $mode);
		$client->__setLocation($endpointurl);
		$usernameToken['Username'] = $this->userid;
		$usernameToken['Password'] = $this->passwd;
		$serviceAccessLicense['AccessLicenseNumber'] = $this->access;
		$upss['UsernameToken'] = $usernameToken;
		$upss['ServiceAccessToken'] = $serviceAccessLicense;
		$header = new SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0','UPSSecurity',$upss);
		$client->__setSoapHeaders($header);
		return $client;
	}
}
?>