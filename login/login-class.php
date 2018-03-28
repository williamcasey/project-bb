<?php

/**
* login class
*/
class login {

	var $login_error = NULL;
	var $student_name = NULL;

	//on initialization connect to MySQL database using PDO
	function __construct($host, $user, $pass, $db, $charset="utf8mb4") {

		//define UTC timezone for timestamps
		date_default_timezone_set('UTC');

		if(!$this->mysql = new PDO('mysql:host='.$host.';dbname='.$db.';charset='.$charset, $user, $pass)) {
			echo "Could not to the connect to the MySQL database";
			return FALSE;
		}
		//set profiling on the mysql db to time queries
		$this->mysql->query('SET profiling = 1');
		return TRUE;
	}

	function authenticate($username, $password) {
		$query = "SELECT * FROM `students` WHERE `user` = ?";
		$stmt = $this->mysql->prepare($query);
		$data = array($username);
		if ($stmt->execute($data)) {
			$rows = $stmt->fetch();
			//var_dump($rows);
			if($rows != FALSE) {
				if(password_verify($password, $rows['password'])) {
					$this->student_name = $rows['first_name'];
					return TRUE;
				} else {
					$this->login_error = "incorrect password";
					return FALSE;
				}
			} else {
				$this->login_error = "username not found";
				return FALSE;
			}
		} else {
			$this->login_error = "login request couldn't be completed";
			return FALSE;
		}
	}
}


?>