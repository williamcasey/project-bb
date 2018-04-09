<?php /*
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

include './sign-out-class.php';

$sign = new sign_out("localhost", "root", "", "lsmsa");

$last = 0;

$requests = $sign->get_requests();

echo "data: [\n";

foreach($requests as $val) {
	echo "data: ".json_encode($val).",\n";
}

echo "data: ]";
//echo "event: thing\n";

echo "data: hello";

flush();

*/
?>

<?php

date_default_timezone_set("America/New_York");
header("Content-Type: text/event-stream\n\n");

include './sign-out-class.php';

$counter = rand(1, 10);
while (1) {
// 1 is always true, so repeat the while loop forever
  echo "event: ping\n";
  $curDate = date(DATE_ISO8601);
  echo 'data: {"time": "' . $curDate . '"}';
  echo "\n\n";



$sign = new sign_out("localhost", "root", "", "lsmsa");

$last = 0;

$requests = $sign->get_requests();

echo "data: [\n";

foreach($requests as $val) {
	echo "data: ".json_encode($val).",\n";
}

echo "data: ]";
  // Send a simple message at random intervals.
  // flush the output buffer and send echoed messages to the browser
  while (ob_get_level() > 0) {
    ob_end_flush();
  }
  flush();
  // sleep for 1 second before running the loop again
  sleep(4);
}