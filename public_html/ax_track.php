<?php
 # encoding: UTF-8 © A&R Multimedia
/*
 * This file is copyrighted.
 */

 if(!isset($_GET['step']) || !is_numeric($_GET['step']) || $_GET['step'] != 4) die('Błąd: Niepoprawny parametr wejściowy!');

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
	ShowError('Błąd: Brak danych konfiguracyjnych!', true);
 }

 $step = intval($_GET['step']);
 $tpl = new stdClass();

 # sprawdź K-EX
 if(strlen(trim($_GET['cno'])) == 9 && intval($_GET['cno']) == trim($_GET['cno']))
 {
	require 'includes/api_kex.php';
	$a = new KEXApi;
	$dane = $a->track('872342085');
	if($dane === false)
	{
		trigger_error('KEXApi: Nieznany błąd', E_USER_WARNING);
	}
	 elseif(is_string($dane))
	{
		trigger_error('KEXApi: '.$dane, E_USER_WARNING);
	}
	 elseif(is_array($dane))
	{
		$tpl->DANE = 'K-EX: '.intval($_GET['cno']);
		foreach($dane['rows'] as $i => $status)
		{
			$tpl->hist[$i]->DATA = $status->DATA;
			$tpl->hist[$i]->STATUS = htmlspecialchars($status->STATUS);
			$tpl->hist[$i]->MIASTO = htmlspecialchars($status->MIASTO);
			$tpl->hist[$i]->KOMENT = htmlspecialchars($status->KOMENT);
		}
	}
	 else
	{
		trigger_error('KEXApi: Błąd skryptu', E_USER_WARNING);
	}
 }

 $tpl->SITEROOT = htmlspecialchars($GLOBALS['SITEROOT']);
 $tpl->SITENAME = htmlspecialchars(SITENAME);
 $tpl->PAGE = $GLOBALS['current_page'];
 $tpl->PAGENAME = $GLOBALS['page'];
 $tpl->DEBUG = DEBUG;
 printt($tpl, 'theme/track.tpl');
 unset($db);
?>