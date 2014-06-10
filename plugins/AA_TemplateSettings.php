<?php
	class AA_TemplateSettings extends AnyAdminPlugin implements SettingsUser
	{
		private $anyadmin;
		
		function onLoad($anyadmin)
		{
			global $_VARS;
			$this->anyadmin = $anyadmin;
			$langFile = "plugins/AA_TemplateSettings/{$anyadmin->getLang()}.txt";
			if (!file_exists($langFile))
			{
				$langFile = "plugins/AA_TemplateSettings/en.txt";
			}
			loadLanguage($langFile);
		}
		
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
			$settingsFile = "templates/{$this->anyadmin->getStyle()}/settings.txt";
			if (file_exists($settingsFile) && is_file($settingsFile) && is_readable($settingsFile))
			{
				$lines = file($settingsFile);
				$settings = array();
				foreach ($lines as $k => $line)
				{
					$line = trim($line);
					if (strlen($line) < 1 || $line[0] == "#")
					{
						continue;
					}
					
					$pos = strpos($line, ":");
					$name = trim(substr($line, 0, $pos));
					$value = trim(substr($line, $pos + 1));
					$settings[] = new Setting($name, Setting::$STRING_SETTING, $value);
					addTranslation("set_$name", $name);
				}
				return new SettingsGroup("template", $settings);
			}
			else
			{
				return new SettingsGroup("template", array(new Setting("aa.template_settings.no_settings", Setting::$HINT_SETTING, null)));
			}
		}
		
		function validate($setting, $value)
		{
			return true;
		}
	}
?>