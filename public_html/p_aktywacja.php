<?php
 # encoding: UTF-8 © A&R Multimedia
/*
 * This file is copyrighted.
 */

 if(!class_exists('database')) die();

 $tpl = new stdClass();

 if(!isset($_GET['ky']) || strlen($_GET['ky']) < 8) die('Błąd parametrów wejściowych.');
 $hash = urldecode($_GET['ky']);
 if(strlen($hash) != 32) die('Błąd długości parametru wejściowego.');

 $res = $db->query("SELECT `numerid`,`status` FROM `users` WHERE MD5(CONCAT('Hy^us8',`login`,'-0',`email`))='".$db->escape($hash)."' LIMIT 1");
 if($db->rows($res) > 0)
 {
	$dat = $db->fetchObject($res);
	if($dat->status == 'inactive')
	{
		$db->query("UPDATE `users` SET `status`='active' WHERE `numerid`=".intval($dat->numerid)." LIMIT 1");
	}
	 else
	{
		redirect($GLOBALS['SITEROOT']);
	}
 }
  else
 {
	die('Bład danych wejściowych / użytkownik nie znaleziony.');
 }
 $db->free($res);

 ob_clean();
 $tpl->TITLE = htmlspecialchars($pages[$_GET['page']]['title']);
 if(strlen(trim($pages[$_GET['page']]['desc'])) > 0) $tpl->METADESC = htmlspecialchars($pages[$_GET['page']]['desc']);
 $tpl->SITEROOT = htmlspecialchars($GLOBALS['SITEROOT']);
 $tpl->SITENAME = htmlspecialchars(SITENAME);
 $tpl->PAGE = $GLOBALS['current_page'];
 $tpl->PAGENAME = $_GET['page'];
 $tpl->DEBUG = DEBUG;
 $tpl->SQLQ = $db->getQueryCount();
 $tpl->TIME = endmicrotime($mytime);
 $GLOBALS['nofooter'] = true;
 printt($tpl, 'theme/aktywacja.tpl');
?>