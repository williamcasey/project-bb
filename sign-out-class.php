<?php


class sign_out {
	
		//create variable for PDO MySQL connection
	var $mysql = NULL;
	var $last_request  = 0;
	
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

	//function to create placeholder values in sql queries for prepared statements
	function placeholders($text, $count=0){
	    $result = array();
	    if($count > 0){
	        for($x=0; $x<$count; $x++){
	            $result[] = $text;
	        }
	    }
	    return implode(",", $result);
	}

	function student_info($id) {
		$query = "SELECT `student_id`, `first_name`, `last_name`, `class`, `image` FROM `students` WHERE `student_id` = ?";
		$stmt = $this->mysql->prepare($query);
		$out = NULL;
		if ($stmt->execute(array($id))) {
			while($row = $stmt->fetch()) {
		  		$out = array('id' => $row['student_id'], 'first' => $row['first_name'], 'last' => $row['last_name'], 'class' => $row['class'], 'image' => $row['image']);
		  	}
		} 
		return $out;
	}

	function update_last_id($val = 0) {

		$this->mysql->beginTransaction();


		$sql = "UPDATE `last_request` SET `request_id` = ? WHERE `id` = 1";
		//create prepared insert statement from the query
		$stmt = $this->mysql->prepare($sql);
		$insert_values = array($val);
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

	function last_id($id = 1) {
		$query = "SELECT `request_id` FROM `last_request` WHERE `id` = ?";
		$stmt = $this->mysql->prepare($query);
		$data = array($id);
		if ($stmt->execute($data)) {
			$result = $stmt->fetch();
			if(isset($result['request_id'])) {
				return $result['request_id'];
			} else {
				return 0;
			}
		} 
	}

	function get_requests($after_id = NULL) {
		$after_id = $this->last_id();
		//echo $after_id;
		//echo "start:".$this->last_request;
		$query = "SELECT `request_id`, `student_id`, `sign_out_time`, `planned_return_time`, `location`, `companions`, `check_in`, `check_in_time`, `sign_in_time`, `active`, `approved`, `approved_by` FROM `requests` WHERE `active` = ? AND `request_id` > ? ORDER BY `request_id` DESC";
		$stmt = $this->mysql->prepare($query);
		$data = array(1, $after_id);
		if ($stmt->execute($data)) {
			while($row = $stmt->fetch()) {
		  		$out[] = array('request_id' => $row['request_id'], 'student_id' => $row['student_id'], 'sign_out_time' => $row['sign_out_time'], 'planned_return_time' => $row['planned_return_time'], 'location' => $row['location'], 'companions' => $row['companions'], 'approved' => $row['approved'], 'approved_by' => $row['approved_by']);
		  	}
		}
		$this->update_last_id($out[0]['request_id']);
		//echo "end:".$this->last_request;
		//var_dump($out);
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

	function create_request($student_id, $location, $companions, $return_time) {
		//begin mysql transaction
		$this->mysql->beginTransaction();
		$sign_out_time = date("Y-m-d H:i:s", time());
		//$return = date("Y-m-d")." ".$return_time.":00";

		$insert_values = array($student_id, $sign_out_time, $return_time, $location, $companions);

		$values_fields = array("student_id", "sign_out_time", "planned_return_time", "location", "companions");
		$question_marks[] = '('.$this->placeholders('?', sizeof($insert_values)).')';

		//var_dump($insert_values);
		//create query
		$sql = "INSERT INTO `requests` (`".implode("`,`", $values_fields)."`) VALUES ".implode(',', $question_marks);
		//echo $sql;
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



}











?>