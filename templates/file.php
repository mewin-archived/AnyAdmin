<?php
	function mimeForExt($ext)
	{
		switch (strtolower($ext))
		{
			case "css":
				return "text/css";
			case "png":
				return "image/png";
			case "jpg":
			case "jpe":
			case "jpeg":
				return "image/jpeg";
			default:
				return "text/html";
		}
	}
	
	require_once("../lib/styler.php");
	require_once("../lib/anyadmin.php");
	require_once("../lib/session.php");
	
	$split = explode("/", trim($_SERVER["PATH_INFO"], "/"), 2);
	$style = $split[0];
	$pos = strrpos($split[1], ".");
	$mime = "text/html";
	if ($pos !== false)
	{
		$mime = mimeForExt(substr($split[1], $pos + 1));
	}
	
	header("Content-Type: $mime");
	chdir("..");
	create_file($style, $split[1]);
?>