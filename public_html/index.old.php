<?php
 # encoding: UTF-8 © A&R Multimedia
/*
 * This file is copyrighted.
 *
 *  SITEROOT, SITEURL, SITEURI, REALPATH, PAGESARR
 *  mytime, cpage, current_page, subpageID, db
 */

 if(isset($_GET['enter']) && $_SERVER['PHP_AUTH_USER'] != 'nadajto')
 {
  if(!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] != 'nadajto' || $_SERVER['PHP_AUTH_PW'] != 'testy')
  {
   header('WWW-Authenticate: Basic realm="Nadajto"');
   header('HTTP/1.0 401 Unauthorized');
   die(file_get_contents('index.html'));
  }
 }
  elseif($_SERVER['PHP_AUTH_USER'] != 'nadajto')
 {
	die(file_get_contents('index.html'));
 }

 require_once 'includes/config.php';
 require_once 'includes/ets.php';
 require_once 'includes/lib.php';
 require_once 'includes/class.database.php';

 basic_init();
 $db = new database;
 if(!$db->connect()) ShowError('Nie można połączyć z bazą danych.', true);
 $res = $db->query("SELECT * FROM `config`");
 if($res)
 {
	$GLOBALS['CONFIG'] = array();
	while($tmp = $db->fetchAssoc($res, MYSQL_ASSOC))
	{
		$GLOBALS['CONFIG'][$tmp['variable']] = $tmp['value'];
	}
	$db->free($res);
 }
	else
 {
	ShowWarning('<b>Brak danych konfiguracyjnych!</b><br>Serwis nie będzie działać poprawnie.');
 }

 // --- CONTROLLER
 require_once 'includes/config_pages.php';
 if(isset($_GET['qs']))
 {
	$qstr = explode(',', str_ireplace('.html', '', $_GET['qs']));
	$i = 1;
	$key = 'page';
	foreach($qstr as $k => $v)
	{
		$i++;
		if($i % 2 !== 0) $key = trim($v); else $_GET[$key] = trim($v);
	}
	unset($i, $key, $qstr, $v, $k);
 }
	else
 {
	$_GET['page'] = 'start';
 }
 if(!isset($_GET['page']) && isset($_POST['page'])) $_GET['page'] = $_POST['page'];
 if(!isset($_GET['page']) || mb_strlen(trim($_GET['page'])) < 1) $_GET['page'] = 'start';
 if(!array_key_exists($_GET['page'], $pages)) $_GET['page'] = 'start';
 $GLOBALS['current_page'] = $pages[$_GET['page']]['file'];

 // --- HEADER
 $tpl = new stdClass();
 $tpl->TITLE = htmlspecialchars($pages[$_GET['page']]['title']);
 if(strlen(trim($pages[$_GET['page']]['desc'])) > 0) $tpl->METADESC = htmlspecialchars($pages[$_GET['page']]['desc']);
 $tpl->SITEROOT = htmlspecialchars($GLOBALS['SITEROOT']);
 $tpl->SITENAME = htmlspecialchars(SITENAME);
 $tpl->PAGE = $GLOBALS['current_page'];
 $tpl->PAGENAME = $_GET['page'];
 $tpl->DEBUG = DEBUG;
 printt($tpl, 'theme/header.tpl');

 // --- LOAD SUBPAGE
 if(isset($GLOBALS['current_page']) && is_file($GLOBALS['current_page']) && stristr($GLOBALS['current_page'], '.php') && !strstr($GLOBALS['current_page'], '..') && !strstr($GLOBALS['current_page'], '/') && !strstr($GLOBALS['current_page'], '\\'))
  require_once $GLOBALS['current_page'];
   else
   {
	header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found', true, 404);
	ShowWarning('<b>Nie znaleziono podstrony!</b><br>Strona którą próbowałeś obejrzeć nie istnieje.<br>Być może została usunięta lub znjaduje się pod innym adresem.');
   }

 // --- FOOTER
 if(!isset($GLOBALS['nofooter']))
 {
	$tpl = new stdClass();
	$tpl->SQLQ = $db->getQueryCount();
	$tpl->TIME = endmicrotime($mytime);
	$tpl->SITEROOT = htmlspecialchars($GLOBALS['SITEROOT']);
	$tpl->SITENAME = htmlspecialchars(SITENAME);
	$tpl->PAGE = $GLOBALS['current_page'];
	$tpl->PAGENAME = $_GET['page'];
	$tpl->DEBUG = DEBUG;
	printt($tpl, 'theme/footer.tpl');
 }
 unset($db);


# require 'includes/api_kex.php';
# $a = new KEXApi;
# print(htmlspecialchars($a->track('872342085')));
# echo $a->getLastXML();
?>