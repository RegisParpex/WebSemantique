var express = require('express');
var morgan = require('morgan');
var logger = require('log4js').getLogger('Server');
var bodyParser = require('body-parser');
var session = require('express-session');
var mysql = require('mysql');
var app = express();

// config
app.set('view engine', 'ejs');
app.set('views', __dirname + '/views');


app.use(session({secret: 'ns2dk45nrl9vpa', resave: false, saveUninitialized: true,}));
app.use(bodyParser.urlencoded({ extended: false }));
app.use(morgan('combined')); 
app.use(express.static(__dirname + '/public')); 

logger.info('server start');

app.get('/', function(req, res){
    res.redirect('/login');
});

app.get('/login', function(req, res){
	if(!req.session.user) {
		res.render('login');
	} else {
		res.redirect('/main');
	}
});

app.post('/login', function (req, res) {
	var connection = mysql.createConnection({
		host     : 'localhost',
		user     : 'test',
		password : 'test',
		database : 'pictionnary'
	});
	connection.connect();
	var condition = [req.body.email, req.body.password];
	connection.query("SELECT * FROM users WHERE email=? AND password=?", condition, function(err, rows, fields) {
		if (!err) {
			if(rows.length > 0) {
				req.session.user = new userInfo(rows[0]);
				res.redirect('/main');
			} else {
				res.redirect('/login');
			}
		} else {
			console.log('Error while performing Query.' + err);
		}
	});
	connection.end();
});

app.get('/logout', function(req, res) {
	delete req.session.user;
	
    res.redirect('/login');
});

app.get('/inscription', function(req, res) {
    res.render('inscription', {query:req.query});
});

app.post('/inscription', function(req, res) {
	var connection = mysql.createConnection({
		host     : 'localhost',
		user     : 'test',
		password : 'test',
		database : 'pictionnary'
	});
	var post = {email: req.body.email, password: req.body.password, nom: req.body.nom, prenom: req.body.prenom, tel: req.body.tel, website: req.body.website, sexe: req.body.sexe, birthdate: req.body.birthdate, ville: req.body.ville, taille: req.body.taille, couleur: req.body.couleur, profilepic: req.body.profilepic};
	connection.connect();
	connection.query("SELECT COUNT(*) AS numberRow FROM users WHERE email=? ", req.body.email, function(err, rows, fields) {
		if (!err) {
			if(rows[0]['numberRow'] == 0) {
				connection.query('INSERT INTO users SET ?', post, function(err, result) {
					if(!err) {
						req.session.user = new userInfo(post);
						res.redirect('/main');
					} else {
						res.render('inscription');
						logger.info('Error while performing Query insert.' + err);
					}
				});
			} else {
				var query = "nom=" + req.body.nom +
							"&prenom =" + req.body.prenom +
							'&tel=' + req.body.tel +
							'&website=' + req.body.website +
							'&sexe=' + req.body.sexe +
							'&birthdate=' + req.body.birthdate +
							'&ville=' + req.body.ville +
							'&taille=' + req.body.taille +
							'&couleur=' + req.body.couleur;
				res.redirect('inscription/?' + query);
			}
			connection.end();
		} else {
			logger.info('Error while performing Query row count.' + err);
		}
	});
});

app.get('/main', function(req, res) {
	if(!req.session.user) {
		res.redirect('/');
	} else {
		var connection = mysql.createConnection({
			host     : 'localhost',
			user     : 'test',
			password : 'test',
			database : 'pictionnary'
		});
		connection.connect();
		connection.query("SELECT * FROM drawings WHERE userId=? ", req.session.user.id, function(err, rows) {
			if (!err) {
				res.render('main', {user:req.session.user, userDraw:rows});
			} else {
				logger.info('Error while performing Query draw row.' + err);
			}
		});
		connection.end();
	}
});

app.get('/paint', function(req, res) {
	if(!req.session.user) {
		res.redirect('/');
	} else {
		res.render('paint', {color:req.session.color});
	}
});

app.post('/paint', function(req, res) {
	var connection = mysql.createConnection({
		host     : 'localhost',
		user     : 'test',
		password : 'test',
		database : 'pictionnary'
	});
	connection.connect();
	var post = {drawingCommands:req.body.drawingCommands, picture:req.body.picture, userId:req.session.user.id};
	connection.query('INSERT INTO drawings SET ?', post, function(err, rows, fields) {
		if (!err) {
			res.redirect('main');
		} else {
			logger.info('Error while performing Query insert drawnings.' + err);
		}
	});
});

app.get('/guess', function(req, res) {
	if(!req.session.user) {
		res.redirect('/');
	} else {
		var connection = mysql.createConnection({
			host     : 'localhost',
			user     : 'test',
			password : 'test',
			database : 'pictionnary'
		});
		connection.connect();
		connection.query("SELECT drawingCommands FROM drawings WHERE userId=? AND id=? ", [req.session.user.id, req.query.id], function(err, rows) {
			if (!err) {
				if(rows.length > 0) {
					res.render('guess', {command:rows[0]['drawingCommands']});	
				} else {
					res.redirect('/');
				}
			} else {
				logger.info('Error while performing Query draw row.' + err);
			}
		});
	}
});

app.get('/profil/:userId/edit', function(req, res) {
	if(!req.session.user) {
		res.redirect('/');
	} else {
		if(req.session.user.id == req.params.userId) {
			res.render('edit', {query:req.session.user});
		} else {
			res.redirect('/profil/' + req.params.userId);
		}
	}
});

app.post('/profil/:userId/edit', function(req, res) {
	if(!req.session.user) {
		res.redirect('/');
	} else {
		var connection = mysql.createConnection({
			host     : 'localhost',
			user     : 'test',
			password : 'test',
			database : 'pictionnary'
		});
		connection.connect();
		var update = {email:req.body.email, password:req.body.password, nom:req.body.nom, prenom:req.body.prenom, couleur:req.body.couleur, profilePic:req.body.profilePic};
		connection.query("UPDATE user SET ? WHERE ?", [update, {id:req.params.userId}], function(err, rows, fields) {
			if (!err) {
				if(rows.length > 0) {
					if(req.params.userId == req.session.id) {
						req.session.user = new userInfo(update);
						req.session.user.id  = req.params.userId;
					}
					res.redirect('/profil/' + req.params.userId);
				} else {
					res.redirect('/profil/' + req.params.userId + '?err=failed');
				}
			} else {
				console.log('Error while performing Query.' + err);
			}
		});
	}
});

app.get('/profil/:userId', function(req, res) {
	if(!req.session.user) {
		res.redirect('/');
	} else {
		res.render('profil', {user:req.session.user});
	}
});

app.get('/profil', function(req, res) {
	if(!req.session.user) {
		res.redirect('/');
	} else {
		res.render('listProfil');
	}
});

app.use(function(req, res) { res.send('404 not found');});

app.listen(1313);

function userInfo(data) {
	this.id = data['id'];
	this.email = data['email'];
	this.nom = data['nom'];
	this.prenom = data['prenom'];
	this.tel = data['tel'];
	this.website = data['website'];
	this.sexe = data['sexe'];
	this.birthdate = data['birthdate'];
	this.ville = data['ville'];
	this.taille = data['taille'];
	this.couleur = data['couleur'];
	this.profilPic = data['profilepic'].toString('utf8');
}


