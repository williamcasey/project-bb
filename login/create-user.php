<?php

include './login-class.php';


if(isset($_POST['id']) && isset($_POST['user']) && isset($_POST['pass']) && isset($_POST['first']) && isset($_POST['last']) && isset($_POST['class'])) {
	$sign = new login("localhost", "root", "", "lsmsa");

	$id = (int) $_POST['id'];
	$user = $_POST['user'];
	$pass = $_POST['pass'];
	$first = $_POST['first'];
	$last = $_POST['last'];
	$class = (int) $_POST['class'];

	echo $id."<br>";
	echo $user."<br>";
	echo $first."<br>";
	echo $last."<br>";
	echo $class."<br><br>";

	if($sign->create_user($id, $user, $pass, $first, $last, $class)) {
		echo "user was added<br>";
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>create user</title>



</head>
<body>
	<form id='request' class='main-form' action='' method='POST'>
		<label> ID: <input type="number" name="id" id="id" placeholder="id" value="<?php echo rand(5, 9999); ?>"></label><br>
		<label> User: <input type="text" name="user" id="user" placeholder="id"></label><br>
		<label> Password: <input type="password" name="pass" id="pass" placeholder="id"></label><br>
		<label> First Name: <input type="text" name="first" id="first" placeholder="id"></label><br>
		<label> Last Name: <input type="text" name="last" id="last" placeholder="id"></label><br>
		<select name="class" id="class">
			<option value="19">2019</option> 
  			<option value="20" selected>2020</option>
  			<option value="21">2021</option>
  			<option value="22">2022</option>
		</select>
		<input type="submit" name="sub" value="create user">
	</form>

</body>
</html>