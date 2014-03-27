<?php
 # encoding: UTF-8 © A&R Multimedia
/*
 * This file is copyrighted.
 */

 if($_SERVER['REQUEST_METHOD'] != 'POST') die('Błąd: Brak danych');

 require_once 'includes/config.php';
 require_once 'includes/ets.php';
 require_once 'includes/lib.php';
 require_once 'includes/class.database.php';
 basic_init();

 $db = new database;
 if(!$db->connect()) die('Błąd: Nie można połączyć z bazą danych');
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
	die('Błąd: Brak danych konfiguracyjnych');
 }

 $imi = htmlspecialchars(strip_tags($_POST['imienaz']));
 $ema = htmlspecialchars(strip_tags(str_replace("\n", '', $_POST['email'])));
 $tre = str_replace("\n", '<br>', htmlspecialchars(strip_tags($_POST['tresc'])));
 $odb = explode(',', $GLOBALS['CONFIG']['msg_dest']);
 $res = array();
 foreach($odb as $admmail)
 {
  $res[] = SendMail($admmail, array('NAME'=>$imi, 'EMAIL'=>$ema, 'MESSAGE'=>$tre), 4, $ema);
 }

 $retVal = 1;
 foreach($res as $result)
	if($result !== false)
	{
		$retVal = 0;
		break;
	}

 redirect($GLOBALS['SITEROOT'].'kontakt,msg,'.$retVal.'.html', true);
?>