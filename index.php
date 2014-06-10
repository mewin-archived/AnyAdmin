<?php
	@require_once("lib/styler.php");
	@require_once("lib/anyadmin.php");
	@require_once("lib/plugin.php");
	@require_once("lib/session.php");
	@require_once("lib/language.php");

	error_reporting(E_ALL);
	
	function temp_func_content()
	{
		global $anyadmin;
		return $anyadmin->getContent();
	}

	function temp_func_user($args)
	{
		global $anyadmin;
		return $anyadmin->getUserInfo($args[0]);
	}

	function temp_func_section($args)
	{
		global $anyadmin;
		return $anyadmin->getSection($args[0]);
	}

	$anyadmin = new AnyAdmin("tron", "en");
	$_SETTINGS = array();
	$_VARS = array("PAGE_TITLE" => "No title set...");
	$anyadmin->fillVars();
	$anyadmin->loadPlugins("plugins");
	$anyadmin->processData();
	$anyadmin->loadPage();
	$anyadmin->cleanup();
?>