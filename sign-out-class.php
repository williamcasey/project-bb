<?php


class sign_out {
	
		//create variable for PDO MySQL connection
	var $mysql = NULL;
	
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

	//function for timing MySQL queries I wrote for debugging
	function timing($total = FALSE) {
		$show = $this->mysql->query('show profiles');
		$result = $show->fetchAll(PDO::FETCH_ASSOC);
		if(!$total) {
			$last = array_values(array_slice($result, -1))[0];
			return $last['Duration'];
		} else {
			$overall = 0;
			foreach ($result as $q) {
				$overall = $overall + $q['Duration'];
			}
			return "<br>".count($result)." queries executed in ".$overall." seconds.</br>";
		}
	}

	function student_info($id) {
		$query = "SELECT `student_id`, `first_name`, `last_name`, `class`, `image` FROM `students` WHERE `student_id` = ?";
		$stmt = $this->mysql->prepare($query);
		if ($stmt->execute(array($id))) {
			while($row = $stmt->fetch()) {
		  		$out[] = array('id' => $row['student_id'], 'first' => $row['first_name'], 'last' => $row['last_name'], 'class' => $row['class'], 'image' => $row['image']);
		  	}
		}
		return $out;
	}

	function get_requests($time = '1970-01-01 00:00:00') {
		$query = "SELECT `student_id`, `sign_out_time`, `planned_return_time`, `location`, `companions`, `check_in`, `check_in_time`, `sign_in_time`, `active`, `approved`, `approved_by` FROM `requests` WHERE `active` = ? AND `sign_out_time` > ?";
		$stmt = $this->mysql->prepare($query);
		$data = array(1, $time);
		if ($stmt->execute($data)) {
			while($row = $stmt->fetch()) {
		  		$out[] = array('student_id' => $row['student_id'], 'sign_out_time' => $row['sign_out_time'], 'planned_return_time' => $row['planned_return_time'], 'location' => $row['location'], 'companions' => $row['companions'], 'approved' => $row['approved'], 'approved_by' => $row['approved_by']);
		  	}
		}
		return $out;
	}

	function sign_out_request($student, $location, $return_time, $companions) {
		//begin mysql transaction
		$this->mysql->beginTransaction();

		//get current UTC time
		$sign_out_time = date("Y-m-d H:i:s");
		$insert_values = array($student, $sign_out_time, $return_time, $location, $companions);
		//create query
		$sql = "INSERT INTO requests(`student_id`, `sign_out_time`, `planned_return_time`, `location`, `companions`) VALUES (?, ?, ?, ?, ?)";
		//create prepared insert statement from the query
		$stmt = $this->mysql->prepare($sql);
		//execute prepared statement and output any errors
		try {
		    $stmt->execute($insert_values);
		} catch (PDOException $e) {
	    	throw $e;
	   	} 
		//end transaction
		if($this->mysql->commit()) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function is_active($request) {
		$query = "SELECT `active` FROM `requests` WHERE `request_id` = ?";
		$stmt = $this->mysql->prepare($query);
		$data = array($request);
		if ($stmt->execute($data)) {
			$result = $stmt->fetch();
			if($result['active'] == 1) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	function deactivate($request) {
		$this->mysql->beginTransaction();
		$values = array($request);
		$query = "UPDATE `requests` SET `active` = 0 WHERE `request_id` = ?";
		//create prepared insert statement from the query
		$stmt = $this->mysql->prepare($query);
		//execute prepared statement and output any errors
		try {
			$stmt->execute($values);
		} catch (PDOException $e) {
		    throw $e;
		} 
		//end transaction
		if($this->mysql->commit()) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function sla_approve($request) {
		if(is_int($request) && $this->is_active($request)) {
			//begin mysql transaction
			$this->mysql->beginTransaction();
			$values = array($request);
			$query = "UPDATE `requests` SET `approved` = 1 WHERE `request_id` = ?";
			//create prepared insert statement from the query
			$stmt = $this->mysql->prepare($query);
			//execute prepared statement and output any errors
			try {
			    $stmt->execute($values);
			} catch (PDOException $e) {
		    	throw $e;
		   	} 
			//end transaction
			if($this->mysql->commit()) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	function sla_decline($request) {
		if(is_int($request) && $this->is_active($request)) {
			//begin mysql transaction
			$this->mysql->beginTransaction();
			$values = array($request);
			$query = "UPDATE `requests` SET `approved` = 0 WHERE `request_id` = ?";
			$this->deactivate($request);
			//create prepared insert statement from the query
			$stmt = $this->mysql->prepare($query);
			//execute prepared statement and output any errors
			try {
			    $stmt->execute($values);
			} catch (PDOException $e) {
		    	throw $e;
		   	} 
			//end transaction
			if($this->mysql->commit()) {
				return TRUE;
			} else {
				return FALSE;
			}

		} else {
			return FALSE;
		}
	}


}











?>