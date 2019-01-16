<?php

use Api\Boa\Logar;
use Api\Boa\Check;
use Api\Boa\Consultar;
use Api\Boa\utils\Util;

require_once 'vendor/autoload.php';

$logar = new Logar();

/*
u=20007064572 s=7656 p=138.255.165.86:50095
*/

$usuario = null;
$senha   = null;
$proxy   = null;

if(count($argv) > 1) {
	foreach($argv as $arv){
		if(stristr($arv, 'u=')){
			$usuario = str_replace('u=', '', $arv);
		}elseif(stristr($arv, 's=')){
			$senha = str_replace('s=', '', $arv);
		}elseif(stristr($arv, 'p=')){
			$proxy = str_replace('p=', '', $arv);
		}
	}
}else{
	//die('falta parametros...');
}


if($usuario != null && $senha != null && $proxy != null){
	$logar->setProxy($proxy);
	$logar->setUsuario($usuario);
	$logar->setSenha($senha);
	$cookie = $logar->run();

	if($cookie == 'rede'){
		echo "start=proxyoff::{$usuario}::{$proxy}=end";
	}elseif($cookie == 'invalida'){
		echo "start=contaoff::{$usuario}::{$proxy}=end";
	}
	elseif(strlen($cookie) > 15) {
		echo "start={$cookie}::{$usuario}::{$proxy}=end";
	}else{
		echo "start=false::{$usuario}::{$proxy}=end";	
	}
}else{
	echo "=nada a fazer=";
}
