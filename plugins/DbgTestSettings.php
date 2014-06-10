<?php
	class DbgTestSettings extends AnyAdminPlugin implements SettingsUser
	{
		function onPluginsLoaded($anyadmin)
		{
			$plug = $anyadmin->getPlugin("AA_Config");
			if ($plug !== false)
			{
				$plug->registerSettingsUser($this);
			}
		}
		
		function getSettings()
		{
			return
				new SettingsGroup("test",
						array(new Setting("aa.config.string", Setting::$STRING_SETTING, "test-string"), 
								new SettingsGroup("TestSub1", array(new Setting("aa.config.int", Setting::$INT_SETTING, 54), 
										new Setting("aa.config.double", Setting::$DOUBLE_SETTING, 1.0)))));
		}
		
		function validate($setting, $value)
		{
			return true;
		}
	}
?>