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
		return '';
	}
	
	/**
	 * Creates or updates the page sent by the user. Requires authentication
	 *
	 * @param	string	$token	the authentication token returned by the login method
	 * @param	string	$content	the new post content (Markdown format)
	 * @param	string	$key	key name of the new page
	 * @param	string	$tags	a list of comma separated tags
	 * @param	string	$title	page title
	 * @return	string	success message if succeeded, error message otherwise
	*/
	public static function post_page($token, $content, $key = '', $tags = '', $title = '')
	{
		return '';
	}
	
	/**
	 * Removes the page identified by the specified key. Requires authentication
	 *
	 * @param	string	$token	the authentication token returned by the login method
	 * @param	string	$key	key name of the page
	 * @return	string	success message if succeeded, error message otherwise
	*/
	public static function get_remove($token, $key)
	{
		return '';
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
}

?>
