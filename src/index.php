<?php

require "cliowl.php";
require "util.php";

$request_uri = $_SERVER['REQUEST_URI'];
$query_string = $_SERVER['QUERY_STRING'];
$document = $_SERVER['PHP_SELF'];
$method = $_SERVER['REQUEST_METHOD'];

$uri_parts = explode("index.php", $document);

if(count($uri_parts) != 2)
{
	die("Bad path.");
}

$tokens = explode("/", $uri_parts[1]);
$tokens = array_slice($tokens, 1);
$ntokens = count($tokens);

if($ntokens < 1)
	die("Bad path.");

if($ntokens > 1 && $tokens[$ntokens - 1] == '')
{
	$tokens = array_slice($tokens, 0, $ntokens - 1);
	$ntokens = count($tokens);
}

$token = strtolower($tokens[0]);

if($token == 'fetch' && $ntokens == 1)
{
	echo Cliowl::get_fetch();
}
else if($token == 'login' && $ntokens == 1)
{
	echo Cliowl::post_login($_POST["user"], $_POST["password"]);
}
else if($token == 'remove' && $ntokens == 3)
{
	echo Cliowl::get_remove($tokens[1], $tokens[2]);
}
else if($token == 'list' && $ntokens >= 2 && $ntokens <= 5)
{
	$user = $tokens[1];
	$date_from = $ntokens > 2 ? $tokens[2] : '';
	$date_to = $ntokens > 3 ? $tokens[3] : '';
	$tags = $ntokens > 4 ? $tokens[4] : '';

	echo Cliowl::get_list($user, $date_from, $date_to, $tags);
}
else if($token == 'page')
{
	if($method == 'POST')
	{
		$fileContent = '';
		
		if($_FILES['file']['size'] > 0)
		{
			$fileTmpName  = $_FILES['file']['tmp_name'];
			$fileSize = $_FILES['file']['size'];

			$fp = fopen($fileTmpName, 'r');
			$fileContent = fread($fp, filesize($fileTmpName));
			fclose($fp);
		}
		
		echo Cliowl::post_page(
			$_POST["token"], $fileContent, $_POST["key"], $_POST["tags"], $_POST["title"]);
	}
	else
	{
		$user = $tokens[1];
		$key = $tokens[2];
		
		echo Cliowl::get_page($user, $key);
	}
}		
else
{
	echo Util::error(array(
		"URI" => $request_uri,
		"query string" => $query_string,
		"Document" => $document,
		"Method" => $method,
		"Action" => $token
	));
}

?>
