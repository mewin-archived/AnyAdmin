<?php
	class Navi_Main extends AnyAdminPlugin
	{
		function onLoadSection($anyadmin, $section)
		{
			switch ($section)
			{
				case "navi_early":
					return '<a class="navi" href="' . $anyadmin->getRoot() . '">' . getTranslation("home") . '</a>';
				case "navi_late":
					if ($anyadmin->getSession()->isLoggedIn())
					{
						return '<a class="navi" href="' . $anyadmin->getRoot() . '?mode=logout">' . getTranslation("logout") . '</a>';
					}
				default:
					return null;
			}
		}
	}
?>