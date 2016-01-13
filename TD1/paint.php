
<?php
// on démarre la session, si l'utilisateur n'est pas connecté alors on redirige vers la page main.php.
session_start();
if(!isset($_SESSION['email'])) {
    header("Location: main.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset=utf-8 />
    <title>Pictionnary</title>
    <link rel="stylesheet" media="screen" href="css/styles.css" >
    <script>

        // les quatre tailles de pinceau possible.
        var sizes=[8,20,44,90];
        // la taille et la couleur du pinceau
        var size, color;
        // la dernière position du stylo
        var x0, y0;
        // le tableau de commandes de dessin à envoyer au serveur lors de la validation du dessin
        var drawingCommands = [];

        var setColor = function() {
            // on récupère la valeur du champs couleur
            color = document.getElementById('color').value;
            console.log("color:" + color);
        }

        var setSize = function() {
            // ici, récupèrez la taille dans le tableau de tailles, en fonction de la valeur choisie dans le champs taille.
			size =  sizes[document.getElementById('size').value];
            console.log("size:" + size);
        }

        window.onload = function() {
            var canvas = document.getElementById('myCanvas');
            canvas.width = 400;
            canvas.height= 400;
            var context = canvas.getContext('2d');

            setSize();
            setColor();
            document.getElementById('size').onchange = setSize;
            document.getElementById('color').onchange = setColor;

            var isDrawing = false;

            var startDrawing = function(e) {
                console.log("start");
                // crér un nouvel objet qui représente une commande de type "start", avec la position, la couleur
                var command = {};
                command.command="start";
				var radius = size / 2;
				command.x=e.x - radius;
				command.y=e.y - radius;
				command.size = size;
				command.color = color;
                // on l'ajoute à la liste des commandes
                drawingCommands.push(command);

				x0 = e.x;
				y0 = e.y;
				
                isDrawing = true;
            }

            var stopDrawing = function(e) {
                console.log("stop");

				x0 = e.x;
				y0 = e.y;
				
                isDrawing = false;
            }

            var draw = function(e) {
                if(isDrawing) {
					console.log("draw");
				
                    // ici, créer un nouvel objet qui représente une commande de type "draw", avec la position, et l'ajouter à la liste des commandes.
					var radius = size / 2;
					var centerY = e.y - radius;
					var centerX = e.x - radius;
					
					var command = {};
					command.command="draw";
					command.x=centerX;
					command.y=centerY;
					drawingCommands.push(command);
                    
					// ici, dessinez un cercle de la bonne couleur, de la bonne taille, et au bon endroit. 

					context.beginPath();
					context.moveTo(x0, y0);
					context.lineTo(centerX, centerY);
					context.lineJoin = "round";
					context.lineWidth = size;
					context.strokeStyle = color;
					context.closePath();
					context.stroke();
					
					x0 = centerX;
					y0 = centerY;
                }
            }

            canvas.onmousedown = startDrawing;
            canvas.onmouseout = stopDrawing;
            canvas.onmouseup = stopDrawing;
            canvas.onmousemove = draw;

            document.getElementById('restart').onclick = function() {
                console.log("clear");
                // ici ajouter à la liste des commandes une nouvelle commande de type "clear"
				var command = {};
				command.command="clear";
				drawingCommands.push(command);
				
                // ici, effacer le context, grace à la méthode clearRect.
				context.clearRect(0, 0, canvas.width, canvas.height);
				
			};

            document.getElementById('validate').onclick = function() {
                // la prochaine ligne transforme la liste de commandes en une chaîne de caractères, et l'ajoute en valeur au champs "drawingCommands" pour l'envoyer au serveur.
                document.getElementById('drawingCommands').value = JSON.stringify(drawingCommands);

                // ici, exportez le contenu du canvas dans un data url, et ajoutez le en valeur au champs "picture" pour l'envoyer au serveur.
				document.getElementById('picture').value = canvas.toDataURL();
            };
        };
    </script>
</head>
<body>

<canvas id="myCanvas"></canvas>

<form name="tools" action="req_paint.php" method="post">
	<input type="range" id="size" min="0" max="3" step="1" value="0"/>
	<input type="color" id="color" value=<?php (isset($_SESSION['color']) ? $_SESSION['color'] : "#000000") ?>>

    <input id="restart" type="button" value="Recommencer"/>
    <input type="hidden" id="drawingCommands" name="drawingCommands"/>
    <!-- à quoi servent ces champs hidden ? a permette de transmette les donnée du formulaire  -->
    <input type="hidden" id="picture" name="picture"/>
    <input id="validate" type="submit" value="Valider"/>
</form>
<a href="main.php">Retour</a>
</body>
</html>