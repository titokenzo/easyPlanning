<?php 

namespace easyPlanning\DB;

use easyPlanning\SysConfig;

class Sql extends SysConfig{

	private $conn;

	public function __construct()
	{

		$this->conn = new \PDO(
			"mysql:dbname=". self::BD_BANCO . ";host=" . self::BD_HOST, 
			self::BD_USER,
			self::BD_PASS
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

		$stmt->execute();

	}

	public function select($rawQuery, $params = array()):array
	{

		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

}

 ?>