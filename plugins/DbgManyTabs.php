<?php
	class DbgManyTabs extends AnyAdminPlugin
	{
		function onLoadSection($anyadmin, $section)
		{
			if ($section == "navi")
			{
				$txt = "";
				for ($i = 0; $i < -1; $i++)
				{
					$txt .= '<a class="navi" href="#">Test' . $i . '</a>';
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