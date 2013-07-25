<?php

/**
 * Class that represents the CLIOWL public API
*/
class Cliowl
{	
	/**
	 * What this class must have:
	 *
     * get config: obtém as configurações atuais de um usuário
     * post style: seta um novo estilo (via css) para as páginas
     * post image: envia uma imagem para ser usada no site
     * get list: obtém uma lista das páginas do usuário (possivelmente alguns filtros serão necessários)
     * get tags: obtém as tags usadas pelo usuário (e a "força" de cada uma)
     * get backup: obtém um arquivo comprimido (p.ex. tar.gz) com os arquivos dos posts de uma data até outra
	*/

	/**
	 * Checks whether the address called is a CLIOWL server
	 *
	 * @return	string	Message to show that there is a CLIOWL server here
	*/
	public static function get_fetch()
	{
		return 'CLIOWL Server API found!';
	}
	
	/**
	 * Tries to login. If login is succeded, returns a token that can be used to make operations that
	 * require authentication 
	 *
	 * @param	string	$password	password of the cliowl user (there is only one user)
	 * @return	string	token used to make operations that need authentication. Empty if login fails.
	*/
	public static function post_login($password)
	{
		return '';
	}
	
	/**
	 * Gets a page (from this server) with a specified key
	 *
	 * @param	string	$key	a key that identifies the page
	 * @return	string	page content
	*/
	public static function get_page($key)
	{
		return '';
	}
	
	/**
	 * Saves the page sent by the user. Requires authentication
	 *
	 * @param	string	$token	the authentication token returned by the login method
	 * @param	string	$content	the new post content (DokuWiki like format)
	 * @param	string	$key	key name of the new page (optional: will be auto generated)
	 * @return	string	blank message if succeded, error message otherwise
	*/
	public static function post_page($token, $content, $key = '')
	{
		return '';
	}
	
	/**
	 * Removes the page identified by the specified key. Requires authentication
	 *
	 * @param	string	$token	the authentication token returned by the login method
	 * @param	string	$key	key name of the page
	 * @return	string	blank message if succeded, error message otherwise
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
	 * @return	string	blank message if succeded, error message otherwise
	*/
	public static function post_config($token, $key, $value)
	{
		return '';
	}
}

?>
