<?php
include 'header.php';

if(isset($_SESSION['email'])) {
?>
		<div>
			<a href="paint.php">Dessiner</a>
		</div>
<?php
	try {
		$dbh = new PDO('mysql:host=localhost;dbname=pictionnary', 'test', 'test');

		$sth = $dbh->prepare("SELECT id FROM drawings WHERE userId= :userId");
		$sth->bindValue(":userId", $_SESSION['id']);
		$sth->execute();
		$i = 0;
		foreach ($sth->fetchAll(PDO::FETCH_ASSOC) as $row) {
			echo "<div><a href=guess.php?id=" . $row['id'] . ">Dessin " . ++$i . "</a></div>";
		}
	} catch (PDOException $e) {
		print "Erreur !: " . $e->getMessage() . "<br/>";
		$dbh = null;
		die();
	}
}
?>
		</body>
</html>