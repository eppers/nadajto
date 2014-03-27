<?php
 # encoding: UTF-8 © A&R Multimedia
/*
 * This file is copyrighted.
 */

 if(!class_exists('database')) die();

 if($_SERVER['REQUEST_METHOD'] == 'POST')
 {
	if(isset($_POST['login']) && strlen($_POST['login']) > 1)
	{
		$res = $db->query("SELECT `login` FROM `users` WHERE `login`='".$db->escape($_POST['login'])."'");
		$ile = $db->rows($res);
		$db->free($res);
		if($ile < 1)
		{
			$login = $_POST['login'];
		}
			else
		{
			redirect($GLOBALS['SITEROOT'].'rejestracja,msg,LGNTKN.html', true);
		}
	}
		else
	{
		redirect($GLOBALS['SITEROOT'].'rejestracja,msg,LGNFLD.html', true);
	}

	if(isset($_POST['passw']) && strlen($_POST['passw']) > 1)
	{
		if(isset($_POST['passw2']) && strlen($_POST['passw2']) > 1)
		{
			if($_POST['passw'] === $_POST['passw2'])
			{
				$haslo = $_POST['passw'];
			}
				else
			{
				redirect($GLOBALS['SITEROOT'].'rejestracja,msg,PMHFLD.html', true);
			}
		}
			else
		{
			redirect($GLOBALS['SITEROOT'].'rejestracja,msg,PS2FLD.html', true);
		}
	}
		else
	{
		redirect($GLOBALS['SITEROOT'].'rejestracja,msg,PSWFLD.html', true);
	}

	if(isset($_POST['imie']) && strlen(trim($_POST['imie'])) > 1)
	{
		$imie = trim($_POST['imie']);
	}
		else
	{
		redirect($GLOBALS['SITEROOT'].'rejestracja,msg,NAMFLD.html', true);
	}

	if(isset($_POST['nazwisko']) && strlen(trim($_POST['nazwisko'])) > 1)
	{
		$nazwisko = trim($_POST['nazwisko']);
	}
		else
	{
		redirect($GLOBALS['SITEROOT'].'rejestracja,msg,SURFLD.html', true);
	}

	if(isset($_POST['telefon']) && strlen(trim($_POST['telefon'])) > 1)
	{
		$telefon = trim($_POST['telefon']);
	}
		else
	{
		redirect($GLOBALS['SITEROOT'].'rejestracja,msg,TELFLD.html', true);
	}

	if(isset($_POST['email']) && strlen(trim($_POST['email'])) > 4 && vaildEmail($_POST['email']))
	{
		$email = trim($_POST['email']);
	}
		else
	{
		redirect($GLOBALS['SITEROOT'].'rejestracja,msg,EMLFLD.html', true);
	}

	$res = $db->query("INSERT INTO `users` (`login`,`passwd`,`name`,`surname`,`phone`,`email`,`reg_time`,`reg_ip`) VALUES(
						'".$db->escape($login)."',
						'".$db->escape(md5('HSRNT'.$haslo.'ny7sgh2Af'))."',
						'".$db->escape($imie)."',
						'".$db->escape($nazwisko)."',
						'".$db->escape($telefon)."',
						'".$db->escape($email)."',
						NOW(),
						'".$db->escape(get_ip())."')");
	if($res)
	{
		$_SESSION['temp_mail'] = $email;
		$admMail = explode(',', $GLOBALS['CONFIG']['msg_dest']);
		$lnk = $GLOBALS['SITEROOT'].'aktywacja,ky,'.urlencode(md5('Hy^us8'.$login.'-0'.$email)).'.html';
		$retM = SendMail($email, array('LOGIN'=>$login, 'EMAIL'=>$email, 'FNAME'=>$imie, 'LNAME'=>$nazwisko, 'PHONE'=>$telefon, 'LINK'=>$lnk), 2, $admMail[0]);
		if($retM)
		{
			redirect($GLOBALS['SITEROOT'].'rejestracja,msg,OK.html', true);
		}
			else
		{
			redirect($GLOBALS['SITEROOT'].'rejestracja,msg,NOMSG.html', true);
		}
	}
		else
	{
		redirect($GLOBALS['SITEROOT'].'rejestracja,msg,FAIL.html', true);
	}
 }

 $tpl = new stdClass();

 $tpl->SITEROOT = htmlspecialchars($GLOBALS['SITEROOT']);
 $tpl->SITENAME = htmlspecialchars(SITENAME);
 $tpl->PAGE = $GLOBALS['current_page'];
 $tpl->PAGENAME = $_GET['page'];
 $tpl->DEBUG = DEBUG;
 if(isset($_GET['msg']) && ($_GET['msg'] == 'OK' || $_GET['msg'] == 'NOMSG') && isset($_SESSION['temp_mail']) && strlen($_SESSION['temp_mail']) > 0)
 {
	ob_clean();
	$tpl->TEMP_MAIL = htmlspecialchars($_SESSION['temp_mail']);
	if($_GET['msg'] == 'NOMSG')
	{
		$tpl->NOMSG = true;
		$db->query("UPDATE `users` SET `status`='active' WHERE `email`='".$db->escape($_SESSION['temp_mail'])."'");
	}
	$tpl->TITLE = htmlspecialchars($pages[$_GET['page']]['title']);
	if(strlen(trim($pages[$_GET['page']]['desc'])) > 0) $tpl->METADESC = htmlspecialchars($pages[$_GET['page']]['desc']);
	$tpl->SITEROOT = htmlspecialchars($GLOBALS['SITEROOT']);
	$tpl->SITENAME = htmlspecialchars(SITENAME);
	$tpl->PAGE = $GLOBALS['current_page'];
	$tpl->PAGENAME = $_GET['page'];
	$tpl->DEBUG = DEBUG;
	$tpl->SQLQ = $db->getQueryCount();
	$tpl->TIME = endmicrotime($mytime);
	unset($_SESSION['temp_mail']);
	$GLOBALS['nofooter'] = true;
	printt($tpl, 'theme/rejestr_ok.tpl');
 }
	else
 {
	if(isset($_GET['msg']) && strlen($_GET['msg']) > 0) $tpl->ERRMSG = htmlspecialchars($_GET['msg']);
	printt($tpl, 'theme/rejestracja.tpl');
 }
?>