<?php
	class AnyAdminPlugin
	{
		/**
		* Executed when this plugin is loaded
		* @param $anyadmin the current instance of anyadmin
		*/
		function onLoad($anyadmin)
		{

		}
		
		/**
		* Executed when all plugins are loaded
		* @param $anyadmin the current instance of anyadmin
		*/
		function onPluginsLoaded($anyadmin)
		{
			
		}

		/**
		* Executed whenever a user tries to login.
		* @param $anyadmin the current instance of anyadmin
		* @param $name the username the user tries to login with
		* @param $pass the password the user uses to authenticate
		* @return false if you do not want to let the user log in with the given credentials, a struct containing the users data otherwise
		* @see Session::buildData()
		*/
		function onTryLogin($anyadmin, $name, $pass)
		{
			return false;
		}

		/**
		* Executed when anyadmin registers the global variables.
		* Use this to write your own variables to $_VARS
		* @param $anyadmin the current instance of anyadmin
		* @see doc/variables.txt
		*/
		function onLoadVars($anyadmin)
		{

		}

		/**
		* Executed when a specific section is loaded by the template
		* @param $anyadmin the current instance of anyadmin
		* @param $section the name of the section to load
		* @return the source to load or "" if there is nothing to load
		*/
		function onLoadSection($anyadmin, $section)
		{
			return null;
		}

		/**
		* Executed when a user has been logged in, because his credentials have been accepted by a plugin
		* @param $anyadmin the current instance of anyadmin
		* @param $session the session created by logging in
		* @param $plugin the plugin that accepted the credentials
		*/
		function onLogin($anyadmin, $session, $plugin)
		{

		}
		
		/**
		* Executed before loading the site if a user is logged in or just logged in
		* @param $anyadmin the current instance of anyadmin
		* @param $session the session of the user
		*/
		function onInitUser($anyadmin, $session)
		{
			
		}
		
		/**
		* Executed after displaying the page so any plugin can clean up resources it does not need anymore
		* @param anyadmin the current instance of anyadmin
		*/
		function onCleanup($anyadmin)
		{
			
		}
	}
?>