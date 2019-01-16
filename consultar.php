<?php

use Nette\Database\Context;
use Api\Boa\Logar;
use Api\Boa\Check;
use Api\Boa\Consultar;
use Api\Boa\utils\Util;

require(dirname(__FILE__).'/vendor/autoload.php');
$user = 'root';
$password = '2019maconhaOk@@';
$dsn = "mysql:host=127.0.0.1;dbname=api_boa";
$database = new Nette\Database\Connection($dsn, $user, $password); // the same arguments as uses PDO


$result = $database->query('SELECT * FROM contas where LENGTH(proxy) > 8 and LENGTH(cookie) > 10 and  status = ?', true);

$con = new Consultar();
$cpf = '00034486577';
foreach ($result as $row) {
	$cookie  = $row->cookie;
	$proxy   = $row->proxy;
	$cookie  = $row->cookie;
	$usuario = $row->usuario;
	if(strlen($cookie) > 10) {
		$con->setCookie($cookie);
		$con->setProxy($proxy);
		$con->setCpf($cpf);
		$res = $con->run();
		if(stristr($res, 'ES FORNECIDAS</s')){
			echo "Consulta ao doc: $cpf = ok $usuario - $proxy";	
			break;		
		}else{
			continue;
		}
	}else{
		$res = 'sem cookie, ingnorar..';
		echo $res;
		continue;
	}
}
