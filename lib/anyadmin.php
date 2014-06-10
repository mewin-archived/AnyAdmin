<?php
	class AnyAdmin
	{
		private $plugins;
		private $session;
		private $style;
		private $site;
		private $redirect;
		private $lang;

		function __construct($style, $lang)
		{
			$this->plugins = array();
			$this->session = new Session();
			$this->style = $style;
			$this->site = "login";
			$this->lang = $lang;
			$this->redirect = null;
		}

		function loadPlugins($dir)
		{
			$dp = opendir($dir);
			while (($file = readdir($dp)) !== false)
			{
				if ($file[0] == "." || is_dir("$dir/$file")) continue;
				$pos = strrpos($file, ".");
				if ($pos === false) continue;
				$ext = substr($file, $pos + 1);
				if (strtolower($ext) != "php") continue;
				@include("$dir/$file");
				$plug = substr($file, 0, $pos);
				if (class_exists($plug))
				{
					$plugin = new $plug();
					$this->plugins[$plug] = $plugin;
					$plugin->onLoad($this);
				}
			}
			foreach ($this->plugins as $k => $plugin)
			{
				$plugin->onPluginsLoaded($this);
			}
		}

		function processData()
		{
			if ($this->session->isLoggedIn())
			{
				if (isset($_GET["mode"]))
				{
					switch($_GET["mode"])
					{
						case "logout":
							$this->session->logout();
							$this->redirect = basename($_SERVER["PHP_SELF"]);
							break;
					}
				}
				else
				{
					$this->site = "overview";
					foreach ($this->plugins as $k => $plugin)
					{
						$plugin->onInitUser($this, $this->session);
					}
				}
			}
			elseif (isset($_POST["aaLogin"]) && isset($_POST["aaPassword"]))
			{
				foreach ($this->plugins as $k => $plugin)
				{
					if (($data = $plugin->onTryLogin($this, $_POST["aaLogin"], $_POST["aaPassword"])) !== false)
					{
						$this->site = "overview";
						$this->session->setData($data);
						foreach ($this->plugins as $k2=> $plugin2)
						{
							$plugin2->onLogin($this, $this->session, $plugin);
						}
						foreach ($this->plugins as $k2=> $plugin2)
						{
							$plugin2->onInitUser($this, $this->session);
						}
						return;
					}
				}
				$this->site = "loginFailed";
			}
		}

		function loadPage()
		{
			loadLanguage("languages/{$this->lang}.txt");
			if ($this->redirect !== null)
			{
				header("Location: " . urlencode($this->redirect), 302);
			}
			else
			{
				create_page($this->style);
			}
		}
		
		function cleanup()
		{
			foreach ($this->plugins as $k => $plugin)
			{
				$plugin->onCleanup($this);
			}
		}

		function fillVars()
		{
			global $_VARS;
			$_VARS["LOGGED_IN"] = $this->session->isLoggedIn();
			$_VARS["PAGE"] = "overview";
			if (!empty($_GET["p"]))
			{
				$_VARS["PAGE"] = $_GET["p"];
			}
			elseif (!empty($_SERVER["PATH_INFO"]) && $_SERVER["PATH_INFO"] != "/")
			{
				$_VARS["PAGE"] = substr($_SERVER["PATH_INFO"], 1);
			}
			
			foreach ($this->plugins as $k => $plugin)
			{
				$plugin->onLoadVars($this);
			}
		}

		function getContent()
		{
			return parse_file($this->style, "{$this->site}.html");
		}

		function getSection($section)
		{
			$text = "";
			foreach ($this->plugins as $k => $plugin)
			{
				$c = $plugin->onLoadSection($this, $section);
				if ($c !== null)
				{
					$text .= $c . "\n";
				}
			}
			return $text;
		}

		function callPluginFunction($func, $args)
		{
			foreach ($this->plugins as $k => $plugin)
			{
				$funcName = "$k::$func";
				
				if (function_exists($funcName))
				{
					$funcName($args);
				}
			}
		}

		function getSession()
		{
			return $this->session;
		}

		// Setters and getters :)
		function getUserInfo($info)
		{
			return $this->session->data[$info];
		}

		function setUserInfo($info, $value)
		{
			$this->session->data[$info] = $value;
		}

		function getLang()
		{
			return $this->lang;
		}

		function setLang($lang)
		{
			$this->lang = $lang;
		}

		function getStyle()
		{
			return $this->style;
		}

		function setStyle($style)
		{
			$this->style = $style;
		}

		function getPlugin($name)
		{
			if (isset($this->plugins[$name]))
			{
				return $this->plugins[$name];
			}
			else
			{
				return false;
			}
		}

		function getRoot()
		{
			return basename($_SERVER["PHP_SELF"]);
		}
	}
?>