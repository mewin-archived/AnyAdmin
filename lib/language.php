<?php
	$_LANG = array();

	function loadLanguage($file)
	{
		global $_LANG;
		$lines = file($file);
		foreach ($lines as $k => $line)
		{
			$line = trim($line);
			if (strlen($line) < 1 || $line[0] == "#") continue;
			$pos = strpos($line, "=");
			if ($pos === false)
			{
				echo "Warning: invalid language file!";
			}
			$_LANG[substr($line, 0, $pos)] = substr($line, $pos + 1);
		}
	}

	function getTranslation($name)
	{
		global $_LANG;
		if (isset($_LANG[$name]))
		{
			if (func_num_args() > 1)
			{
				$args = func_get_args();
				array_shift($args);
				return sprintf($_LANG[$name], $args);
			}
			return $_LANG[$name];
		}
		else
		{
			return $name;
		}
	}
	
	function addTranslation($name, $value)
	{
		global $_LANG;
		if (!isset($_LANG[$name]))
		{
			$_LANG[$name] = $value;
		}
	}

	function temp_func_lang($args)
	{
		$name = $args[0];
		return getTranslation($name);
	}	
?>