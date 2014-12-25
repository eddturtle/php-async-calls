<?php

$start = time(	);

error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'async.php';

//$cmd = "curl -X POST https://sendgrid.com/api/mail.send.json -d api_user=app18440694@heroku.com -d api_key=ep7phwqi -d to=eddturtle@live.co.uk -d toname=Edd -d subject=Test -d from=edd@designedbyaturtle.co.uk -d text=testing";

 $cmd = 'function add($a, $b) { return $a + $b } sleep(3); add(1, 2); exit(1);';

// $cmd = "";

$async = new Async();

for ($i=0; $i<5; $i++) {
	$async->queue($cmd);
}

echo '<br />Complete';
echo '<br />Random Number: '.rand(1, 100);
echo '<br />Time: ' . (microtime(true) - $start);
echo '<br />';