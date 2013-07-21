<?php

require "cliowl.php";
require "util.php";

$request_uri = $_SERVER["REQUEST_URI"];
$query_string = $_SERVER['QUERY_STRING'];
$document = $_SERVER["PHP_SELF"];
$method = $_SERVER['REQUEST_METHOD'];

$uri_parts = explode("index.php", $document);

if(count($uri_parts) != 2)
{
	die("Bad path.");
}

$tokens = explode("/", $uri_parts[1]);

if(count($tokens) < 2)
{
	die("Bad path.");
}

foreach ($tokens as $token)
{
	if($token != '')
	{
		if($token == 'fetch')
		{
			echo Cliowl::fetch();
		}
		else
		{
			echo Util::error(array(
				"URI" => $request_uri,
				"query string" => $query_string,
				"Document" => $document,
				"Method" => $method,
				"Unknown action" => $token
			));
			
			break;
		}
	}
}

?>
