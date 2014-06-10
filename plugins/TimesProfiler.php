<?php
	global $start;
	$start = microtime(true);
	
	class TimesProfiler extends AnyAdminPlugin
	{
		function onLoadSection($anyadmin, $section)
		{
			global $start;
			if ($section == "footer")
			{
				$time = round((microtime(true) - $start) * 1000, 4);
				return "Page loaded in $time ms.<br>";
			}
			else
			{
				return "";
			}
		}
	}
?>