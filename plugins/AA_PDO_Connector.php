<?php
	require_once("plugins/AA_PDO_Connector/settings.php");
	
	class AA_PDO_Connector extends AnyAdminPlugin implements SettingsProvider
	{
		private $pdo;
		
		function onPluginsLoaded($anyadmin)
		{
			$plug = $anyadmin->getPlugin("AA_Config");
			if ($plug !== false)
			{
				$plug->registerSettingsProvider($this);
			}
		}
		
		function getSetting($name)
		{
			$sql = "SELECT value
					FROM settings
					WHERE name = :name";
			$sth = $this->pdo->prepare($sql);
			$sth->execute(array(":name" => $name));
			$res = $sth->fetch();
			
			if ($res === false)
			{
				return null;
			}
			else
			{
				return $res["value"];
			}
		}
		
		function setSetting($name, $value)
		{
			$sql = "UPDATE settings
					SET value = :value
					WHERE name = :name";
			$sth = $this->pdo->prepare($sql);
			if (!$sth->execute(array(":name" => $name, ":value" => $value)))
			{
				return "Update statement failed.";
			}
			elseif ($sth->rowCount() < 1)
			{
				$sql = "INSERT INTO settings (name, value)
						VALUES (:name, :value)";
				$sth = $this->pdo->prepare($sql);
				if ($sth->execute(array(":name" => $name, ":value" => $value)))
				{
					return true;
				}
				else
				{
					return "Insert statement failed.";
				}
			}
			else
			{
				return true;
			}
			
		}
		
		function getAllSettings()
		{
			$sql = "SELECT name, value
					FROM settings";
			$sth = $this->pdo->prepare($sql);
			$sth->execute();
			$res = $sth->fetchAll();
			
			$settings = array();
			foreach ($res as $k => $set)
			{
				$settings[$set["name"]] = $set["value"];
			}
			return $settings;
		}
		
		function onLoad($anyadmin)
		{
			$this->pdo = new PDO(PDO_Settings::$DSN, PDO_Settings::$USER, PDO_Settings::$PASSWORD);
		}
		
		function onCleanup($anyadmin)
		{
		
		}
	}
?>