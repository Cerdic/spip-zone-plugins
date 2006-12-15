<?php
#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice�.!vanneufville�@!laposte�.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#
# exemples de jeux avec les mots :
# http://www.cc-concarneaucornouaille.fr/net_bibli/portail/jouer%20avec%20les%20mots.htm
# http://d.ch.free.fr/index.html
# jeux musicaux :
# http://www.metronimo.com/fr/jeux/

// balises du plugin a inserer dans les articles
define('_JEUX_DEBUT', '<jeux>');
define('_JEUX_FIN', '</jeux>');

// separateurs utilisables a l'interieur des balises ci-dessus
// format � utiliser dans la redaction : [separateur]
define('_JEUX_TITRE', 'titre');		// separateur indiquant le titre du jeu
define('_JEUX_TEXTE', 'texte');		// separateur indiquant un contenu a garder telquel
define('_JEUX_CONFIG', 'config');	// separateur permettant de passer des parametres au jeu
define('_JEUX_REPONSE', 'reponse');
define('_JEUX_SOLUTION', 'solution');
define('_JEUX_HORIZONTAL', 'horizontal');
define('_JEUX_VERTICAL', 'vertical');
define('_JEUX_SUDOKU', 'sudoku');
define('_JEUX_KAKURO', 'kakuro');
define('_JEUX_QCM', 'qcm');
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

// liste des separateurs autorises dans les jeux.
// tous les jeux doivent etre listes ci-apres.
// monjeu est le jeu traite dans le fichier inc/monjeu.php
global $jeux_separateurs;
$jeux_separateurs = array(
	'sudoku' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_SUDOKU, _JEUX_SOLUTION, _JEUX_CONFIG),
	'kakuro' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_KAKURO, _JEUX_SOLUTION, _JEUX_CONFIG),
	'mots_croises' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_HORIZONTAL, _JEUX_VERTICAL, _JEUX_SOLUTION, _JEUX_CONFIG),
	'qcm' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_QCM, _JEUX_QUIZ, _JEUX_CONFIG),
	'textes' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_POESIE, _JEUX_CITATION, _JEUX_BLAGUE, _JEUX_AUTEUR, _JEUX_RECUEIL),
	'devinettes' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_DEVINETTE, _JEUX_CHARADE, _JEUX_REPONSE, _JEUX_CONFIG),
	'trous' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_TROU, _JEUX_CONFIG),
	'pendu' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_PENDU, _JEUX_CONFIG),
	'diag_echecs' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_DIAG_ECHECS, _JEUX_CONFIG),
);

// liste des signatures caracteristiques d'un jeu.
// tous les jeux doivent etre listes ci-apres.
// monjeu est le jeu traite dans le fichier inc/monjeu.php
// exemple :
// array(_JEUX_SEPAR_3, _JEUX_SEPAR_4) doit s'interpreter :
// " le jeu est charge si on trouve _JEUX_SEPAR_3 ou _JEUX_SEPAR_4
//   a l'interieur de <jeu> et </jeu> "
global $jeux_signatures;
$jeux_signatures = array(
	'sudoku' => array(_JEUX_SUDOKU),
	'kakuro' => array(_JEUX_KAKURO),
	'mots_croises' => array(_JEUX_HORIZONTAL, _JEUX_VERTICAL),
	'qcm' => array(_JEUX_QCM, _JEUX_QUIZ),
	'textes' => array(_JEUX_POESIE, _JEUX_CITATION, _JEUX_BLAGUE),
	'devinettes' => array(_JEUX_DEVINETTE, _JEUX_CHARADE),
	'trous' => array(_JEUX_TROU),
	'pendu' => array(_JEUX_PENDU),
	'diag_echecs' => array(_JEUX_DIAG_ECHECS),
);

// liste des css a placer dans le header public
// dossier jeux/style/
global $jeux_header_public;
$jeux_header_public = array('jeux', 'qcm', 'mots_croises', 'sudoku');


// liste des css a placer dans le header prive
// dossier jeux/style/
global $jeux_header_prive;
$jeux_header_prive = array('jeux','qcm', 'mots_croises', 'sudoku');		//, 'mots_croises-prive');

// liste des js a placer dans le header prive
// dossier jeux/javascript/
global $jeux_javascript;
$jeux_javascript = array('mots_croises', 'layer');

?>