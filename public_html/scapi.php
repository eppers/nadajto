<?php
 error_reporting(E_ALL);
 ini_set('display_errors', 'On');
 ini_set('session.gc_maxlifetime', 120);
 ini_set('session.gc_divisor', 1);
 ini_set('session.gc_probability', 4);
 ini_set('session.use_cookies', 0);
 setlocale(LC_ALL, 'en_US.UTF-8');
 mb_internal_encoding('UTF-8');
 date_default_timezone_set('Europe/Warsaw');

 if(!class_exists('SOAPServer'))
 {
	trigger_error('SOAPServer is not available.', E_USER_ERROR);
 }

 define('INCPATH',	dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR);
 define('TMPPATH',	dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR);

 session_save_path(TMPPATH);

 class ShopClientAPI
 {
## PUBLIC variables

## PRIVATE variables

	private $db;

## PUBLIC methods

	public function Login($uinfo)
	{
		$this->dbConnect();
		$retVal = false;
		$res = $this->db->query("SELECT `numerid`,`login`,`apikey` 
									FROM `users`
									WHERE `status`='active' AND `login`='".$this->db->prepareString($uinfo->login)."' AND `passwd`='".$this->db->escape(sha1($uinfo->password))."'
									LIMIT 1");
		if($this->db->rows($res) > 0)
		{
			$dat = $this->db->fetchObject($res);
			if(strlen($dat->apikey) >= 4)
			{
				if($dat->apikey == $uinfo->key)
				{
					session_start();
					$_SESSION['login'] = $uinfo->login;
					$_SESSION['key'] = $uinfo->key;
					$retVal = array('SessionID' => session_id(), 'LoginTS' => time());
				}
				 else
				{
					$retVal = new SoapFault('103', 'Invalid API KEY.');
				}
			}
			 else
			{
				$retVal = new SoapFault('102', 'Sorry, this user has no valid API KEY.');
			}
		}
		 else
		{
			$retVal = new SoapFault('101', 'Invalid username or password.');
		}
		$this->db->free($res);
		return $retVal;
	}

	public function GetAvailableCouriers($sessID)
	{
		if($this->authUser($sessID))
		{
			$this->dbConnect();
			$rows = array();
			$res = $this->db->query("SELECT * FROM `couriers` ORDER BY `name` ASC");
			while($dat = $this->db->fetchObject($res))
			{
				array_push($rows, array($dat->numerid, $dat->name, 'http://'.$_SERVER['HTTP_HOST'].'/res/logo/'.mb_strtolower(trim($dat->name)).'.png'));
			}
			$this->db->free($res);
			return $rows;
		}
		return new SoapFault('2', 'Unauthorized access. Please do login.');
	}

	public function CalculateDelivery()
	{
		return new SoapFault('2', 'Unauthorized access. Please do login.');
	}

	public function AddConsignment()
	{
		return new SoapFault('2', 'Unauthorized access. Please do login.');
	}

	public function GetConsignments()
	{
		return new SoapFault('2', 'Unauthorized access. Please do login.');
	}

	public function ConsignmentStatus()
	{
		return new SoapFault('2', 'Unauthorized access. Please do login.');
	}

## PRIVATE methods

	private function dbConnect()
	{
		require_once INCPATH.'config.php';
		require_once INCPATH.'class.database.php';
		$this->db = new database();
		$this->db->connect();
	}

	private function authUser($sessID)
	{
		if(file_exists(TMPPATH.'sess_'.$sessID))
		{
			session_id($sessID);
			session_start();
			return true;
		}
		else return false;
	}

 }//class


## classes definitnions ###########################################################
 class UserObject {
	public $login;
	public $password;
	public $key;
 }

 class ConsignmentObject {
 }


## start server ###################################################################
 if($_SERVER['REQUEST_METHOD'] == 'GET' || $_SERVER['REQUEST_METHOD'] == 'POST')
 {
	try {
		$server = new SOAPServer(
			NULL,
			array(
				'uri' => 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']
			)
		);
		$server->setClass('ShopClientAPI');
		$server->handle();
	}
	catch (SOAPFault $err) {
		trigger_error($err->faultstring, E_USER_ERROR);
	}
 }
?>