<?php
 # encoding: UTF-8 © A&R Multimedia
/*
 * This file is copyrighted.
 */

 if(!isset($_GET['step']) || !is_numeric($_GET['step']) || $_GET['step'] < 1 || $_GET['step'] > 3) die('Błąd: Niepoprawny parametr wejściowy!');

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

 if($step == 1) ////////////////////////////////////////////////////////////////////////////////////
 {
	if(!isset($_POST['rodzaj'])) $_POST['rodzaj'] = '1';
	if(!isset($_POST['dlu'])) $_POST['dlu'] = '40';
	if(!isset($_POST['szer'])) $_POST['szer'] = '30';
	if(!isset($_POST['wys'])) $_POST['wys'] = '20';
	if(!isset($_POST['waga'])) $_POST['waga'] = '10';
	if(!isset($_POST['niestandardowa'])) $_POST['niestandardowa'] = '0';
	if(!isset($_POST['kraj'])) $_POST['kraj'] = '177';
	if(!isset($_POST['kex_ubezp1'])) $_POST['kex_ubezp1'] = '1';
	if(!isset($_POST['kex_gwar1'])) $_POST['kex_gwar1'] = '1';
	if(!isset($_POST['ups_ubezp1'])) $_POST['ups_ubezp1'] = '1';

	$tpl->RODZAJ = $_POST['rodzaj'];
	$tpl->DLU = $_POST['dlu'];
	$tpl->WYS = $_POST['wys'];
	$tpl->SZER = $_POST['szer'];
	$tpl->WAGA = $_POST['waga'];
	$tpl->NIESTAND = $_POST['niestandardowa'];
	$tpl->KRAJ = $_POST['kraj'];
	$tpl->KEX_UBEZP1 = $_POST['kex_ubezp1'];
	$tpl->KEX_GWAR1 = $_POST['kex_gwar1'];
	$tpl->UPS_UBEZP1 = $_POST['ups_ubezp1'];
 }

 if($step == 2) ////////////////////////////////////////////////////////////////////////////////////
 {
	if(!isset($_POST['nad_typ'])) $_POST['nad_typ'] = '0';
	if(!isset($_POST['odb_typ'])) $_POST['odb_typ'] = '0';

	$tpl->NAD_TYP = $_POST['nad_typ'];
	if(isset($_POST['nad_imie']) && strlen(trim($_POST['nad_imie'])) > 0) $tpl->NAD_IMIE = htmlspecialchars($_POST['nad_imie']);
	if(isset($_POST['nad_nazwisko']) && strlen(trim($_POST['nad_nazwisko'])) > 0) $tpl->NAD_NAZWISKO = htmlspecialchars($_POST['nad_nazwisko']);
	if(isset($_POST['nad_ulica']) && strlen(trim($_POST['nad_ulica'])) > 0) $tpl->NAD_ULICA = htmlspecialchars($_POST['nad_ulica']);
	if(isset($_POST['nad_nrdomu']) && strlen(trim($_POST['nad_nrdomu'])) > 0) $tpl->NAD_NRDOMU = htmlspecialchars($_POST['nad_nrdomu']);
	if(isset($_POST['nad_nrlok']) && strlen(trim($_POST['nad_nrlok'])) > 0) $tpl->NAD_NRLOK = htmlspecialchars($_POST['nad_nrlok']);
	if(isset($_POST['nad_kod1']) && strlen(trim($_POST['nad_kod1'])) > 0) $tpl->NAD_KOD1 = htmlspecialchars($_POST['nad_kod1']);
	if(isset($_POST['nad_kod2']) && strlen(trim($_POST['nad_kod2'])) > 0) $tpl->NAD_KOD2 = htmlspecialchars($_POST['nad_kod2']);
	if(isset($_POST['nad_email']) && strlen(trim($_POST['nad_email'])) > 0) $tpl->NAD_EMAIL = htmlspecialchars($_POST['nad_email']);
	if(isset($_POST['nad_email2']) && strlen(trim($_POST['nad_email2'])) > 0) $tpl->NAD_EMAIL2 = htmlspecialchars($_POST['nad_email2']);
	if(isset($_POST['nad_telef']) && strlen(trim($_POST['nad_telef'])) > 0) $tpl->NAD_TELEF = htmlspecialchars($_POST['nad_telef']);
	if(isset($_POST['nad_nazwa']) && strlen(trim($_POST['nad_nazwa'])) > 0) $tpl->NAD_NAZWA = htmlspecialchars($_POST['nad_nazwa']);
	if(isset($_POST['nad_nip']) && strlen(trim($_POST['nad_nip'])) > 0) $tpl->NAD_NIP = htmlspecialchars($_POST['nad_nip']);

	$tpl->ODB_TYP = $_POST['odb_typ'];
	if(isset($_POST['odb_imie']) && strlen(trim($_POST['odb_imie'])) > 0) $tpl->ODB_IMIE = htmlspecialchars($_POST['odb_imie']);
	if(isset($_POST['odb_nazwisko']) && strlen(trim($_POST['odb_nazwisko'])) > 0) $tpl->ODB_NAZWISKO = htmlspecialchars($_POST['odb_nazwisko']);
	if(isset($_POST['odb_ulica']) && strlen(trim($_POST['odb_ulica'])) > 0) $tpl->ODB_ULICA = htmlspecialchars($_POST['odb_ulica']);
	if(isset($_POST['odb_nrdomu']) && strlen(trim($_POST['odb_nrdomu'])) > 0) $tpl->ODB_NRDOMU = htmlspecialchars($_POST['odb_nrdomu']);
	if(isset($_POST['odb_nrlok']) && strlen(trim($_POST['odb_nrlok'])) > 0) $tpl->ODB_NRLOK = htmlspecialchars($_POST['odb_nrlok']);
	if(isset($_POST['odb_kod1']) && strlen(trim($_POST['odb_kod1'])) > 0) $tpl->ODB_KOD1 = htmlspecialchars($_POST['odb_kod1']);
	if(isset($_POST['odb_kod2']) && strlen(trim($_POST['odb_kod2'])) > 0) $tpl->ODB_KOD2 = htmlspecialchars($_POST['odb_kod2']);
	if(isset($_POST['odb_email']) && strlen(trim($_POST['odb_email'])) > 0) $tpl->ODB_EMAIL = htmlspecialchars($_POST['odb_email']);
	if(isset($_POST['odb_email2']) && strlen(trim($_POST['odb_email2'])) > 0) $tpl->ODB_EMAIL2 = htmlspecialchars($_POST['odb_email2']);
	if(isset($_POST['odb_telef']) && strlen(trim($_POST['odb_telef'])) > 0) $tpl->ODB_TELEF = htmlspecialchars($_POST['odb_telef']);
	if(isset($_POST['odb_nazwa']) && strlen(trim($_POST['odb_nazwa'])) > 0) $tpl->ODB_NAZWA = htmlspecialchars($_POST['odb_nazwa']);
	if(isset($_POST['odb_nip']) && strlen(trim($_POST['odb_nip'])) > 0) $tpl->ODB_NIP = htmlspecialchars($_POST['odb_nip']);
 }

 if($step == 3) ////////////////////////////////////////////////////////////////////////////////////
 {
	if(strlen($_POST['data_nad']) != 10) unset($_POST['data_nad']);
	if(isset($_POST['data_nad'])) $tpl->DATA_NAD = htmlspecialchars($_POST['data_nad']);
	if(isset($_POST['dodat_ubezp']) && $_POST['dodat_ubezp'] == '1') $tpl->DODAT_UBEZP = 1;
	if(isset($_POST['przes_pobr']) && $_POST['przes_pobr'] == '1') $tpl->PRZES_POBR = 1;
	if(isset($_POST['rod']) && $_POST['rod'] == '1') $tpl->ROD = 1;
	if(isset($_POST['rod_expr']) && $_POST['rod_expr'] == '1') $tpl->ROD_EXPR = 1;
	if(isset($_POST['dod_ubez_kw']) && $_POST['dod_ubez_kw'] > 0)
	{
		$kw = str_replace(' ', '', $_POST['dod_ubez_kw']);
		$kw = intval($kw);
		$tpl->DOD_UBEZ_KW = htmlspecialchars($kw);
	}
	if(isset($_POST['przes_pobr_kw']) && $_POST['przes_pobr_kw'] > 0)
	{
		$kw = str_replace(' ', '', $_POST['przes_pobr_kw']);
		$kw = intval($kw);
		$tpl->PRZES_POBR_KW = htmlspecialchars($kw);
	}
 }

 $tpl->SITEROOT = htmlspecialchars($GLOBALS['SITEROOT']);
 $tpl->SITENAME = htmlspecialchars(SITENAME);
 $tpl->PAGE = $GLOBALS['current_page'];
 $tpl->PAGENAME = $GLOBALS['page'];
 $tpl->DEBUG = DEBUG;
 printt($tpl, 'theme/step'.$step.'.tpl');
 unset($db);
?>