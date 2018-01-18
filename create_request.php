<?php

include './sign-out-class.php';

$sign = new sign_out("localhost", "root", "", "lsmsa");

$student_id = 00000004;
$location = "Texas";
$companions = "Sam";
$return_time = "2018-01-17 10:20:37";

$sign->create_request($student_id, $location, $companions, $return_time);

?>

