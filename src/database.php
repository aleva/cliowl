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
		
		$con = new mysqli($DB["SERVER"], $DB["USER"], $DB["PASSWORD"], $DB["DATABASE"]);
		
		if($con->connect_error)
			die('Database#connect error: ' . $con->connect_error);
		
		return $con;
	}
	
	/**
	 * Close the connection 
	*/
	public static function disconnect($connection)
	{
		$connection->close();
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
	 * Get the SQL to add a foreign key to a table 
	*/
	public static function fk($src_table, $src_field, $tgt_table, $tgt_field)
	{
		return 'ALTER TABLE ' . $src_table .
			' ADD CONSTRAINT fk_' . $src_table . '_' . $src_field .
			' FOREIGN KEY(' . $src_field . ')' .
			' REFERENCES ' . $tgt_table . '(' . $tgt_field . ')';
	}

	/**
	 * Creates the necessary database tables
	*/
	public static function create_db()
	{
		global $DB;
		
		$con = new mysqli($DB["SERVER"], $DB["USER"], $DB["PASSWORD"]); 
		
		if($con->connect_error)
			die('Database#create_db error 1: ' . $con->connect_error);
		
		// Creates the database
		$query = 'CREATE DATABASE IF NOT EXISTS ' . $DB["DATABASE"];
		
		$result = $con->query($query) or				
			die('Database#create_db error 2: ' . $con->error);

		$con->select_db($DB["DATABASE"]) or 
			die('Database#create_db error 3: ' . $con->error);
		
		// Creates the user table	
		$query = 'CREATE TABLE ' . Database::tb('user') . '(' .
			'id INT PRIMARY KEY AUTO_INCREMENT, ' .
			'name VARCHAR(50) NOT NULL, ' .
			'password VARCHAR(32) NOT NULL )';
		
		$result = $con->query($query) or			
			die('Database#create_db error 4: ' . $con->error);
		
		// Creates the session table
		$query = 'CREATE TABLE ' . Database::tb('session') . '(' .
			'id INT PRIMARY KEY AUTO_INCREMENT, ' .
			'token CHAR(32) NOT NULL, ' .
			'user_id INT NOT NULL, ' .
			'expires_at DATETIME NOT NULL)';
		
		$result = $con->query($query) or			
			die('Database#create_db error 5: ' . $con->error);
		
		$query = Database::fk(Database::tb('session'), 'user_id', Database::tb('user'), 'id');
		
		$result = $con->query($query) or			
			die('Database#create_db error 6: ' . $con->error);
		
		// Creates the tag table
		$query = 'CREATE TABLE ' . Database::tb('tag') . '(' .
			'id INT PRIMARY KEY AUTO_INCREMENT, ' .
			'name VARCHAR(100) NOT NULL, ' .
			'user_id INT NOT NULL)';
		
		$result = $con->query($query) or			
			die('Database#create_db error 7: ' . $con->error);
		
		$query = Database::fk(Database::tb('tag'), 'user_id', Database::tb('user'), 'id');
		
		$result = $con->query($query) or			
			die('Database#create_db error 8: ' . $con->error);
		
		// Creates the post table
		$query = 'CREATE TABLE ' . Database::tb('post') . '(' .
			'id INT PRIMARY KEY AUTO_INCREMENT, ' .
			'title VARCHAR(255) NOT NULL, ' .
			'key_name VARCHAR(255) NOT NULL, ' .
			'content TEXT NOT NULL, ' .
			'created_at DATETIME NOT NULL, ' .
			'updated_at DATETIME NOT NULL, ' .
			'user_id INT NOT NULL)';
		
		$result = $con->query($query) or			
			die('Database#create_db error 9: ' . $con->error);
		
		$query = Database::fk(Database::tb('post'), 'user_id', Database::tb('user'), 'id');
		
		$result = $con->query($query) or			
			die('Database#create_db error 10: ' . $con->error);
		
		// Creates the tag-post table
		$query = 'CREATE TABLE ' . Database::tb('tag_post') . '(' .
			'tag_id INT NOT NULL, ' .
			'post_id INT NOT NULL)';
		
		$result = $con->query($query) or			
			die('Database#create_db error 11: ' . $con->error);
		
		$query = Database::fk(Database::tb('tag_post'), 'tag_id', Database::tb('tag'), 'id');
		
		$result = $con->query($query) or			
			die('Database#create_db error 12: ' . $con->error);
		
		$query = Database::fk(Database::tb('tag_post'), 'post_id', Database::tb('post'), 'id');
		
		$result = $con->query($query) or			
			die('Database#create_db error 13: ' . $con->error);
		
		Database::disconnect($con);
	}
	
	/**
	 * Check if the necessary tables are created
	*/
	public static function db_created()
	{
		global $DB;
		
		$con = new mysqli($DB["SERVER"], $DB["USER"], $DB["PASSWORD"]); 
		
		if($con->connect_error)
			die('Database#db_created error 1: ' . $con->connect_error);

		if(!$con->select_db($DB["DATABASE"])) 
			return false;

		$query = 'SELECT * FROM ' . Database::tb('user') . ';';
		
		return $con->query($query);
	}
	
	/**
	 * Creates the cliowl user
	*/
	public static function create_user($user, $password)
	{
		$con = Database::connect();
		
		$query = $con->prepare("INSERT INTO " . Database::tb('user') . "(name, password) VALUES(?, ?)");
		$query->bind_param('ss', $user, md5($password));

		$result = $query->execute() or				
			die('Database#create_user error 1: ' . $con->error);		

		Database::disconnect($con);
	}
	
	/**
	 * Authenticates an user, returning true if the user and password are correct 
	*/
	public static function login($user, $password)
	{
		Database::connect();
		$con = Database::connect();
		
		$query = $con->prepare("SELECT * FROM " . Database::tb('user') . " WHERE name = ? AND password = ?");
		$query->bind_param('ss', $user, md5($password));

		$result = $query->execute() or				
			die('Database#create_user error 1: ' . $con->error);		
		
		$query->store_result();
		Database::disconnect($con);
		return $query->num_rows == 1;
	}
	
	/**
	 * Return the user name if the token is valid, false otherwise
	*/
	public static function validate_session($token)
	{
		Database::connect();
		
		return $token;
	}
	
	/**
	 * Create a session for the user 
	*/
	public static function create_session($user, $token)
	{
		Database::connect();
		
		return $token;
	}
}

?>
