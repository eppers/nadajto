<?php
 # encoding: UTF-8 © A&R Multimedia
/*
 * This file is copyrighted.
 */

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

 $tpl = new stdClass();




 unset($db);
?>