<?php

use React\Promise\Deferred;
use React\ChildProcess\Process;
use Nette\Database\Context;
use Psr\Http\Message\ServerRequestInterface as ServReqInterf;
use React\Socket\Server;
use Legionth\React\Http\Rest\Server as ServerRest;
use Nette\Database\Connection;
use React\EventLoop\Factory;
use React\Http\Response;
use React\EventLoop\Timer\Timer;

require(dirname(__FILE__).'/vendor/autoload.php');

$user     = 'root';
$password = '153356';
$dsn      = "mysql:host=127.0.0.1;dbname=api_boa";
$database = new Connection($dsn, $user, $password);

$loop   = Factory::create();
$server = new ServerRest();

function runPayload($payload) {
	global $loop;
	$result = '';
	$deferred = new Deferred();
	$process  = new Process($payload);

	$process->start($loop);

	$process->stdout->on('data', function ($chunk) use (&$result) {
		$result .= $chunk;
	});
	
	$process->on('error', function($e) use($deferred) {
		$deferred->reject($e);
	});

	$process->on('exit', function ($code, $term) use(&$result, $deferred) {

	    if ($term === null) {
	        print_r($result);
	        echo 'exit with code ' . $code . PHP_EOL;
	    } else {
	       // echo 'terminated with signal ' . $term . PHP_EOL;
	    }
		$deferred->resolve($result);
	});

	return $deferred->promise();
}


//add periodic time que verifica os proxys, em arquivo separado. ->
//>> se tiver menos que o estoque necessario, busca mais.

//rota que consulta o doc.
//rota que adiciona senha
//rota que adiciona proxy

//ligar o retorno do timer abaixo com o bot telegram....

$loop->addPeriodicTimer(1, function(Timer $timer) {
	echo "\nPassou..";
});
$loop->addPeriodicTimer(45, function(Timer $timer) {

	echo date("Y-m-d H:i:s")." - verifica cookie e proxys..\n";

	$payload = "php main.php";
	runPayload($payload)
		->then(function ($value) {
			print_r($value);
	    },
	    function ($reason) {
	    	echo "\ndeu error...\n";
	    }
	);

});

$server->get('/check', 
	function (ServReqInterf $request, callable $next) use ($loop, $database) {
		$result = json_encode($database->fetchAll('SELECT * FROM contas'));
		$header = ['Content-type' => 'application/json'];
		return new Response(200, $header, $result);
});


$socket = new Server('0.0.0.0:5555', $loop);
$server->listen($socket);
echo "run";
$loop->run();
