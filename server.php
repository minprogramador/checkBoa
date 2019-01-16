<?php

use React\Promise\Deferred;
use React\ChildProcess\Process;
use Nette\Database\Context;

require_once 'vendor/autoload.php';

$user = 'root';
$password = '2019maconhaOk@@';
$dsn = "mysql:host=127.0.0.1;dbname=api_boa";
$database = new Nette\Database\Connection($dsn, $user, $password); // the same arguments as uses PDO

$loop = \React\EventLoop\Factory::create();

$server = new \Legionth\React\Http\Rest\Server();



$loop->addPeriodicTimer(30, function(React\EventLoop\Timer\Timer $timer){

	echo date("Y-m-d H:i:s")." - verifica cookie e proxys..\n";

	$payload = "php main.php";
	runPayload($payload)
		->then(function ($value){
			print_r($value);

	    },
	    function ($reason)  use($request, $next){
	    	echo "\ndeu error...\n";
			 //return new \React\Http\Response(200, array(), 'deu error');	    	
	    }
	);
});


function runPayload($payload) {
	global $loop;
	$deferred = new Deferred();
	$payload = 'php main.php';

	$process = new Process($payload);

	$process->start($loop);

	$process->stdout->on('data', function ($chunk) use (&$dados, $deferred) {
		$resok = str_replace(['start=', '=end'], '', $chunk);
		$deferred->resolve($resok);
	});
	
	$process->on('error', function($e) {
		$deferred->reject($e);
	});

	return $deferred->promise();

}

$server->get('/check', function (\Psr\Http\Message\ServerRequestInterface $request, callable $next) use($loop, $database) {

	$result =  $database->fetchAll('SELECT * FROM contas');
	$result = json_encode($result);
	return new \React\Http\Response(200, array('Content-type' => 'application/json'), $result);
});



// $server->get('/config', function (\Psr\Http\Message\ServerRequestInterface $request, callable $next) {
//     return new \React\Http\Response(200, array(), 'hello');
// });

$socket = new \React\Socket\Server('0.0.0.0:5555', $loop);
$server->listen($socket);
echo "run";
$loop->run();