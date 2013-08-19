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
	 * Create a session for the user 
	*/
	public static function create_session($user, $token)
	{
		global $CLIOWL;
		
		$user_id = Database::get_user_id($user);		
		$con = Database::connect();

		// delete other sessions from this user
		$st = $con->prepare("DELETE FROM " . Database::tb('session') . " WHERE user_id = ?");
		$st->bind_param('i', $user_id);
		
		$result = $st->execute() or				
			die('Database#create_session error 1: ' . $con->error);
		
		// gets session expiration date/time
		$session_dur = $CLIOWL['SESSION'];		
		$date = new DateTime('NOW');
		$date->add(new DateInterval('PT' . $session_dur . 'M'));		
		$expires_at = $date->format('Y-m-d H:i:s');
		
		$st = $con->prepare("INSERT INTO " . Database::tb('session') . "(token, user_id, expires_at)" .
			" VALUES(?, ?, ?)");
			
		$st->bind_param('sis', $token, $user_id, $expires_at);
		
		$result = $st->execute() or				
			die('Database#create_session error 2: ' . $con->error);
	}
	
	/**
	 * Return the user name if the token is valid, false otherwise
	*/
	public static function validate_session($token)
	{
		$con = Database::connect();
		
		$st = $con->prepare("SELECT user_id FROM " . Database::tb('session') . 
			" WHERE token = ? AND expires_at > NOW()");

		$st->bind_param('s', $token);

		$st->execute() or				
			die('Database#validate_session error 1: ' . $con->error);		
		
		$st->bind_result($user_id);
		
		if($st->fetch())
			$result = Database::get_user_name($user_id);
		else
			$result = false;

		$st->close();
		Database::disconnect($con);
		
		return $result;
	}
	
	/**
	 * Get user id for a user name
	*/
	public static function get_user_id($user)
	{
		$con = Database::connect();
		
		$st = $con->prepare("SELECT id FROM " . Database::tb('user') . " WHERE name = ?");
		$st->bind_param('s', $user);

		$st->execute() or				
			die('Database#get_user_id error 1: ' . $con->error);		
		
		$st->bind_result($user_id);
		$st->fetch();
		$st->close();
		Database::disconnect($con);
		
		return $user_id;
	}
	
	/**
	 * Get user name for a user ID
	*/
	public static function get_user_name($id)
	{
		$con = Database::connect();
		
		$st = $con->prepare("SELECT name FROM " . Database::tb('user') . " WHERE id = ?");
		$st->bind_param('i', $id);

		$st->execute() or				
			die('Database#get_user_name error 1: ' . $con->error);		
		
		$st->bind_result($user_name);
		$st->fetch();
		$st->close();
		Database::disconnect($con);
		
		return $user_name;
	}
	
	/**
	 * Get page content
	*/
	public static function get_page_content($user_name, $key)
	{
		$user_id = Database::get_user_id($user_name);

		$con = Database::connect();
		
		$st = $con->prepare("SELECT content FROM " . Database::tb('post') . " WHERE user_id = ? AND key_name = ?");
		$st->bind_param('is', $user_id, $key);

		$st->execute() or				
			die('Database#get_page_content error 1: ' . $con->error);		
		
		$st->bind_result($content);
		
		if($st->fetch())
			$result = $content;
		else
			$result = false;			
		
		$st->close();
		Database::disconnect($con);
		
		return $result;
	}
	
	/**
	 * Get page ID
	*/
	public static function get_page_id($user_name, $key)
	{
		$user_id = Database::get_user_id($user_name);

		$con = Database::connect();
		
		$st = $con->prepare("SELECT id FROM " . Database::tb('post') . " WHERE user_id = ? AND key_name = ?");
		$st->bind_param('is', $user_id, $key);

		$st->execute() or				
			die('Database#get_page_content error 1: ' . $con->error);		
		
		$st->bind_result($id);
		
		if($st->fetch())
			$result = $id;
		else
			$result = false;			
		
		$st->close();
		Database::disconnect($con);
		
		return $result;
	}
	
	/**
	 * Creates a new page (post)
	*/
	public static function create_page($content, $key, $tags, $title, $user_name)
	{
		$user_id = Database::get_user_id($user_name);
		$con = Database::connect();
		
		$st = $con->prepare("INSERT INTO " . Database::tb('post') . 
			"(title, key_name, content, created_at, updated_at, user_id)" .
			" VALUES(?, ?, ?, NOW(), NOW(), ?)");
			
		$st->bind_param('sssi', $title, $key, $content, $user_id);
		
		$result = $st->execute() or				
			die('Database#create_page error 1: ' . $con->error);
		
		return true;
	}
	
	/**
	 * Updates an existing page (post)
	*/
	public static function update_page($page_id, $content, $tags, $title)
	{
		// If title is empty, do not update it
	}
}

?>
