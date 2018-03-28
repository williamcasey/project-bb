<?php

include './sign-out-class.php';

session_start();

if(!isset($_SESSION['name'])) {
	echo "not logged in, please login <a href='./login/index.php'>here</a>.";
	exit;
}


if(isset($_POST['location']) && isset($_POST['companions']) && isset($_POST['return_time'])) {
	$sign = new sign_out("localhost", "root", "", "lsmsa");

	$student_id = rand(5, 9999);
	$location = $_POST['location'];
	$companions = $_POST['companions'];
	$return_time = $_POST['return_time'];
	$return = date("Y-m-d")." ".$return_time.":00";

	echo $location."<br>";
	echo $companions."<br>";
	echo $return."<br><br>";

	if($sign->create_request($student_id, $location, $companions, $return)) {
		echo "request was sent to SLA<br>";
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Create request</title>



</head>
<body>
	<span>Hello, <?php echo $_SESSION['name']; ?></span><br>
	<form id='request' class='main-form' action='' method='POST'>
		<label> Location: <input type="text" name="location" id="location" placeholder="where are you going?"></label><br>
		<label> Companions: <input type="text" name="companions" id="companions" placeholder="who are you going with?"></label><br>
		<label> Return time: <input name="return_time" id="return_time" type="time" min="7:00" max="9:00"></label>
		<input type="submit" name="sub" value="Send to SLA">
	</form>

</body>
</html>
