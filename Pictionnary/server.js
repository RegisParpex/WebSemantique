var express = require('express');
var morgan = require('morgan');
var logger = require('log4js').getLogger('Server');
var bodyParser = require('body-parser');
var session = require('express-session');
var mysql = require('mysql');
var app = express();
var  passport = require('passport');
var Strategy = require('passport-facebook').Strategy;

passport.use(new Strategy({
	clientID: "1104040822953850",
	clientSecret: "344805b756bc8f944e75eaf5b638288f",
	callbackURL: "http://localhost:1313/login/facebook/callback",
	profileFields: ['id', 'displayName', 'email']
}, function(accessToken, refreshToken, profile, cb) {
	return cb(null, profile)
}));

passport.serializeUser(function(user, cb) {
	cb(null, user);
});

passport.deserializeUser(function(obj, cb) {
	cb(null, obj);
});

// config
app.set('view engine', 'ejs');
app.set('views', __dirname + '/views');


app.use(session({secret: 'ns2dk45nrl9vpa', resave: false, saveUninitialized: true,}));
app.use(bodyParser.urlencoded({ extended: false }));
app.use(morgan('combined')); 
app.use(express.static(__dirname + '/public')); 

app.use(passport.initialize());
app.use(passport.session());

logger.info('server start');

app.get('/', function(req, res){
    res.redirect('/login');
});

app.get('/login/facebook', passport.authenticate('facebook'));

app.get('/login/facebook/callback', passport.authenticate('facebook', {failureRedirect : '/login'}), function(req, res) { 
	var connection = mysql.createConnection({
		host     : 'localhost',
		user     : 'test',
		password : 'test',
		database : 'pictionnary'
	});
	connection.connect();
	
	logger.info(req.user);
	connection.query("SELECT * FROM users WHERE facebookId = ?", req.user._json.id, function(err, rows, fields) {
		if (!err) {
			if(rows.length > 0) {	
				req.session.user = {};
				req.session.user.id = rows[0].id;
				req.session.user.email = rows[0].email;
				req.session.user.nom = rows[0].nom;
				res.redirect('/main');
			} else {
				logger.info('New user');
				var insert = {email:req.user._json.email, nom:req.user._json.name, facebookId:req.user._json.id};
				connection.query("INSERT INTO users SET ? ", insert, function(err, rows, fields) {
					if (!err) {
						req.session.id = rows.insertId;
						req.session.email = update.email;
						req.session.nom = update.name;
						req.session.facebookId = update.facebookId;
					} else {
						logger.info('Error while performing Query.' + err);
					}
				});
			}
		} else {
			logger.info('Error while performing Query.' + err);
		}
		connection.end();
	});
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
						req.session.user.id = rows.insertId;
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
			database : 'pictionnary',
			multipleStatements: true
		});
		connection.connect();
		connection.query("SELECT * FROM drawings;",  function(err, rows) {
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
		res.render('paint', {user:req.session.user});
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
			res.redirect('main'); // TODO : Redirect to the new paint
		} else {
			logger.info('Error while performing Query insert drawnings.' + err);
		}
	});
});

app.get('/guess/:id', function(req, res) {
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
		connection.query("SELECT drawingCommands FROM drawings WHERE userId=? AND id=? ", [req.session.user.id, req.params.id], function(err, rows) {
			if (!err) {
				if(rows.length > 0) {
					res.render('guess', {user:req.session.user, command:rows[0]['drawingCommands']});	
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
			res.render('edit', {user:req.session.user});
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
		var update = {email:req.body.email, nom:req.body.nom, prenom:req.body.prenom, couleur:req.body.couleur};
		connection.query("UPDATE users SET ? WHERE ?", [update, {id:req.params.userId}], function(err, rows, fields) {
			if (!err) {
				if(req.session.user.id == req.params.userId) {
					req.session.user.email  = update.email;
					req.session.user.nom  = update.nom;
					req.session.user.prenom  = update.prenom;
					req.session.user.couleur  = update.couleur;
				}
				res.redirect('/profil/' + req.params.userId);
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
		res.render('profil', {user:req.session.user}); // TODO : Get user with special id
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


