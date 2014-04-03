<?php

$GLOBALS['normalizeChars'] = array(
    '�'=>'S', '�'=>'s', '?'=>'Dj','�'=>'Z', '�'=>'z', 'A'=>'A', '�'=>'A', '�'=>'A', 'A'=>'A', '�'=>'A', 
    'A'=>'A', '�'=>'A', 
    'A'=>'A', '�'=>'C', 'E'=>'E', '�'=>'E', '�'=>'E', '�'=>'E', 'I'=>'I', '�'=>'I', '�'=>'I', 
    'I'=>'I', 'N'=>'N', 'O'=>'O', '�'=>'O', '�'=>'O', 'O'=>'O', '�'=>'O', 'O'=>'O', 'U'=>'U', '�'=>'U', 
    'U'=>'U', '�'=>'U', '�'=>'Y', '?'=>'B', '�'=>'Ss','a'=>'a', '�'=>'a', '�'=>'a', 'a'=>'a', '�'=>'a', 
    'a'=>'a', 'a'=>'a', '�'=>'c', '�'=>'e', '�'=>'e', 'e'=>'e', '�'=>'e', 'i'=>'i', '�'=>'i', '�'=>'i', 
    'i'=>'i', '?'=>'o', 'n'=>'n', 'o'=>'o', '�'=>'o', '�'=>'o', 'o'=>'o', '�'=>'o', 'o'=>'o', 'u'=>'u', 
    '�'=>'u', 'u'=>'u', '�'=>'y', '�'=>'y', '?'=>'b', 'y'=>'y', 'f'=>'f',
    //PL
    '�'=>'A', '�'=>'C', '�'=>'E', '�'=>'L', '�'=>'N', '�'=>'O', '�'=>'S', '�'=>'Z', '�'=>'Z',
    '�'=>'a', '�'=>'c', '�'=>'e', '�'=>'l', '�'=>'n', '�'=>'o', '�'=>'s', '�'=>'z', '�'=>'z'
);
 
function cleanForShortURL($toClean) {
    $toClean     =     str_replace('&', '-and-', $toClean);
    $toClean = strtr($toClean, $GLOBALS['normalizeChars']);
    $toClean     =    trim(preg_replace('/[^\w\d_ -]/si', '', $toClean));//remove all illegal chars
    $toClean     =     str_replace(' ', '-', $toClean);
    $toClean     =     str_replace('--', '-', $toClean);
    
   
    

    
    if (function_exists('mb_strtolower')) { 
     return mb_strtolower($toClean); 
   } else { 
     return strtolower($toClean); 
   } 
   
}

function clearName($name) {
    $allow = '/[^\p{L}\s0-9\-_\.\/]/u';
    $name = preg_replace($allow, "", trim($name));
    
    if(empty($name)) return false;
    else  return $name;
}

function clearPhone($name) {
    $allow = '/[^0-9]/u'; #$allow = '/[^\s0-9\-_\+\(\)]/u';
    $name = preg_replace($allow, "", $name);
    
    if(empty($name)) return false;
    else  return $name;
}

function onlyNumber($name) {
    $allow = '/[^0-9]/u';
    $name = preg_replace($allow, "", $name);
    
    if(empty($name)) return false;
    else  return $name;
}

function onlyLetter ($name) {
    $allow = '/[^\p{L}\s\-]/u';
    $name = preg_replace($allow, "", trim($name));
    
    if(empty($name)) return false;
    else  return $name;
}

function clearZip($name) {
    $allow = '/[^0-9\-]/u';
    $name = preg_replace($allow, "", $name);
    
    if(empty($name)) return false;
    else  return $name;
}


function clear_spec_char($text){ 
   $tabela = Array( 
   //WIN 
    "\xb9" => "a", "\xa5" => "A", "\xe6" => "c", "\xc6" => "C", 
    "\xea" => "e", "\xca" => "E", "\xb3" => "l", "\xa3" => "L", 
    "\xf3" => "o", "\xd3" => "O", "\x9c" => "s", "\x8c" => "S", 
    "\x9f" => "z", "\xaf" => "Z", "\xbf" => "z", "\xac" => "Z", 
    "\xf1" => "n", "\xd1" => "N", 
   //UTF 
    "\xc4\x85" => "a", "\xc4\x84" => "A", "\xc4\x87" => "c", "\xc4\x86" => "C", 
    "\xc4\x99" => "e", "\xc4\x98" => "E", "\xc5\x82" => "l", "\xc5\x81" => "L", 
    "\xc3\xb3" => "o", "\xc3\x93" => "O", "\xc5\x9b" => "s", "\xc5\x9a" => "S", 
    "\xc5\xbc" => "z", "\xc5\xbb" => "Z", "\xc5\xba" => "z", "\xc5\xb9" => "Z", 
    "\xc5\x84" => "n", "\xc5\x83" => "N", 
   //ISO 
    "\xb1" => "a", "\xa1" => "A", "\xe6" => "c", "\xc6" => "C", 
    "\xea" => "e", "\xca" => "E", "\xb3" => "l", "\xa3" => "L", 
    "\xf3" => "o", "\xd3" => "O", "\xb6" => "s", "\xa6" => "S", 
    "\xbc" => "z", "\xac" => "Z", "\xbf" => "z", "\xaf" => "Z", 
    "\xf1" => "n", "\xd1" => "N", 
    //I to co nie potrzebne 
   "$" => "-", "!" => "-", "@" => "-", "#" => "-", "%" => "-"); 

   return strtr($text,$tabela); 
}  

function array_find($needle, $haystack)
{
   foreach ($haystack as $key=>$item)
   {
      if (strpos($item, $needle) !== FALSE)
      {
         return $key;
         break;
      }
   }
   
   return false;
}

function Utf2Iso($str) {
    return iconv("utf-8", "iso-8859-2", $str);
}

function setProperZipType($zip) {
  if(strpos($zip,'-')===false)
    $zip = substr_replace($zip, '-', 2, 0);
  return $zip;
}

?>
