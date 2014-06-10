<?php
	const CONFIG_PAGE = "config";
	
	class AA_Config extends AnyAdminPlugin implements SettingsUser
	{
		private $settingsUsers;
		private $settingsProviders;
		private $settingsLoaded;
		
		function __construct()
		{
			$this->settingsUsers = array();
			$this->settingsProviders = array();
			$this->settingsLoaded = false;
		}
		
		function onLoad($anyadmin)
		{
			global $_VARS;
			$this->settingsUsers[] = $this;
			$langFile = "plugins/AA_Config/{$anyadmin->getLang()}.txt";
			if (!file_exists($langFile))
			{
				$langFile = "plugins/AA_Config/en.txt";
			}
			loadLanguage($langFile);
		}
		
		function registerSettingsUser($suser)
		{
			if ($suser instanceof SettingsUser)
			{
				$this->settingsUsers[] = $suser;
			}
		}
		
		function registerSettingsProvider($sprov)
		{
			if ($sprov instanceof SettingsProvider)
			{
				$this->settingsProviders[] = $sprov;
			}
		}
		
		private function hasPerm($user, $perm)
		{
			return $user->hasPermission($perm) || $user->hasPermission("aa.config.grant");
		}
		
		private function toSetList($setGroup)
		{
			$sets = array();
			foreach ($setGroup->getSettings() as $k => $set)
			{
				if ($set instanceof Setting)
				{
					$sets[$set->getName()] = $set->getDefault();
				}
				else if ($set instanceof SettingsGroup)
				{
					$sets += $this->toSetList($set);
				}
			}
			return $sets;
		}
		
		function getAllSettings($forceReload = false)
		{
			global $_SETTINGS;
			if ($forceReload || !$this->settingsLoaded)
			{
				$this->settingsLoaded = true;
				foreach ($this->settingsProviders as $k => $provider)
				{
					//$_SETTINGS = array_merge($_SETTINGS, $provider->getAllSettings());
					$_SETTINGS += $provider->getAllSettings();
				}
				foreach ($this->settingsUsers as $k => $suser)
				{
					$_SETTINGS += $this->toSetList($suser->getSettings());
				}
			}
			return $_SETTINGS;
		}
		
		function getSetting($name, $default = null, $forceReload = false)
		{
			global $_SETTINGS;
			if (!$forceReload && isset($_SETTINGS[$name]))
			{
				return $_SETTINGS[$name];
			}
			else
			{
				foreach ($this->settingsProviders as $k => $provider)
				{
					$set = $provider->getSetting($name);
					if ($set != null)
					{
						$_SETTINGS[$name] = $set;
						return $set;
					}
				}
			}
			return $default;
		}
		
		function onLoadSection($anyadmin, $section)
		{
			global $_VARS, $_SETTINGS;
			$user = $anyadmin->getSession()->getUser();
			
			if ($section == "head")
			{
				$this->getAllSettings(true);
			}
			elseif ($section == "header" && $_VARS["PAGE"] == CONFIG_PAGE && isset($_GET["save"]))
			{
				if ($_VARS["PAGE"] == CONFIG_PAGE)
				{
					$_VARS["PAGE_TITLE"] = getTranslation("config");
					
					if (isset($_GET["save"]))
					{
						$settings = $this->getAllSettings();
						$msgs = array();
						foreach ($settings as $k => $setting)
						{
							foreach ($this->settingsProviders as $k2 => $provider)
							{
								if (isset($_POST[str_replace(".", "_", $k) . "_type"]))
								{
									switch ($_POST[str_replace(".", "_", $k) . "_type"])
									{
										case "bool":
											$setting = isset($_POST[str_replace(".", "_", $k)]);
											break;
										case "int":
											$setting = intval($_POST[str_replace(".", "_", $k)]);
											break;
										case "double":
											$setting = floatval($_POST[str_replace(".", "_", $k)]);
											break;
										default:
											$setting = $_POST[str_replace(".", "_", $k)];
									}
									$_SETTINGS[$k] = $setting;
								}
								$res = $provider->setSetting($k, $setting);
								if ($res !== true)
								{
									if (count($msgs) < 10)
									{
										$msgs[] = getTranslation("set_$k") . ": $res";
									}
									elseif (count($msgs) == 10)
									{
										$msgs[] = 1;
									}
									else
									{
										$msgs[9]++;
									}
								}
							}
						}
						if (count($msgs) > 0)
						{
							if (count($msgs) > 9)
							{
								$msgs[9] = getTranslation("config_save_failed_more", $msgs[9]);
							}
							$_VARS["MESSAGE"] =  getTranslation("config_save_failed") . '<br>
													' . implode('<br>', $msgs);
							return parse_file($anyadmin->getStyle(), "error.html");
						}
						else
						{
							return "";
						}
					}
				}
			}
			elseif ($section == "navi" && $this->hasPerm($user, "aa.config.show") && $this->getSetting("aa.config.button", true))
			{
				return '<a class="navi" href="' . $anyadmin->getRoot() . '?p=' . CONFIG_PAGE .'">' . getTranslation("config") . '</a>';
			}
			elseif ($section == "overview" && $_VARS["PAGE"] == CONFIG_PAGE && $this->hasPerm($user, "aa.config.show"))
			{
				$style = $anyadmin->getStyle();
				
				ob_start();
				echo "<h2>".getTranslation("config")."</h2>";
				echo '<form action="' . $anyadmin->getRoot() . '?p=' . CONFIG_PAGE . '&amp;save" method="post">';
				foreach ($this->settingsUsers as $k => $suser)
				{
					echo '<div class="overview">';
					$this->print_settings($suser->getSettings(), 3, $style);
					echo '</div>';
				}
				echo '<br><input type="submit" value="' . getTranslation("save_settings") . '">';
				echo '<input type="reset" value="' . getTranslation("reset_settings") . '">';
				$txt = ob_get_contents();
				ob_end_clean();
				return $txt;
			}
		}
		
		function print_settings($settings, $level, $style)
		{
			global $_VARS;
			echo "<h$level>" . getTranslation("set_grp_" . $settings->getName()) . "</h$level>";
			echo '<div class="group">';
			foreach ($settings->getSettings() as $k => $setting)
			{
				if ($setting instanceof SettingsGroup)
				{
					$this->print_settings($setting, min(6, $level+1), $style);
				}
				else
				{
					$_VARS["SETTING_ID"] = str_replace(".", "_", $setting->getName());
					$_VARS["SETTING_NAME"] = getTranslation("set_" . $setting->getName());
					$_VARS["SETTING_VALUE"] = $this->getSetting($setting->getName(), $setting->getDefault());
					switch ($setting->getType())
					{
						case Setting::$BOOL_SETTING:
							echo '<input type="hidden" name="' . str_replace(".", "_", $setting->getName()) . '_type" id="' . str_replace(".", "_", $setting->getName()) . '_type" value="bool">';
							create_file($style, "aa_config/setting_bool.html");
							break;
						case Setting::$STRING_SETTING:
							echo '<input type="hidden" name="' . str_replace(".", "_", $setting->getName()) . '_type" id="' . str_replace(".", "_", $setting->getName()) . '_type" value="string">';
							create_file($style, "aa_config/setting_string.html");
							break;
						case Setting::$INT_SETTING:
							echo '<input type="hidden" name="' . str_replace(".", "_", $setting->getName()) . '_type" id="' . str_replace(".", "_", $setting->getName()) . '_type" value="int">';
							create_file($style, "aa_config/setting_int.html");
							break;
						case Setting::$DOUBLE_SETTING:
							echo '<input type="hidden" name="' . str_replace(".", "_", $setting->getName()) . '_type" id="' . str_replace(".", "_", $setting->getName()) . '_type" value="double">';
							create_file($style, "aa_config/setting_double.html");
							break;
						case Setting::$HINT_SETTING:
							create_file($style, "aa_config/setting_hint.html");
							break;
						default: 
							echo getTranslation("unknown_setting_type");
					}
					echo "<br>";
				}
			}
			echo '</div>';
		}
		
		function getSettings()
		{
			return
				new SettingsGroup("main",
						array(new Setting("aa.config.button", Setting::$BOOL_SETTING, true)));
		}
		
		function validate($setting, $value)
		{
			switch ($setting)
			{
				case "aa.config.button":
					return true;
				default:
					return true;
			}
		}
	}
	
	class SettingsGroup
	{
		private $settings;
		private $name;
		
		function __construct($name, $settings)
		{
			$this->name = $name;
			$this->settings = $settings;
		}
		
		function getName()
		{
			return $this->name;
		}
		
		function getSettings()
		{
			return $this->settings;
		}
	}
	
	class Setting
	{
		static $BOOL_SETTING = 0;
		static $INT_SETTING = 1;
		static $DOUBLE_SETTING = 2;
		static $STRING_SETTING = 3;
		static $HINT_SETTING = 4;
		
		private $name;
		private $type;
		private $default;
		
		function __construct($name, $type, $default)
		{
			$this->name = $name;
			$this->type = $type;
			$this->default = $default;
		}
		
		function getName()
		{
			return $this->name;
		}
		
		function getType()
		{
			return $this->type;
		}
		
		function getDefault()
		{
			return $this->default;
		}
	}
	
	interface SettingsProvider
	{
		function getSetting($name);
		function setSetting($name, $value);
		function getAllSettings();
	}
	
	interface SettingsUser
	{
		function getSettings();
		function validate($setting, $value);
	}
?>