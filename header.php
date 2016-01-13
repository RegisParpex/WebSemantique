<?php
session_start();  
?>
<html>
	<head>
		<title>Truc</title>
	</head>
	<body>
<?php
if(isset($_SESSION['email'])) {
	echo '<h3>Hello ' . $_SESSION['name'] . '<h3/>';
	echo '<img src=' . $_SESSION['profilepic'] . ' /> <br/>';
	echo '<a href="logout.php">Logout</a>';
}  else {
?>
	<form action="req_login.php" method="post">
		<label for="username">Email:</label>
		<input type="text" id="email" name="email" />

		<label for="password">Password:</label>
		<input type="password" id="password" name="password" />

		<button type="submit">Login</button>
		<a href="inscription.php">Inscription</a>
	</form>
<?php
}
?>