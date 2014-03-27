<?php
 # encoding: UTF-8 © A&R Multimedia
/*
 * This file is copyrighted.
 */

 if(!isset($_GET['name']) || strlen(trim($_GET['name'])) < 2) die('');
 if(!isset($_GET['surname']) || strlen(trim($_GET['surname'])) < 2) die('');

 require_once 'includes/config.php';
 require_once 'includes/ets.php';
 require_once 'includes/lib.php';
 require_once 'includes/class.database.php';

 basic_init();
 $db = new database;
 if(!$db->connect()) ShowError('Nie można połączyć z bazą danych.', true);

 $plog = mb_substr(trim($_GET['name']), 0, 3).mb_substr(trim($_GET['surname']), 0, 3);
 $pval = rand(100, 999);

 $iter = 0;
 while(1 == 1)
 {
	$res = $db->query("SELECT `login` FROM `users` WHERE `login`='".$db->escape($plog.$pval)."'");
	$ile = $db->rows($res);
	$db->free($res);
	if($ile < 1) break;
	$iter++;
	if($iter > 100) die('');
	$pval = rand(100, 999);
 }
 echo $plog.$pval;
 unset($db);
?>