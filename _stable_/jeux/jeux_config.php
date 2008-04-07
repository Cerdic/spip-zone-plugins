<?php
#---------------------------------------------------#
#  Plugin  : Jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Gestion des scores : Maieul Rouquette, 2007      #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : http://www.spip-contrib.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#

# exemples de jeux avec les mots :
# http://www.cc-concarneaucornouaille.fr/net_bibli/portail/jouer%20avec%20les%20mots.htm
# http://d.ch.free.fr/index.html
# jeux musicaux :
# http://www.metronimo.com/fr/jeux/

// balises du plugin a inserer dans les articles
define('_JEUX_DEBUT', '<jeux>');
define('_JEUX_FIN', '</jeux>');
define('_JEUX_POST', '@@JEUX_POST@@');
define('_JEUX_HEAD1', "\n<!-- CSS & JS JEUX -->\n");
define('_JEUX_HEAD2', "\n<!-- CSS & JS JEUX (AUTO) -->\n");


// separateurs utilisables a l'interieur des balises ci-dessus
// format a utiliser dans la redaction : [separateur]
define('_JEUX_TITRE', 'titre');		// separateur indiquant le titre du jeu
define('_JEUX_TEXTE', 'texte');		// separateur indiquant un contenu a garder telquel
define('_JEUX_CONFIG', 'config');	// separateur permettant de passer des parametres au jeu
define('_JEUX_REPONSE', 'reponse');
define('_JEUX_SOLUTION', 'solution');
define('_JEUX_SCORE', 'score');
define('_JEUX_HORIZONTAL', 'horizontal');
define('_JEUX_VERTICAL', 'vertical');
define('_JEUX_SUDOKU', 'sudoku');
define('_JEUX_KAKURO', 'kakuro');
define('_JEUX_QCM', 'qcm');
define('_JEUX_QRM', 'qrm');
define('_JEUX_QUIZ', 'quiz');
define('_JEUX_CHARADE', 'charade');
define('_JEUX_DEVINETTE', 'devinette');
define('_JEUX_TROU', 'trou');
define('_JEUX_POESIE', 'poesie');
define('_JEUX_CITATION', 'citation');
define('_JEUX_BLAGUE', 'blague');
define('_JEUX_AUTEUR', 'auteur');
define('_JEUX_RECUEIL', 'recueil');
define('_JEUX_PENDU', 'pendu');
define('_JEUX_DIAG_ECHECS', 'diag_echecs');

// globale stockant les carateristiques d'un jeu :
//   - les separateurs autorises
//   - les signatures permettant de reconnaitre un jeu
//   - le nom du jeu
global $jeux_caracteristiques;
$jeux_caracteristiques = array(
// liste des separateurs autorises dans les jeux.
// tous les jeux doivent etre listes ci-apres.
// monjeu est le jeu traite dans le fichier jeux/monjeu.php
 'SEPARATEURS' => array(
	'sudoku' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_SUDOKU, _JEUX_SOLUTION, _JEUX_CONFIG),
	'kakuro' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_KAKURO, _JEUX_SOLUTION, _JEUX_CONFIG),
	'mots_croises' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_HORIZONTAL, _JEUX_VERTICAL, _JEUX_SOLUTION, _JEUX_CONFIG),
	'qcm' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_QCM, _JEUX_QRM, _JEUX_QUIZ, _JEUX_CONFIG, _JEUX_SCORE),
	'textes' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_POESIE, _JEUX_CITATION, _JEUX_BLAGUE, _JEUX_AUTEUR, _JEUX_RECUEIL),
	'devinettes' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_DEVINETTE, _JEUX_CHARADE, _JEUX_REPONSE, _JEUX_CONFIG),
	'trous' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_TROU, _JEUX_CONFIG),
	'pendu' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_PENDU, _JEUX_CONFIG),
	'diag_echecs' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_DIAG_ECHECS, _JEUX_CONFIG),
  ),

// liste des signatures caracteristiques d'un jeu.
// tous les jeux doivent etre listes ci-apres.
// monjeu est le jeu traite dans le fichier jeux/monjeu.php
// exemple :
// array(_JEUX_SEPAR_3, _JEUX_SEPAR_4) doit s'interpreter :
// " le jeu est charge si on trouve _JEUX_SEPAR_3 ou _JEUX_SEPAR_4
//   a l'interieur de <jeu> et </jeu> "
  'SIGNATURES' => array(
	'sudoku' => array(_JEUX_SUDOKU),
	'kakuro' => array(_JEUX_KAKURO),
	'mots_croises' => array(_JEUX_HORIZONTAL, _JEUX_VERTICAL),
	'qcm' => array(_JEUX_QCM, _JEUX_QRM, _JEUX_QUIZ),
	'textes' => array(_JEUX_POESIE, _JEUX_CITATION, _JEUX_BLAGUE),
	'devinettes' => array(_JEUX_DEVINETTE, _JEUX_CHARADE),
	'trous' => array(_JEUX_TROU),
	'pendu' => array(_JEUX_PENDU),
	'diag_echecs' => array(_JEUX_DIAG_ECHECS),
  ),

// nom court a donner aux jeux
  'TYPES' => array(
	'sudoku' => _T('sudoku:titre_court'),
	'kakuro' => _T('kakuro:titre_court'),
	'mots_croises' => _T('motscroises:titre_court'),
	'qcm' => _T('qcm:titre_court'),
	'textes' => _L('Textes'),
	'devinettes' => _L('Devinettes'),
	'trous' => _L('Trous'),
	'pendu' => _T('pendu:titre_court'),
	'diag_echecs' => _L('Echecs')
  ),

);

// addition de tous les separateurs
$temp = array();
foreach($jeux_caracteristiques['SEPARATEURS'] as $sep) $temp=array_merge($temp, $sep);
$jeux_caracteristiques['SEPARATEURS']['la_totale'] = array_unique($temp);
unset($temp);

// liste manuelle des css ou js a placer dans le header prive
// ca peut toujours servir pour les controles...
// dossiers : jeux/style/ et jeux/javascript/
global $jeux_header_prive, $jeux_javascript_prive;
$jeux_header_prive = array('jeux','qcm', 'mots_croises', 'sudoku', 'pendu');
// mots_croises.js suffit car sudoku.js est a priori l'exacte copie
$jeux_javascript_prive = array('layer', 'pendu', 'mots_croises');

// Codes RGB des couleurs predefinies a utiliser pour certains parametres apres la balise [config]
global $jeux_couleurs;
$jeux_couleurs = Array(
  	// en
	'white' => array(255,255,255),
	'black' => array(0,0,0), 
	'grey' => array(211,209,209), 
	'green' => array(191,220,192), 
	'blue' => array(152,192,218), 
	'brown' => array(224,183,153), 
	'lightyellow' => array(247,235,211), 
	'lightbrown' => array(255,243,217),
	// fr
	'blanc' => array(255,255,255),
	'noir' => array(0,0,0), 
	'gris' => array(211,209,209), 
	'vert' => array(191,220,192), 
	'bleu' => array(152,192,218), 
	'brun' => array(224,183,153), 
	'jauneclair' => array(247,235,211), 
	'brunclair' => array(255,243,217),
);

?>