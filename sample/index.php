<?php

// Just in case: show any errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Import Library, either directly or through composer
require __DIR__.'/../lib/Async.php';

// Create some code to execute, as a string
// We'll sleep for 10 seconds to illustrate the async is working
// (remember to get the PHP syntax correct here)
$cmd = "sleep(10);";

// Create the async object, either simply (as show) or with options:
// e.g. new Async([
//          'debug'     => true,            // Will Echo out data along the way
//          'cleanup'   => false,           // Leave the temp files around after (for debug & inspection only)
//          'async      => false            // Turns async off, also useful for debugging & seeing the difference
//          'type'      => Async::TYPE_RAW  // Turns off php execution, just runs as a basic command
//      ]);
$async = new Async([
    'debug' => true
]);

// Add the code we created earlier to the queue
$async->queue($cmd);