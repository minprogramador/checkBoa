<?php

use React\Promise\PromisorInterface;
use React\ChildProcess\Process;
use React\Promise\Deferred;
use React\EventLoop\Factory;

require("vendor/autoload.php");

// $process = new Process('true');
// $process->start($loop);
// $zen = new BaseZen();
// $zen->go($process);
// $result = Block\await($zen->promise(), $loop, 1.0);
// print_r($result);
// //$this->assertTrue($result);


$loop   = Factory::create();


$process = new Process('sleep 10');
$process->start($loop);

$temp = '';

$process->stdout->on('data', function ($chunk) use(&$temp){
    $temp .= $chunk;
});

$process->on('exit', function ($code, $term) use(&$temp) {
    if ($term === null) {
        print_r($temp);
        echo 'exit with code ' . $code . PHP_EOL;
    } else {
        echo 'terminated with signal ' . $term . PHP_EOL;
    }
});

$loop->run();

