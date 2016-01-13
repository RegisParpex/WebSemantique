<!DOCTYPE html>
<html>
	<head>
		<meta charset=utf-8 />
		<title>Pictionnary - Inscription</title>
		<link rel="stylesheet" media="screen" href="css/styles.css" >  
	</head>
	<body>
		<h2>Inscrivez-vous</h2>
		<form class="inscription" action="req_inscription.php" method="post" name="inscription">
			<!-- c'est quoi les attributs action et method ? action : form trettant les data, Method : comment sont envoyer les data -->
			<!-- qu'y a-t-il d'autre comme possiblité que post pour l'attribut method ? Method : Post ou Get -->
			<span class="required_notification">Les champs obligatoires sont indiqués par *</span>
			<ul>
				</li>
				<li>
					<label for="email">E-mail :</label>
					<input type="email" name="email" id="email" placeholder="Email" autofocus required="required"/>
					<!-- quelle est la différence entre les attributs name et id ? Name :formulaire, Id: document -->
					<!-- c'est lequel qui doit être égal à l'attribut for du label ? for -> id element --> 
					<span class="form_hint">Format attendu "name@something.com"</span>
				</li>
				<li>
					<label for="prenom">Prénom :</label>
					<input type="text" name="prenom" id="prenom" placeholder="Prénom" required="required" <?php if(isset($_GET['prenom']))echo 'value='.$_GET['prenom']; ?> />
				</li>
				<li>
					<label for="nom">Nom :</label>
					<input type="text" name="nom" id="nom" placeholder="Nom" v<?php if(isset($_GET['nom']))echo 'value='.$_GET['nom']; ?> />
				</li>
				<li>
					<label for="mdp1">Mot de passe :</label>
					<input type="password" name="password" id="mdp1" placeholder="Mot de passe" pattern="[a-zA-Z0-9].{5,8}" onkeyup="validateMdp2()" title = "Le mot de passe doit contenir de 6 à 8 caractères alphanumériques." required="required">
					<!-- quels sont les deux scénarios où l'attribut title sera affiché ? lorsque la souris passe au dessus ou lorsque le champ et vide lorsque l'user submit -->
					<span class="form_hint">De 6 à 8 caractères alphanumériques.</span>
				</li>
				<li>
					<label for="mdp2">Confirmez mot de passe :</label>
					<input type="password" id="mdp2" placeholder="Confirmation mot de passe" required="required" onkeyup="validateMdp2()">
					<!-- pourquoi est-ce qu'on a pas mis un attribut name ici ? Car cette input ne sert qu'a verifier que le mot de passe est identique et cel creerer un doublon dans le post -->
					<!-- quel scénario justifie qu'on ait ajouté l'écouter validateMdp2() à l'évènement onkeyup de l'input mdp1 ? pour verifier qu'a chaque frappe l'element rentrer correspond a linput precedent -->
					<span class="form_hint">Les mots de passes doivent être égaux.</span>
					<script>
						validateMdp2 = function(e) {
							var mdp1 = document.getElementById('mdp1');
							var mdp2 = document.getElementById('mdp2');
							if (passwordValueIsValid(mdp1.value) &&  mdp1.value == mdp2.value) {
								// ici on supprime le message d'erreur personnalisé, et du coup mdp2 devient valide.
								document.getElementById('mdp2').setCustomValidity('');
							} else {
								// ici on ajoute un message d'erreur personnalisé, et du coup mdp2 devient invalide
								document.getElementById('mdp2').setCustomValidity('Les mots de passes doivent être égaux.');
							}
						}
						function passwordValueIsValid(value)  {
							if(value.length > 8 || value.length < 5) {
								return false;
							}
							return true;
						}
					</script>
				</li>
				<li>
					<label for="telephone">Téléphone :</label>
					<input type="tel" name="tel" id="telephone" placeholder="Téléphone" <?php if(isset($_GET['tel']))echo 'value='.$_GET['tel']; ?> pattern="^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$"/>
				</li>
				<li>
					<label for="siteWeb">Site web :</label>
					<input type="url" name="website" id="siteWeb" placeholder="Site web" <?php if(isset($_GET['website']))echo 'value='.$_GET['website']; ?> />
				</li>
				<li>
					<label>Sexe :</label>
					<input type="radio" name="sexe" value="male" id="male"/><label for="male">Male</label><br/>
					<input type="radio" name="sexe" value="female" id="female"/><label for="female">Female</label>
				</li>
				<li>
					<label for="birthdate">Date de naissance:</label>
					<input type="date" name="birthdate" id="birthdate" placeholder="JJ/MM/AAAA" <?php if(isset($_GET['birthdate']))echo 'value='.$_GET['birthdate']; ?> required onchange="computeAge()"/>
					<script>
						computeAge = function(e) {
							try{
								if(document.getElementById("birthdate").valueAsDate > new Date()) {
									throw "Date invalide";
								}
								document.getElementById("age").value = new Date(Date.now()).getYear() - document.getElementById("birthdate").valueAsDate.getYear();
								if(new Date().getMonth() < document.getElementById("birthdate").valueAsDate.getMonth() || (new Date().getMonth() == document.getElementById("birthdate").valueAsDate.getMonth() && new Date().getDate() < document.getElementById("birthdate").valueAsDate.getDate())) {
									document.getElementById("age").value -= 1;
								}
							} catch(e) {
								document.getElementById("birthdate").value = null;
								document.getElementById("age").value = null;
								alert(e);
							}
						}
					</script>
					<span class="form_hint">Format attendu "JJ/MM/AAAA"</span>
				</li>
				<li>
					<label for="age">Age:</label>
					<input type="number" name="age" id="age" disabled/>
					<!-- à quoi sert l'attribut disabled ? a empecher l'utiliser d'entrer des donées -->
				</li>
				<li>
					<label for="ville">Ville :</label>
					<input type="text" name="ville" id="ville" placeholder="Ville" <?php if(isset($_GET['ville']))echo 'value='.$_GET['ville']; ?> />
				</li>
				<li>
					<label for="taille">Taille :</label>
					<input type="range" name="taille" id="taille" value="0" min="0" max="2.5" step="0.01" placeholder="Taille" <?php if(isset($_GET['taille']))echo 'value='.$_GET['taille']; ?>/>
				</li>
				<li>
					<label for="couleur">Couleur préférée :</label>
					<input type="color"value="#000000" name="couleur" id="couleur" placeholder="Couleur préférée" <?php if(isset($_GET['couleur']))echo 'value='.$_GET['couleur']; ?>/>
				</li>
				<li>
					<label for="profilepicfile">Photo de profil:</label>
					<input type="file" id="profilepicfile" onchange="loadProfilePic(this)"/>
					<span class="form_hint">Choisissez une image.</span>
					<input type="hidden" name="profilepic" id="profilepic"/>
					<canvas id="preview" width="0" height="0"></canvas>
					<script>
						loadProfilePic = function (e) {
							// on récupère le canvas où on affichera l'image
							var canvas = document.getElementById("preview");
							var ctx = canvas.getContext("2d");
							// on réinitialise le canvas: on l'efface, et déclare sa largeur et hauteur à 0
							ctx.fillStyle = "white";
							ctx.rect(0,0,canvas.width,canvas.height);
							ctx.fill();
							canvas.width=0;
							canvas.height=0;
							// on récupérer le fichier: le premier (et seul dans ce cas là) de la liste
							var file = document.getElementById("profilepicfile").files[0];
							// l'élément img va servir à stocker l'image temporairement
							var img = document.createElement("img");
							// l'objet de type FileReader nous permet de lire les données du fichier.
							var reader = new FileReader();
							// on prépare la fonction callback qui sera appelée lorsque l'image sera chargée
							reader.onload = function(e) {
								//on vérifie qu'on a bien téléchargé une image, grâce au mime type
								if (!file.type.match(/image.*/)) {
									// le fichier choisi n'est pas une image: le champs profilepicfile est invalide, et on supprime sa valeur
									document.getElementById("profilepicfile").setCustomValidity("Il faut télécharger une image.");
									document.getElementById("profilepicfile").value = "";
								}
								else {
									// le callback sera appelé par la méthode getAsDataURL, donc le paramètre de callback e est une url qui contient 
									// les données de l'image. On modifie donc la source de l'image pour qu'elle soit égale à cette url
									// on aurait fait différemment si on appelait une autre méthode que getAsDataURL.
									img.src = e.target.result;
									// le champs profilepicfile est valide
									document.getElementById("profilepicfile").setCustomValidity("");
									var MAX_WIDTH = 96;
									var MAX_HEIGHT = 96;
									var width = img.width;
									var height = img.height;

									// A FAIRE: si on garde les deux lignes suivantes, on rétrécit l'image mais elle sera déformée
									// Vous devez supprimer ces lignes, et modifier width et height pour:
									//    - garder les proportions, 
									//    - et que le maximum de width et height soit égal à 96
									var width = MAX_WIDTH;
									var height = MAX_HEIGHT;
									
									canvas.width = width;
									canvas.height = height;
									// on dessine l'image dans le canvas à la position 0,0 (en haut à gauche)
									// et avec une largeur de width et une hauteur de height
									ctx.drawImage(img, 0, 0, width, height);
									// on exporte le contenu du canvas (l'image redimensionnée) sous la forme d'une data url
									var dataurl = canvas.toDataURL("image/png");
									// on donne finalement cette dataurl comme valeur au champs profilepic
									document.getElementById("profilepic").value = dataurl;
								};
							}
							// on charge l'image pour de vrai, lorsque ce sera terminé le callback loadProfilePic sera appelé.
							reader.readAsDataURL(file);
						}
					</script>
				</li>
				<li>
					<input type="submit" value="Soumettre Formulaire">
				</li>
			</ul>
		</form>
		<a href="main.php">Retour</a>
	</body>
</html>