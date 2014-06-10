<?php
	$currentStyle = "";
	function create_page($style, $page = "main")
	{
		global $currentStyle, $_VARS;
		$_VARS["STYLE_BASE"] = "templates/file.php/$style/";
		$currentStyle = $style;
		echo parse_style("templates/$style/$page.html");
	}

	function create_file($style, $file)
	{
		echo parse_file($style, $file);
	}

	function parse_file($style, $file)
	{
		return parse_style("templates/$style/$file");
	}
	
	function parse_style($file)
	{
		global $_VARS;
		$content = file_get_contents($file);
		$regex = "/<!--%([a-zA-Z0-9_]+)%-->/";
		while (preg_match($regex, $content, $arr) > 0)
		{
			$val = "";
			if (isset($_VARS[$arr[1]]))
			{
				$val = $_VARS[$arr[1]];
			}
			$content = str_replace($arr[0], $val, $content);
		}
		$regex = "/<!--@([a-zA-Z0-9_]+)\\(([^\\)]*)(,([^\\)]*))*\\)-->/";
		while (preg_match($regex, $content, $arr) > 0)
		{
			$funcName = "temp_func_{$arr[1]}";
			$params = explode(",", $arr[2]);
			$val = "";
			if (function_exists($funcName))
			{
				$val = $funcName($params);
			}
			$content = str_replace($arr[0], $val, $content);
		}
		$regex = "/<!--\\[\\[(!?)([a-zA-Z0-9_]+)\\]\\]\W*{-->(.*)<!--}{-->(.*)<!--}-->/s";
		while (preg_match($regex, $content, $arr) > 0)
		{
			$val = "";
			$erg = isset($_VARS[$arr[2]]) && $_VARS[$arr[2]] == true;
			if ($arr[1] != "")
			{
				$erg = !$erg;
			}
			if ($erg)
			{
				$val = $arr[3];
			}
			else
			{
				$val = $arr[4];
			}
			$content = str_replace($arr[0], $val, $content);
		}
		$regex = "/<!--\\[\\[(!?)([a-zA-Z0-9_]+)\\]\\]\W*{-->(.*)<!--}-->/s";
		while (preg_match($regex, $content, $arr) > 0)
		{
			$val = "";
			$erg = isset($_VARS[$arr[2]]) && $_VARS[$arr[2]] == true;
			if ($arr[1] != "")
			{
				$erg = !$erg;
			}
			if ($erg)
			{
				$val = $arr[3];
			}
			$content = str_replace($arr[0], $val, $content);
		}
		return $content;
	}

	function temp_func_include($params)
	{
		global $currentStyle;
		return parse_style("templates/$currentStyle/{$params[0]}");
	}

	function temp_func_setting($params)
	{
		global $_SETTINGS;
		if (isset($_SETTINGS[$params[0]]))
		{
			echo $_SETTINGS[$params[0]];
			return $_SETTINGS[$params[0]];
		}
		elseif (count($params) > 1)
		{
			return $params[1];
		}
		else
		{
			return "";
		}
	}
?>