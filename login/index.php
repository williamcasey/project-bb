      <form class="form-signin" method="POST" action="">
        <h2 class="form-signin-heading">Please Login</h2>
        <div class="input-group">
	  <span class="input-group-addon" id="basic-addon1">User</span>
	  <input type="text" name="username" class="form-control" placeholder="Username" required>
	</div>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
      </form>

<?php

if(isset($_POST['username']) AND isset($_POST['password'])) {

  include './login-class.php';

  $login = new login("localhost", "root", "", "lsmsa");

  $username = $_POST['username'];
  $password = $_POST['password'];

  if($login->authenticate($username, $password)) {
    session_start();
    $_SESSION['name'] = $login->student_name;

    header('location: ../create_request.php');
    echo $username." successfully logged in.";
  } else {
    echo "login failed: ".$login->login_error;
  }

}

?>