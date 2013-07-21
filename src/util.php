<?php

class Util
{
	public static function error($data)
	{
		$msg = "cliowl API :: ";
		
		foreach($data as $k => $v)
		{
			$msg .= $k . ": { " . $v . " } | ";
		}
		
		return $msg;
	}
}

?>
