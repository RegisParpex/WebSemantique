
<?php
session_start();
if(!isset($_SESSION['id'])) {
    header("Location: main.php");
} else {
	$dbh = new PDO('mysql:host=localhost;dbname=pictionnary', 'test', 'test');
	$sth = $dbh->prepare("SELECT drawingCommands FROM drawings WHERE userId= :userId AND id= :id");
	$sth->bindValue(":userId", $_SESSION['id']);
	$sth->bindValue(":id", $_GET['id']);
	$sth->execute();
	if($sth->rowCount() == 0) {
		header("Location: main.php");
	} else {
		$commands = $sth->fetch(PDO::FETCH_COLUMN);
	}
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset=utf-8 />
    <title>Pictionnary</title>
    <link rel="stylesheet" media="screen" href="css/styles.css" >
    <script>
        // la taille et la couleur du pinceau
        var size, color;
        // la dernière position du stylo
        var x0, y0;
        // le tableau de commandes de dessin à envoyer au serveur lors de la validation du dessin
        var drawingCommands = <?php echo $commands;?>;

        window.onload = function() {
            var canvas = document.getElementById('myCanvas');
            canvas.width = 400;
            canvas.height= 400;
            var context = canvas.getContext('2d');

            var start = function(c) {
                size =  c.size;
				color = c.color;
				
				y0 = c.y;
				x0 = c.x;
            }

            var draw = function(c) {
				context.beginPath();
				context.moveTo(x0, y0);
				context.lineTo(c.x, c.y);
				context.lineJoin = "round";
				context.lineWidth = size;
				context.strokeStyle = color;
				context.closePath();
				context.stroke();
				
				y0 = c.y;
				x0 = c.x;
            }

            var clear = function() {
                context.clearRect(0, 0, canvas.width, canvas.height);
            }

            // étudiez ce bout de code
            var i = 0;
            var iterate = function() {
                if(i>=drawingCommands.length)
                    return;
                var c = drawingCommands[i];
                switch(c.command) {
                    case "start":
                        start(c);
                        break;
                    case "draw":
                        draw(c);
                        break;
                    case "clear":
                        clear();
                        break;
                    default:
                        console.error("cette commande n'existe pas "+ c.command);
                }
                i++;
                setTimeout(iterate,30);
            };

            iterate();

        };

    </script>
</head>
<body>
<canvas id="myCanvas"></canvas>
<a href="main.php">Retour</a>
</body>
</html>