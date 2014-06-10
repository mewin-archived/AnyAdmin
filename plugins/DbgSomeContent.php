<?php
	class DbgSomeContent extends AnyAdminPlugin
	{
		function onLoadSection($anyadmin, $section)
		{
			global $_VARS;
			
			if ($section == "overview" && $_VARS["PAGE"] == "overview")
			{
				$txt = "";
				for ($i = 0; $i < 50; $i++)
				{
					$txt .= "$i<br>\n";
				}
				return $txt;
			}
			else
			{
				return null;
			}
		}
	}
?>