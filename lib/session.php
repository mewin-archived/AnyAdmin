<?php
	session_start();
	class Session
	{
		private $anonymous_group, $user;
		public $data;

		function __construct()
		{
			$this->anonymous_group = new Group("Anonymous");
			$this->data = isset($_SESSION["ses_data"]) ? $_SESSION["ses_data"] : $this->buildData();
		}

		function isLoggedIn()
		{
			return isset($this->data["group"]) && $this->data["group"] != $this->anonymous_group;
		}

		function setData($data)
		{
			$this->data = $data;
			$_SESSION["ses_data"] = $data;
		}

		function logout()
		{
			session_destroy();
		}

		function getAnonymousGroup()
		{
			return $this->anonymous_group;
		}
		
		function getUser()
		{
			if (!isset($this->user))
			{
				$this->user = new User(createDefaultGroup());
			}
			return $this->user;
		}

		private function buildData()
		{
			return array("user" => "Anonymous", 
						 "group" => $this->anonymous_group);
		}
	}

	interface PermissionHolder
	{
		function hasPermission($perm);
		function setPermission($perm, $val);
		function unsetPermission($perm);
	}

	class User implements PermissionHolder
	{
		private $permissions;
		private $group;

		function __construct($group)
		{
			$this->permissions = array();
			$this->group = $group;
		}

		function hasPermission($perm)
		{
			if (isset($this->permissions[$perm]))
			{
				return $this->permissions[$perm];
			}
			elseif (isset($this->group))
			{
				return $this->group->hasPermission($perm);
			}
			else
			{
				return false;
			}
		}

		function setPermission($perm, $val)
		{
			$this->permissions[$perm] = $val;
		}

		function unsetPermission($perm)
		{
			unset($this->permissions[$perm]);
		}

		function getGroup()
		{
			return $this->group;
		}

		function setGroup($group)
		{
			$this->group = $group;
		}
	}

	class Group implements PermissionHolder
	{
		private $permissions;
		private $name;

		function __construct($name)
		{
			$this->permissions = array();
			$this->name = $name;
		}

		function hasPermission($perm)
		{
			if (isset($this->permissions[$perm]))
			{
				return $this->permissions[$perm];
			}
			else
			{
				return false;
			}
		}

		function setPermission($perm, $val)
		{
			$this->permissions[$perm] = $val;
		}

		function unsetPermission($perm)
		{
			unset($this->permissions[$perm]);
		}

		function setName($name)
		{
			$this->name = $name;
		}

		function getName()
		{
			return $this->name;
		}
	}

	function createDefaultGroup()
	{
		return new Group("Default");
	}
?>