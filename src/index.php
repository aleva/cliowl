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

$index = -1;

foreach ($tokens as $token)
{
	$index++;
	
	if($token != '')
	{
		if($token == 'fetch')
		{
			echo Cliowl::get_fetch();
		}
		else if($token == 'login')
		{
			echo Cliowl::post_login($_POST["user"], $_POST["password"]);
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
				$user = $tokens[$index + 1];
				$key = $tokens[$index + 2];
				
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
				"Unknown action" => $token
			));
			
			break;
		}
	}
}

?>
