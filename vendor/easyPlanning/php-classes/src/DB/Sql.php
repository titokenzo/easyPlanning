<?php 

namespace easyPlanning\DB;

use easyPlanning\SysConfig;

class Sql extends SysConfig{

	private $conn;

	public function __construct()
	{

		$this->conn = new \PDO("mysql:dbname=". self::BD_BANCO . ";host=" . self::BD_HOST . ";charset=utf8", 
			self::BD_USER,
		    self::BD_PASS,
		    array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
		);
	}

	private function setParams($statement, $parameters = array())
	{

		foreach ($parameters as $key => $value) {
			
			$this->bindParam($statement, $key, $value);

		}

	}

	private function bindParam($statement, $key, $value)
	{

		$statement->bindParam($key, $value);

	}

	public function query($rawQuery, $params = array())
	{

		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);
		
		if (!$stmt->execute()){
		    throw new \Exception('Erro nos dados. #' . $stmt->errorInfo()[0] . ': ' . $stmt->errorInfo()[2]);
		}

	}

	public function select($rawQuery, $params = array())
	{

		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

		if (!$stmt->execute()){
		    throw new \Exception('Erro nos dados. #' . $stmt->errorInfo()[0] . ': ' . $stmt->errorInfo()[2]);
		}
		
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

}

 ?>