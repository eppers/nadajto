<?php

/*
 * Bootstrap start
 */
 function bootstrap_start() {
     define('DEBUG', true);
  if(DEBUG) {
	error_reporting(E_ERROR | E_WARNING | E_USER_ERROR | E_USER_WARNING);
	ini_set('display_errors', 'On');
  }
   else {
	error_reporting(E_ERROR);
	ini_set('display_errors', 'Off');
  }
  $GLOBALS['REALPATH'] = preg_replace('@(\/lib)$@i', '', str_replace('\\', '/', __DIR__));
  $GLOBALS['REALPATH'] = rtrim($GLOBALS['REALPATH'], " \t\\\n\r/");

  session_save_path($GLOBALS['REALPATH'].'/temp');
  session_name('sesid');
  session_start();
  ob_implicit_flush(false);
  ob_start();
  header('Content-Type: text/html; charset=UTF-8');
  header('Cache-Control: public');
  header('Pragma: cache');
  $dn = rtrim(dirname($_SERVER['PHP_SELF']), " \t\\\n\r/");
  if(mb_strlen($dn) < 1 || $dn{mb_strlen($dn)-1} != '/') $dn.= '/';
  $GLOBALS['SITEURL'] = $_SERVER['HTTP_HOST'].$dn;
  $GLOBALS['SITEURI'] = (stristr($_SERVER['REQUEST_URI'], 'www.') ? 'www.' : '').$GLOBALS['SITEURL'];
  $GLOBALS['SITEROOT'] = 'http'.(!empty($_SERVER['HTTPS']) ? 's' : '').'://'.$GLOBALS['SITEURI'];
  if(get_magic_quotes_gpc() == 1 && count($_POST) > 0) 
  {
   foreach($_POST as $k => $v) 
    if(!is_array($_POST[$k])) $_POST[$k] = stripslashes($v);
	 else
	  foreach($_POST[$k] as $kt => $vt) if(!is_array($_POST[$k][$kt])) $_POST[$k][$kt] = stripslashes($vt);
  }
  if(get_magic_quotes_gpc() == 1 && count($_GET) > 0) 
  {
   foreach($_GET as $k => $v)
    if(!is_array($_GET[$k])) $_GET[$k] = stripslashes($v);
	 else
	  foreach($_GET[$k] as $kt => $vt) if(!is_array($_GET[$k][$kt])) $_GET[$k][$kt] = stripslashes($vt);
  }

 }

 function couriers_start($parcelType = 1){
     $courArray = array();
     $couriers = \Model::factory('Courier')->find_many();
     if(count($couriers)>0) {
          foreach($couriers as $courier) {
              if($courierPrice = $courier->prices()->where('parcel_type',$parcelType)->find_one())
                  $courArray[strtolower($courier->name)] = array('id'=>$courier->id_courier, 'name'=>$courier->name, 'price'=>$courierPrice->price);
              else  continue;
          }
      }
      
      return $courArray;
 }
 
/*
 * Send email
 * 
 * return boolean or -1
 */
 function SendMail($email, $dane, $nrszablonu, $reply = false, $attach = false)
 {
   global $db;
   //if(mb_strlen($email) < 2 || !stristr($email, '@') || !is_array($dane) || !is_numeric($nrszablonu) || $nrszablonu < 1) return false;
   require_once $GLOBALS['REALPATH'].'/vendor/PHPMailer/class.phpmailer.php';
   $mail = new PHPMailer();
   $mail->CharSet  = 'UTF-8';
   $mail->IsHTML(true);
   $mail->Hostname = $_SERVER['HTTP_HOST'];
   $mail->Sender   = $GLOBALS['CONFIG']['em_address'];
   $mail->From     = $GLOBALS['CONFIG']['em_address'];
   $mail->FromName = $GLOBALS['CONFIG']['em_sender'];
   if(mb_strlen(trim($GLOBALS['CONFIG']['em_host'])) > 0)
   {
    if(!strstr($GLOBALS['CONFIG']['em_host'], ':'))
	{
     $mail->Host     = $GLOBALS['CONFIG']['em_host'];
     $mail->Port     = 25;
	}
	 else
	{
	 $tx = explode(':', $GLOBALS['CONFIG']['em_host']);
	 $mail->Host     = $tx[0];
     $mail->Port     = intval($tx[1]);
	 unset($tx);
	}
    $mail->Username = $GLOBALS['CONFIG']['em_login'];
    $mail->Password = $GLOBALS['CONFIG']['em_passw'];
    $mail->Mailer   = 'smtp';
    $mail->SMTPDebug= false;
    if($GLOBALS['CONFIG']['em_auth'] == 'yes')  {
      $mail->SMTPAuth = true;
      $mail->SMTPSecure = "tls"; #for gmail 
    }
   }
    else
   {
    $mail->Mailer   = 'mail';
   }

   $res = Model::factory('Message')->where_in('numerid', array('1', intval($nrszablonu)))->order_by_asc('numerid')->limit(2)->find_many();

   $tpl = $res[0];
   $dat = $res[1];
   
   if($dat->active == 'yes')
   {
    $tpl->body = str_replace('{BODY}', $dat->body, $tpl->body);
    $tpl->body = preparePict($mail, $tpl->body);
	$alter = html2txt(preg_replace('@<style(.*)<\/style>@isu', '', $tpl->body));

	$dane['SITEURL'] = str_ireplace('admin/', '', $GLOBALS['SITEROOT']);
	$dane['SITEEMAIL'] = $GLOBALS['CONFIG']['em_address'];
	$dane['SITENAME'] = 'NadajTo.pl';
	$search = array('@<script[^>]*?>.*?</script>@siu','@<![\s\S]*?--[ \t\n\r]*>@');
	$tpl->body = str_replace('%7B', '{', $tpl->body);
	$tpl->body = str_replace('%7D', '}', $tpl->body);
	$alter = str_replace('%7B', '{', $alter);
	$alter = str_replace('%7D', '}', $alter);
	foreach($dane as $k => $v)
	{
	 $tpl->body = str_ireplace('{'.$k.'}', preg_replace($search, '', $v), $tpl->body);
     $alter = str_ireplace('{'.$k.'}', html2txt($v), $alter);
	 $dat->subject = str_ireplace('{'.$k.'}', strip_tags($v), $dat->subject);
	}
	if(is_array($attach) && count($attach) > 0)
	{
	 foreach($attach as $nazwa => $plik)
	  $mail->AddAttachment($plik, $nazwa, 'base64');
         //print 'jest attach';
	}
    $mail->Body = $tpl->body;
    $mail->AltBody = trim(str_replace("\n ", "\n", preg_replace('/[ ]+/', ' ', $alter)));
    $mail->Subject = $dat->subject;
	$mail->AddAddress($email);
	if($reply && strstr($reply, '@')) $mail->AddReplyTo($reply);
	$return = $mail->Send();
	$mail->ClearAllRecipients();
	$mail->ClearAttachments();
	if(!$return) return $mail->ErrorInfo;
	return true;
   }
   else { return -1; }
 }

 /*
  * Prepare pictures - insert picture into the email
  * 
  * @return string
  */
 
 function preparePict(&$mail, $buffer)
 {
   $doc = new DOMDocument('1.0', 'UTF-8');
   @$doc->loadHTML('<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"> '.$buffer);
   $foto = $doc->getElementsByTagName('img');
   $cidcnt = 0;
   foreach($foto as $el)
   {
    $src = $el->getAttribute('src');
    if(stristr($src, 'res/'))
    {
     $cidcnt++;
     $pth = pathinfo($src);
     $cid = 'im'.$cidcnt.$pth['filename'];
     $plik = '/res/'.preg_replace('@(.*)?(res\/)@i', '', $src);
     if(is_file($GLOBALS['REALPATH'].'/public_html/'.$plik) && is_readable($GLOBALS['REALPATH'].'/public_html/'.$plik))
     {
      $mimes = array('gif'=>'image/gif','png'=>'image/x-png','jpeg'=>'image/pjpeg','jpg'=>'image/pjpeg','jpe'=>'image/pjpeg');
      $el->setAttribute('src', 'cid:'.$cid);
      $mail->AddEmbeddedImage($GLOBALS['REALPATH'].'/public_html/'.$plik, $cid, basename($plik), 'base64', $mimes[mb_strtolower($pth['extension'])]);
     }
    }
   }
   return preg_replace('@(.*)<body>@usi', '', preg_replace('@<\/body>(.*)@usi', '', $doc->saveHTML()));
 }
 
 /*
  * Change html to txt (email)
  * 
  * @return string
  */

 function html2txt($s)
 {
  $s = strip_javascript($s);
  $s = preg_replace('@<script[^>]*?>.*?</script>@isu', '', $s);
  $s = preg_replace('@<style[^>]*?>.*?</style>@isu', '', $s);
  $s = strip_tags($s, '<a><br><br/><p>');
  $s = str_replace("\r", '', $s);
  $s = str_replace("\t", '', $s);
  $s = preg_replace('@<br[^>]*?>@isu', '<br>', $s);
  $s = preg_replace('@<p[^>]*?>(.*?)</p>@isu', '<br>$1<br>', $s);
  $s = str_ireplace('<br>', "\n", $s);
  if(stristr($s, '<a')) $s = preg_replace('@<a(\shref="(.*)?")?>(.*?)</a>@siu', '$3 ($2)', $s);
  $s = preg_replace("@\n(\n+)@isu", "\n\n", strip_tags($s));
  return html_entity_decode($s);
 }
 
 /*
  * Remove java scripts from string
  * 
  * @return string
  */
  function strip_javascript($filter)
 {
  $filter = preg_replace("/href=(['\"]).*?javascript:(.*)?\\1/i", "onclick=' $2 '", $filter);
  while(preg_match("/<(.*)?javascript.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", $filter)) $filter = preg_replace("/<(.*)?javascript.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", "<$1$3$4$5>", $filter);
  while(preg_match("/<(.*)?:expr.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", $filter)) $filter = preg_replace("/<(.*)?:expr.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", "<$1$3$4$5>", $filter);
  while(preg_match("/<(.*)?\s?on.+?=?\s?.+?(['\"]).*?\\2\s?(.*)?>/i", $filter)) $filter = preg_replace("/<(.*)?\s?on.+?=?\s?.+?(['\"]).*?\\2\s?(.*)?>/i", "<$1$3>", $filter);
  return $filter;
 }
 
 /*
  * Show warning
  */
  function ShowWarning($msg) {
      print('<div class="warning"><p>'.$msg.'</p></div>');
 }
 
?>
