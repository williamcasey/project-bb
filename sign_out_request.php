<?php

//include class and connect to database 
include './sign-out-class.php';
$sign = new sign_out("localhost", "root", "", "lsmsa");

if(isset($_POST["student"]) && isset($_POST["location"]) && isset($_POST["companions"]) && isset($_POST["return_time"])) {
	$info = array("student" => intval($_POST["student"]), "location" => $_POST["location"], "companions" => $_POST["companions"], "return_time" => $_POST["return_time"]);
	if($sign->sign_out_request($info['student'], $info['location'], $info['return_time'], $info['companions']) == TRUE) {
		echo "Success";
	} else {
		echo "An error occured: request could not be added to databasedatabase";
	}
} else {
	echo "An error occured: insufficent data provided";
}

?>