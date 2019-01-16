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

require_once 'vendor/autoload.php';

$user     = 'root';
$password = '2019maconhaOk@@';
$dsn      = "mysql:host=127.0.0.1;dbname=api_boa";
$database = new Connection($dsn, $user, $password); // the same arguments as uses PDO

$loop   = Factory::create();
$server = new ServerRest();

function runPayload($payload) {
	global $loop;

	$deferred = new Deferred();
	$payload  = 'php main.php';
	$process  = new Process($payload);

	$process->start($loop);

	$process->stdout->on('data', function ($chunk) use ($deferred) {
		$deferred->resolve($resok);
	});
	
	$process->on('error', function($e) {
		$deferred->reject($e);
	});

	return $deferred->promise();
}

$loop->addPeriodicTimer(30, function(Timer $timer) {

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

$server->get('/check', function (ServReqInterf $request, callable $next) use($loop, $database) {

	$result = json_encode($database->fetchAll('SELECT * FROM contas'));
	return new Response(200, array('Content-type' => 'application/json'), $result);

});


$socket = new Server('0.0.0.0:5555', $loop);
$server->listen($socket);
echo "run";
$loop->run();
