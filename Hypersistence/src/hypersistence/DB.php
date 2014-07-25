<?php

class DB extends PDO{
	/**
	 * @var DB
	 */
	private static $conn = null;
	
	public function __construct($dsn, $username, $passwd, $options) {
		parent::__construct($dsn, $username, $passwd, $options);
	}

	/**
	 * 
	 * @return DB
	 */
	public static function &getDBConnection(){
		if(!is_null(self::$conn) && is_a(self::$conn, 'DB')){
			return self::$conn;
		}else{
			$conf = simplexml_load_file(__DIR__.'/dbconf.xml');
			self::$conn = new DB("$conf->dbms:host=$conf->host;dbname=$conf->database;charset=$conf->charset", $conf->username, $conf->password, array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_STATEMENT_CLASS => array('ResultSet'), PDO::ATTR_PERSISTENT => false));
			if(!self::$conn->inTransaction())
				self::$conn->beginTransaction();
			return self::$conn;
		}
	}
	
	public static function destroy(){
		self::$conn = null;
	}
}

class ResultSet extends PDOStatement{
	
	
	public function execute(array $input_parameters = null){
		if(!is_null($input_parameters)){
			foreach ($input_parameters as $key => $param){
				if(is_array($param)){
					if(isset($param[1]) && $param[1] == PDO::PARAM_INT){
						$this->bindValue($key, $param[0], PDO::PARAM_INT);
					}else{
						$this->bindValue($key, $param[0]);
					}
				}else{
					$this->bindValue($key, $param);
				}
			}
		}
		return parent::execute();
	}
}
?>