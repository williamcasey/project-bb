<?php

include './sign-out-class.php';
$sign = new sign_out("localhost", "root", "", "lsmsa");

$info = array("student" => intval($_POST["student"]), "location" => $_POST["location"], "companions" => $_POST["companions"], "return_time" => $_POST["return_time"]);

if($sign->sign_out_request($info['student'], $info['location'], $info['return_time'], $info['companions']) == TRUE) {
	echo "sign out request was recorded";
} else {
	echo "error with sign out request";
}


?>