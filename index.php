<!DOCTYPE html>
<html>
<head>
	<title></title>

	<?php

include './sign-out-class.php';

$sign = new sign_out("localhost", "root", "", "lsmsa");


$sign->is_active(10);

?>

</head>
<body>

<table>
	<tr>
		<th>ID</th>
		<th>Last Name</th>
		<th>First Name</th>
		<th>Sign Out Time</th>
		<th>Destination</th>
		<th>Companions</th>
	</tr>
<?php
$data = $sign->get_requests('2017-08-21 20:11:37');
foreach ($data as $row) {
	echo "<tr>";
	foreach ($row as $val) {
		echo "<td>".$val."</td>";
	}
	

	echo "</tr>";
}


?>

</table>
<?php

echo $sign->timing(TRUE);

?>

</body>
</html>


