<?php

use Api\Boa\Logar;
use Api\Boa\Check;
use Api\Boa\Consultar;
use Api\Boa\utils\Util;

require_once 'vendor/autoload.php';

$logar = new Logar();

/*

u=usuario p=138.255.165.86:50095 c="cookie..........."

*/

$usuario = null;
$proxy   = null;
$cookie  = null;

if(count($argv) > 1) {
	foreach($argv as $arv){
		if(stristr($arv, 'p=')){
			$proxy = str_replace('p=', '', $arv);
		}elseif(stristr($arv, 'u=')){
			$usuario = str_replace('u=', '', $arv);
		}elseif(stristr($arv, 'c=')){
			$cookie = str_replace('c=', '', $arv);
		}
	}
}else{
	//die('falta parametros...');
}

if($usuario != null || $proxy != null || $cookie != null) {
	$check = new Check();
	$check->setcookie($cookie);
	$check->setProxy($proxy);
	$run = $check->run();

	if($run == true){
		echo "start=cookieon::{$usuario}::{$proxy}=end";
	}elseif($run == 'rede'){
		echo "start=redeoff::{$usuario}::{$proxy}=end";
	}else{
		echo "start=cookieoff::{$usuario}::{$proxy}=end";
	}
}else{
	echo "=nada a fazer=";
}
