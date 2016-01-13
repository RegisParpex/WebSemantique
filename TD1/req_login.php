<?php

$email=stripslashes($_POST['email']);
$password=stripslashes($_POST['password']);

try {
    $dbh = new PDO('mysql:host=localhost;dbname=pictionnary', 'test', 'test');

    $sth = $dbh->prepare("SELECT * FROM users WHERE email= :email AND password= :password");
	$sth->bindValue(":email", $email);
	$sth->bindValue(":password", $password);
	$sth->execute();
    if ($sth->rowCount() == 0) {
		header('Location: main.php?error='.urlencode("email ou password errone"));
    } else {
		session_start();
		$row = $sth->fetch();
		$_SESSION['email'] = $row['email'];
		$_SESSION['name'] = $row['nom'];
		$_SESSION['profilepic'] = $row['profilepic'];	
		$_SESSION['id'] = $row['id'];		
		header('Location: main.php');
	}
	$dbh = null;
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    $dbh = null;
    die();
}
?>