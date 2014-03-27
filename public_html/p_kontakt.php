<?php
 # encoding: UTF-8 © A&R Multimedia
/*
 * This file is copyrighted.
 */

 if(!class_exists('database')) die();
 $tpl = new stdClass();

 if(isset($_GET['msg']) && strlen($_GET['msg']) > 0) $tpl->RETMSG = htmlspecialchars($_GET['msg']);

 $tpl->SITEROOT = htmlspecialchars($GLOBALS['SITEROOT']);
 $tpl->SITENAME = htmlspecialchars(SITENAME);
 $tpl->PAGE = $GLOBALS['current_page'];
 $tpl->PAGENAME = $_GET['page'];
 $tpl->DEBUG = DEBUG;
 printt($tpl, 'theme/kontakt.tpl');
?>