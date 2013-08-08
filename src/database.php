<?php

include_once 'config.php';

/**
 * This class acts as an interface with the database
*/
class Database
{
	/**
	 * Connects with the database
	*/
	public static function connect()
	{
		global $DB;
		
		mysql_connect($DB["SERVER"], $DB["USER"], $DB["PASSWORD"]) or 
			die('Database#connect error 1: ' . mysql_error());

		mysql_select_db($DB["DATABASE"]) or 
			die('Database#connect error 2: ' . mysql_error());
	}
	
	/**
	 * Get table name with prefix
	*/
	public static function tb($table)
	{
		global $DB;		
		return $DB["TB_PREFIX"] . strtolower($table);
	}

	/**
	 * Creates the necessary database tables
	*/
	public static function create_db()
	{
		global $DB;
		
		mysql_connect($DB["SERVER"], $DB["USER"], $DB["PASSWORD"]) or 
			die('Database#create_db error 1: ' . mysql_error());
		
		// Creates the database
		$query = 'CREATE DATABASE IF NOT EXISTS ' . $DB["DATABASE"];
		
		$result = mysql_query($query) or				
			die('Database#create_db error 2: ' . mysql_error());

		mysql_select_db($DB["DATABASE"]) or 
			die('Database#create_db error 3: ' . mysql_error());
			
		$query = 'CREATE TABLE ' . Database::tb('user') . '(' .
			'id INT PRIMARY KEY AUTO_INCREMENT, ' .
			'name VARCHAR(50) NOT NULL, ' .
			'password VARCHAR(32) NOT NULL )';
		
		$result = mysql_query($query) or			
			die('Database#create_db error 4: ' . mysql_error());
	}
	
	/**
	 * Check if the necessary tables are created
	*/
	public static function db_created()
	{
		global $DB;
		
		mysql_connect($DB["SERVER"], $DB["USER"], $DB["PASSWORD"]) or 
			die('Database#db_created error 1: ' . mysql_error());

		if(!mysql_select_db($DB["DATABASE"])) 
			return false;

		$query = 'SELECT * FROM ' . Database::tb('user') . ';';
		
		return mysql_query($query);
	}
	
	/**
	 * Creates the cliowl user
	*/
	public static function create_user($user, $password)
	{
		Database::connect();
		
		$user2 = mysql_real_escape_string($user);
		$password2 = mysql_real_escape_string(md5($password));
		
		$query = "INSERT INTO " . Database::tb('user') . "(name, password)" .
			" VALUES('" . $user2 . "', '" . $password2 . "')";

		$result = mysql_query($query) or				
			die('Database#create_user error 1: ' . mysql_error());		
	}
	
	public static function login($user, $password)
	{
		Database::connect();
		
		$token = '';
		
		// TODO: Check if this user exists with this password		
		// TODO: Deletes all sessions of this user		
		// TODO: Create a token and a session for this user
		
		return $token;
	}
}

?>
