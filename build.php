<?php

$phar = new Phar('comb.phar', 0, 'dist/csvutil.phar');

// start buffering. Mandatory to modify stub.
$phar->startBuffering();

// Get the default stub. You can create your own if you have specific needs
$defaultStub = $phar->createDefaultStub('index.php');

// Adding files
$phar->buildFromDirectory(__DIR__, '/\.php$/');

// Create a custom stub to add the shebang
$stub = "#!/usr/bin/php \n".$defaultStub;

// Add the stub
$phar->setStub($stub);

$phar->stopBuffering();
