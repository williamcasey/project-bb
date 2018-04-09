<!DOCTYPE html>
<html>
<head>
	<title></title>

	<?php

include './sign-out-class.php';

$sign = new sign_out("localhost", "root", "", "lsmsa");



?>
<style>
	th, td {
		width: 160px;
		text-align: center;
	}
</style>

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
$data = $sign->get_requests();
foreach ($data as $row) {
	$student_info = $sign->student_info($row['student_id']);
?>

	<tr>
		<td><?php echo $row['student_id']; ?></td>
		<td><?php echo $student_info['last']; ?></td>
		<td><?php echo $student_info['first']; ?></td>
		<td><?php echo $row['sign_out_time']; ?></td>
		<td><?php echo $row['location']; ?></td>
		<td><?php echo $row['companions']; ?></td>
	</tr>

<?php
}


?>

</table>
<?php

echo $sign->timing(TRUE);

?>

</body>
</html>


