<?php

use Nette\Database\Context;
use Api\Boa\Logar;
use Api\Boa\Check;
use Api\Boa\Consultar;
use Api\Boa\utils\Util;



require('vendor/autoload.php');
$user = 'root';
$password = '2019maconhaOk@@';
$dsn = "mysql:host=127.0.0.1;dbname=api_boa";
$database = new Nette\Database\Connection($dsn, $user, $password); // the same arguments as uses PDO

function getProxy() {

	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "http://68.183.171.32:5555/proxy",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 15,
	  CURLOPT_CUSTOMREQUEST => "GET",
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		return false;
	} else {

		$response = json_decode($response);

		if(isset($response->proxy->status) && $response->proxy->status === true) {
			return $response->proxy->proxy;
		}else{
			return false;
		}
	}

}

$result = $database->query('SELECT * FROM contas where status = ?', true);

foreach ($result as $row) {
    $usuario = $row->usuario;
    $senha = $row->senha;
    $proxy = $row->proxy;
    $start = $row->start;

	if(strlen($proxy) < 5) {
		$proxy = getProxy();
	}

	if(strlen($cookie) > 5) {

		$check = new Check();
		$check->setcookie($cookie);
		$check->setProxy($proxy);
		$run = $check->run();

		if($run == true){

			echo "\n##### Cookie online >>>> ", $usuario, PHP_EOL;
			$result = $database->query('UPDATE contas SET', [
			    'update' => date("Y-m-d H:i:s")
			], 'WHERE usuario = ?', $usuario);

		}elseif($run == 'rede'){

			$result = $database->query('UPDATE contas SET', [
				'proxy'  => '',
				'cookie' => '',
			    'update' => date("Y-m-d H:i:s"),
			    'status' => true
			], 'WHERE usuario = ?', $usuario);

			echo "\n############Rede off >>>", $usuario, PHP_EOL;

		}else{

			echo "\n############# Cookie offline >>>> $usuario\n";
			$result = $database->query('UPDATE contas SET', [
				'cookie' => '',
			    'update' => date("Y-m-d H:i:s"),
			    'status' => true
			], 'WHERE usuario = ?', $usuario);

		}

	}else{

		$logar = new Logar();
		$logar->setProxy($proxy);
		$logar->setUsuario($usuario);
		$logar->setSenha($senha);
		$cookie = $logar->run();

		if($cookie == 'rede'){
			$proxy = getProxy();
			$logar->setProxy($proxy);
			$cookie = $logar->run();
			echo "segunda verificada...";
			print_r($cookie);
			echo "start=proxyoff::{$usuario}::{$proxy}=end";
			continue;
		}elseif($cookie == 'invalida'){

			$result = $database->query('UPDATE contas SET', [
			    'proxy' => '',
			    'cookie' => '',
			    'status' => false
			], 'WHERE usuario = ?', $usuario);

			echo "start=contaoff::{$usuario}::{$proxy}=end";

		}elseif($cookie === false){

			$result = $database->query('UPDATE contas SET', [
			    'proxy' => '',
			    'cookie' => '',
			    'update' => date("Y-m-d H:i;s"),
			    'status' => true
			], 'WHERE usuario = ?', $usuario);


			echo "deu false, trocar de proxy ?", PHP_EOL;

		}
		elseif(strlen($cookie) > 15) {

			$result = $database->query('UPDATE contas SET', [
			    'proxy' => $proxy,
			    'cookie' => $cookie,
			    'update' => date("Y-m-d H:i:s"),
			    'status' => true
			], 'WHERE usuario = ?', $usuario);
			echo "salvou cookie > ", $usuario;
			//echo $result->getRowCount(); // returns the number of affected rows

			//echo "start={$cookie}::{$usuario}::{$proxy}=end";
			//die;

		}else{
			echo "\n############# debug linha 107\n";
			echo "start=false::{$usuario}::{$proxy}=end";	
			die;

		}

	}

}

//echo $result->getRowCount(); // returns the number of rows if is known

//print_r($row);















