<?php

require 'database.php';

/**
 * Class that represents the CLIOWL public API
*/
class Cliowl
{	
	/**
	 * This class must implement the cliowl API:
	 *
     * GET fetch
     * POST login
     * GET page
     * POST page
     * GET remove
     * POST config
     * GET config
     * POST image
     * GET images
     * GET list
     * GET tags
     * GET backup
	*/

	static $success = '0';
	static $failure = '';
	static $fetch_message = 'CLIOWL SERVER API';

	/**
	 * Checks whether the address called is a CLIOWL server
	 *
	 * @return	string	Message to show that there is a CLIOWL server here
	*/
	public static function get_fetch()
	{
		return Cliowl::$fetch_message;
	}
	
	/**
	 * Tries to login. If login is succeded, returns a token that can be used to make operations that
	 * require authentication 
	 *
	 * @param	string	$user	name of the cliowl user
	 * @param	string	$password	password of the cliowl user
	 * @return	string	token used to make operations that need authentication. Empty if login fails.
	*/
	public static function post_login($user, $password)
	{
		if(Database::login($user, $password))
		{
			if($token = Database::validate_user_session($user))
				return $token;

			$token = md5(rand());
			
			Database::create_session($user, $token);
			return $token;
		}
		else
			return Cliowl::$failure;
	}
	
	/**
	 * Gets a page (from this server) with a specified key
	 *
	 * @param	string	$user	user name of the author (of this page)
	 * @param	string	$key	a key that identifies the page
	 * @return	string	page content
	*/
	public static function get_page($user, $key)
	{
		if($content = Database::get_post_content($user, $key))
			return $content;

		return '';
	}
	
	/**
	 * Creates or updates the page sent by the user. Requires authentication
	 *
	 * @param	string	$token	the authentication token returned by the login method
	 * @param	string	$file	the new post content (Markdown format)
	 * @param	string	$key	key name of the new page
	 * @param	string	$tags	a list of comma separated tags
	 * @param	string	$title	page title
	 * @return	string	success message if succeeded, error message otherwise
	*/
	public static function post_page($token, $file, $key = '', $tags = '', $title = '')
	{
		if(!($user_name = Database::validate_session($token)))
			return Cliowl::$failure;
		
		if($page_id = Database::get_post_id($user_name, $key))
		{
			// Page exists, update page
			$result = Database::update_post($page_id, $file, $tags, $title, $user_name);
		}
		else
		{
			// New page
			$result = Database::create_post($file, $key, $tags, $title, $user_name);
		}

		return $result === true ? Cliowl::$success : Cliowl::$failure;
	}
	
	/**
	 * Removes the page identified by the specified key. Requires authentication
	 *
	 * @param	string	$key	key name of the page
	 * @param	string	$token	the authentication token returned by the login method
	 * @return	string	success message if succeeded, error message otherwise
	*/
	public static function get_remove($key, $token)
	{
		if(!($user_name = Database::validate_session($token)))
			return Cliowl::$failure;

		$result = Database::remove_post($user_name, $key);

		return $result === true ? Cliowl::$success : Cliowl::$failure;
	}
	
	/**
	 * Sets a specific configuration. Requires authentication
	 *
	 * @param	string	$token	the authentication token returned by the login method
	 * @param	string	$key	key name of the configuration option
	 * @param	string	$value	value to set into this configuration option
	 * @return	string	success message if succeeded, error message otherwise
	*/
	public static function post_config($token, $key, $value)
	{
		return '';
	}

	/**
	 * Get a list of posts, filtering by some criteria
	 *
	 * @param	string	user	user whose posts will be retrieved
	 * @param	string	from	posts before this date will not be retrieved
	 * @param	string	to		posts after this date will not be retrieved
	 * @param	string	tags	only posts with all this tags will be brought
	 * @return	string	post list in JSON format, or error message
	*/
	public static function get_list($user, $from = '', $to = '', $tags = '')
	{
		if($result = Database::get_posts($user, $from, $to, $tags))
			return json_encode($result);

		return Cliowl::$failure;
	}
}

?>
