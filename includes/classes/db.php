<?php
//require_once('config.php');

class Db
{
	static private $db = null;
	static private $queryCount = 0;

	/**
	 *
	 * @return PDO|false
	 */
	static function getDb()
	{
		global $CONFIG;
		if(self::$db === null)
		{
			$db_info = $CONFIG['current_mysql_credentials'];
			$driverOptions = array();
			if($db_info['mysql_use_persistent_connection'])
			{
				$driverOptions[PDO::ATTR_PERSISTENT] = true;
			}
			$dsn = "mysql:host={$db_info['mysql_host']};port={$db_info['mysql_port']};dbname={$db_info['mysql_database']}";
			try
			{
				self::$db = new PDO($dsn,$db_info['mysql_user'], $db_info['mysql_pass'],$driverOptions);
				self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // turn on exception error handling
				self::$db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
			}
			catch( PDOException $e)
			{
				trigger_error('Connection to MySQL database failed. Error: ' . $e->getMessage());
				self::$db = null;
				return false;
			}
		}
		return self::$db;
	}
	
	static function getEmulatedQuery($query, $params)
	{
		global $CONFIG;
		$matches = array();
		$errors = '';
		$match_num = preg_match_all('/:[_a-zA-Z]+/', $query, $matches);

		foreach($matches[0] as $match)
		{
			$placeholder = $match;
			$placeholderName = substr($placeholder, 1);
			if(!isset($params[$placeholderName]))
			{
				$placeholderName = $placeholder;
			}
			if(isset($params[$placeholderName]))
			{
				$data = addslashes($params[$placeholderName]);
				unset($params[$placeholderName]);
				$query = str_replace($placeholder, "'$data'" , $query);
			}
			else
			{
				$errors .= "Error, $placeholder has no data.\n";
			}
		}
		$remaining = count($params);
		if($remaining!=0)
		{
			$errors .= " Error, there are $remaining unused paramaters.\n";
		}
		return $query . $errors . $CONFIG['current_mysql_credentials']['mysql_database'];
	}
	/**
	 * Queries the Database using the query and placeholder array provided.
	 * Returns the PDOStatement object associated with the query
	 * @param string $query
	 * @param array $bindArray
	 * @return PDOStatement|false returns a PDOStatement on success, false on error
	 */
	static function query($query,$bindArray=array())
	{
		//if($query == "SELECT * FROM `tbAccounts` WHERE `sUsername` = ''"){
		//	$myFile = "/var/www/SQLQueries.txt";
		//	$fh = fopen($myFile, 'a') or die("can't open file");
		//	$stringData = $query." - ".$_SERVER['SCRIPT_NAME']."\n";
		//	fwrite($fh, $stringData);
		//	fclose($fh);
		//}

		self::$queryCount++;
		try
		{
			$stmt = self::getDb()->prepare($query);
			$stmt->execute($bindArray);
			
			return $stmt;
		}
		catch(PDOException $e)
		{
			trigger_error('Error on database query: ' . $e->getMessage());
			return false;
		}
	}

	static function getQueryCount()
	{
		return self::$queryCount;
	}
	
	static function lastInsertId()
	{
		return self::getDb()->lastInsertId();
	}
	
	static function disconnect()
	{
		self::$db=null;
	}
	static function closeCursor()
	{
		$stmt->closeCursor();
	}
}
?>
