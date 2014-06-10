<?php
	class TextLogo extends AnyAdminPlugin
	{
		function onLoadSection($anyadmin, $section)
		{
			if ($section == "head")
			{
				return '<script type="text/javascript" src="plugins/textLogo/textLogo.js"></script>
						<link rel="stylesheet" type="text/css" href="plugins/textLogo/textLogo.css">';
			}
			else if ($section == "header")
			{
				return '<a href="' . $anyadmin->getRoot() . '" onmouseover="tl_over();" onmouseout="tl_out();" class="tl_a"><span id="tl_1" class="tl_1">Any</span><span id="tl_2" class="tl_2">Admin</span></a>';
			}
			else
			{
				return null;
			}
		}
	}
?>