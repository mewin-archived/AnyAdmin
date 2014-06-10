<?php
	class DbgLogin extends AnyAdminPlugin
	{
		function onTryLogin($anyadmin, $name, $pass)
		{
			if ($name == "test" && $pass == "test")
			{
				return array("user" => "Debug User", "group" => createDefaultGroup());
			}
			else
			{
				return false;
			}
		}
		
		function onInitUser($anyadmin, $session)
		{
			$session->getUser()->setPermission("aa.config.grant", true);
		}
	}
?>