<?php

error_reporting(E_ALL);

define("PIOL_ROOT_PATH",__DIR__);

echo "Loading classloader ...<br />";

require_once("vendor/autoload.php");

echo "Loading test suite ...<br />";

require_once("test/IOTestSuite.php");
     
echo "Running test suite ...<br />";

$ts = new IOTestSuite();

echo "Test suite created!!<br />";

PHPUnit_TextUI_TestRunner::run($ts);

?>