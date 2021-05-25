<?php

error_reporting(E_ALL);

if (php_sapi_name() != 'cli') {
    throw new Exception('invalid interface');
}

$phpSocket = fsockopen('php', 9000);

if (empty($phpSocket)) {
    throw new Exception('connection error');
}

// fcgi GET /status HTTP/1.1
$packet = '"\u0001\u0001\u0000\u0000\u0000\b\u0000\u0000\u0000\u0001\u0000\u0000\u0000\u0000\u0000\u0000\u0001\u0004' .
    '\u0000\u0000\u0000?\u0001\u0000\u000f\u0007SCRIPT_FILENAME\/status\u000b\u0007SCRIPT_NAME\/status\u000e\u0003' .
    'REQUEST_METHODGET\u0000\u0001\u0004\u0000\u0000\u0000\u0000\u0000\u0000\u0001\u0005\u0000\u0000\u0000\u0000\u0000\u0000"';

fwrite($phpSocket, json_decode($packet));
echo fread($phpSocket, 4096);
fclose($phpSocket);
