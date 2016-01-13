<?php

// r�cup�rer les �l�ments du formulaire
// et se prot�ger contre l'injection MySQL (plus de d�tails ici: http://us.php.net/mysql_real_escape_string)
$email=stripslashes($_POST['email']);
$password=stripslashes($_POST['password']);
$nom=(empty($_POST['prenom'])) ? 'NULL' : stripslashes($_POST['nom']);
$prenom=stripslashes($_POST['prenom']);
$tel=(empty($_POST['prenom'])) ? 'NULL' : stripslashes($_POST['tel']);
$website=(empty($_POST['prenom'])) ? 'NULL' : stripslashes($_POST['website']);
$sexe='';
if (array_key_exists('sexe',$_POST)) {
    $sexe=stripslashes($_POST['sexe']);
}
$birthdate=stripslashes($_POST['birthdate']);
$ville=(empty($_POST['prenom'])) ? 'NULL' : stripslashes($_POST['ville']);
$taille=(empty($_POST['prenom'])) ? 'NULL' : stripslashes($_POST['taille']);
$couleur=stripslashes($_POST['couleur']);
$profilepic=(empty($_POST['prenom'])) ? 'NULL' : stripslashes($_POST['profilepic']);

try {
    // Connect to server and select database.
    $dbh = new PDO('mysql:host=localhost;dbname=pictionnary', 'test', 'test');

    // V�rifier si un utilisateur avec cette adresse email existe dans la table.
    // En SQL: s�lectionner tous les tuples de la table USERS tels que l'email est �gal � $email.
    $sth = $dbh->prepare("SELECT COUNT(*) FROM users WHERE email= :email");
	$sth->bindValue(":email", $email);
	$sth->execute();
    if ($sth->fetchColumn() > 0) {
		$query_string = 'nom=' . urlencode($nom) . 
						'&prenom=' . urlencode($prenom) . 
						'&tel=' . urlencode($tel) .
						'&website=' . urlencode($website) .
						'&sexe=' . urlencode($sexe) .
						'&birthdate=' . urlencode($birthdate) .
						'&ville=' . urlencode($ville) .
						'&taille=' . urlencode($taille) .
						'&couleur=' . urlencode($couleur);
		
		header('Location: inscription.php?' . $query_string);
        // rediriger l'utilisateur ici, avec tous les param�tres du formulaire plus le message d'erreur
        // utiliser � bon escient la m�thode htmlspecialchars http://www.php.net/manual/fr/function.htmlspecialchars.php          
		// et/ou la m�thode urlencode http://php.net/manual/fr/function.urlencode.php
    }
        // Tenter d'inscrire l'utilisateur dans la base
    else {
        $sql = $dbh->prepare("INSERT INTO users (email, password, nom, prenom, tel, website, sexe, birthdate, ville, taille, couleur, profilepic) "
                . "VALUES (:email, :password, :nom, :prenom, :tel, :website, :sexe, :birthdate, :ville, :taille, :couleur, :profilepic)");
        $sql->bindValue(":email", $email);
		$sql->bindValue(":password", $password);
		$sql->bindValue(":nom", $nom);
		$sql->bindValue(":prenom", $prenom);
		$sql->bindValue(":tel", $tel);
		$sql->bindValue(":website", $website);
		$sql->bindValue(":sexe", $sexe);
		$sql->bindValue(":birthdate", $birthdate);
		$sql->bindValue(":ville", $ville);
		$sql->bindValue(":taille", $taille);
		$sql->bindValue(":couleur", $couleur);
		$sql->bindValue(":profilepic", $profilepic);
        // de m�me, lier la valeur pour le mot de passe
        // lier la valeur pour le nom, attention le nom peut �tre null, il faut alors lier avec NULL, ou DEFAULT
        // idem pour le prenom, tel, website, birthdate, ville, taille, profilepic
        // n.b., notez: birthdate est au bon format ici, ce serait pas le cas pour un SGBD Oracle par exemple
        // idem pour la couleur, attention au format ici (7 caract�res, 6 caract�res attendus seulement)
        // idem pour le prenom, tel, website
        // idem pour le sexe, attention il faut �tre s�r que c'est bien 'H', 'F', ou ''

        // on tente d'ex�cuter la requ�te SQL, si la m�thode renvoie faux alors une erreur a �t� rencontr�e.
        if (!$sql->execute()) {
            echo "PDO::errorInfo():<br/>";
            $err = $sql->errorInfo();
            print_r($err);
        } else {

            // ici d�marrer une session
			session_start();
			
            // ensuite on requ�te � nouveau la base pour l'utilisateur qui vient d'�tre inscrit, et 
            $sql = $dbh->query("SELECT u.id, u.email, u.nom, u.prenom, u.couleur, u.profilepic FROM USERS u WHERE u.email='".$email."'");
            if ($sql->rowCount()<1) {
                header("Location: main.php?erreur=".urlencode("un probl�me est survenu"));
            }
            else {
                // on r�cup�re la ligne qui nous int�resse avec $sql->fetch(),
				$row = $sql->fetch();
                // et on enregistre les donn�es dans la session avec $_SESSION["..."]=...
				$_SESSION['email'] = $row['email'];
				$_SESSION['name'] = $row['nom'];
				$_SESSION['profilepic'] = $row['profilepic'];	
				$_SESSION['id'] = $row['id'];	
            }

            // ici,  rediriger vers la page main.php
			header('Location: main.php');
        }
        $dbh = null;
    }
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    $dbh = null;
    die();
}
?>