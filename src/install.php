<?php
	/**
	 * DELETE THIS FILE FROM YOUR SERVER AFTER DOING THE FOLLOWING INSTRUCTIONS:
	 *
	 * 1 - Copy all cliowl files to your server location
	 * 2 - Fill database configuration in config.php file
	 * 2 - Access this file (install.php) through your web browser
	*/
	
	include_once 'config.php';
	require "database.php";
	
	if($DB['DRIVER'] == '' || $DB['SERVER'] == '' || $DB['DATABASE'] == '' || $DB['USER'] == '')
	{
		$msg = 'There are missing configuration fields. Make sure you have filled ' .
			'the config.php file correctly!';
	}
	else if(isset($_POST["user"]) && isset($_POST["password"]))
	{
		if(!Database::db_created())
		{
			Database::create_db();
			Database::create_user($_POST["user"], $_POST["password"]);
			$msg = 'Installation completed successfully! Please, delete the install.php file.';
		}
	}
?>
<html>
	<head>
		<title>cliowl :: Command Line Interface Open Weblog - Installation</title>
		<style type="text/css">
			p.message { color: red; }
		</style>
	</head>
	<body>
		<h1>cliowl :: Command Line Interface Open Weblog - Installation</h1>

		<p class="message"><?php if(isset($msg)) echo $msg; ?></p>

		<h2>cliowl Configuration</h2>
		<p>The fields below are related to cliowl configuration.
			They define the user and password for the cliowl blog
		</p>
		<form action="install.php" method="POST">
			<p>
				<label>User:</label>
				<input type="text" name="user" />
			</p>
			<p>
				<label>Password:</label>
				<input type="password" name="password" />
			</p>
			<p>
				<input type="submit" name="submit" value="ok" />
			</p>
		</form>
	</body>
</html>
