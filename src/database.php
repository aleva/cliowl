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
		
		// Creates the user table	
		$query = 'CREATE TABLE ' . Database::tb('user') . '(' .
			'id INT PRIMARY KEY AUTO_INCREMENT, ' .
			'name VARCHAR(50) NOT NULL, ' .
			'password VARCHAR(32) NOT NULL )';
		
		$result = mysql_query($query) or			
			die('Database#create_db error 4: ' . mysql_error());
		
		// Creates the session table
		$query = 'CREATE TABLE ' . Database::tb('session') . '(' .
			'id INT PRIMARY KEY AUTO_INCREMENT, ' .
			'token CHAR(32) NOT NULL, ' .
			'user_id INT NOT NULL, ' .
			'expires_at DATETIME NOT NULL)';
		
		$result = mysql_query($query) or			
			die('Database#create_db error 5: ' . mysql_error());
		
		$query = 'ALTER TABLE ' . Database::tb('session') .
			' ADD CONSTRAINT fk_' . Database::tb('session') . '_user_id' .
			' FOREIGN KEY(user_id)' .
			' REFERENCES ' . Database::tb('user') . '(id)';
		
		$result = mysql_query($query) or			
			die('Database#create_db error 6: ' . mysql_error());
		
		// Creates the tag table
		$query = 'CREATE TABLE ' . Database::tb('tag') . '(' .
			'id INT PRIMARY KEY AUTO_INCREMENT, ' .
			'name VARCHAR(100) NOT NULL, ' .
			'user_id INT NOT NULL)';
		
		$result = mysql_query($query) or			
			die('Database#create_db error 7: ' . mysql_error());
		
		$query = 'ALTER TABLE ' . Database::tb('tag') .
			' ADD CONSTRAINT fk_' . Database::tb('tag') . '_user_id' .
			' FOREIGN KEY(user_id)' .
			' REFERENCES ' . Database::tb('user') . '(id)';
		
		$result = mysql_query($query) or			
			die('Database#create_db error 8: ' . mysql_error());
		
		// Creates the post table
		$query = 'CREATE TABLE ' . Database::tb('post') . '(' .
			'id INT PRIMARY KEY AUTO_INCREMENT, ' .
			'title VARCHAR(255) NOT NULL, ' .
			'key VARCHAR(255) NOT NULL, ' .
			'content TEXT NOT NULL, ' .
			'created_at DATETIME NOT NULL, ' .
			'updated_at DATETIME NOT NULL, ' .
			'user_id INT NOT NULL)';
		
		$result = mysql_query($query) or			
			die('Database#create_db error 9: ' . mysql_error());
		
		$query = 'ALTER TABLE ' . Database::tb('post') .
			' ADD CONSTRAINT fk_' . Database::tb('post') . '_user_id' .
			' FOREIGN KEY(user_id)' .
			' REFERENCES ' . Database::tb('user') . '(id)';
		
		$result = mysql_query($query) or			
			die('Database#create_db error 10: ' . mysql_error());
		
		// Creates the tag-post table
		$query = 'CREATE TABLE ' . Database::tb('tag_post') . '(' .
			'id_tag INT NOT NULL, ' .
			'id_post INT NOT NULL)';
		
		$result = mysql_query($query) or			
			die('Database#create_db error 11: ' . mysql_error());
		
		$query = 'ALTER TABLE ' . Database::tb('tag_post') .
			' ADD CONSTRAINT fk_' . Database::tb('tag_post') . '_user_id' .
			' FOREIGN KEY(user_id)' .
			' REFERENCES ' . Database::tb('tag') . '(id)';
		
		$result = mysql_query($query) or			
			die('Database#create_db error 12: ' . mysql_error());
		
		$query = 'ALTER TABLE ' . Database::tb('tag_post') .
			' ADD CONSTRAINT fk_' . Database::tb('tag_post') . '_user_id' .
			' FOREIGN KEY(user_id)' .
			' REFERENCES ' . Database::tb('post') . '(id)';
		
		$result = mysql_query($query) or			
			die('Database#create_db error 13: ' . mysql_error());
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
