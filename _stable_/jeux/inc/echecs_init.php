<?php

/* Taille des images en pixels (29 ou 35) */
//$image_size = 35;

/* Couleur des cases "blanches" */
/* Couleurs prédéfinies : */
/* white,black,grey,green,blue,brown,lightyellow,lightbrown */
/* Pour définir d'autres couleurs, éditer le fichier includes.inc.php */

/* Code de la police utilisée pour les coordonnées */
/* Entier compris entre 1 et 5, à modifier éventuellement */


global $diag_echecs_globales;
$diag_echecs_globales = Array(
  // Codes RGB des couleurs prédéfinies
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
	// hilite
	'hrouge' => array(255,170,170), 
	'hbleu' => array(101,128,230), 
	'hvert' => array(51,153,102), 
	'hjaune' => array(255,255,102), 
	// diverses initialisations
	'colonnes' => "abcdefgh",
	'english' => array("r"=>"k", "d"=>"q", "t"=>"r", "f"=>"b", "c"=>"n", "p"=>"p"),
	'letter2number' => array("a"=>1, "b"=>2, "c"=>3, "d"=>4, "e"=>5, "f"=>6, "g"=>7, "h"=>8),
	'number2letter' => array(1=>"a", 2=>"b", 3=>"c", 4=>"d", 5=>"e", 6=>"f", 7=>"g", 8=>"h"),
	'english2french' => array("K"=>"r", "Q"=>"d", "R"=>"t", "B"=>"f", "N"=>"c", "P"=>"p", 
  		"k"=>"r", "q"=>"d", "r"=>"t", "b"=>"f", "n"=>"c", "p"=>"p"),
/*
	Language     Piece letters (pawn knight bishop rook queen king)
	----------   --------------------------------------------------
	Czech        P J S V D K
	Danish       B S L T D K
	Dutch        O P L T D K
	English      P N B R Q K	en
	Estonian     P R O V L K
	Finnish      P R L T D K
	French       P C F T D R	fr
	German       B S L T D K	de
	Hungarian    G H F B V K
	Icelandic    P R B H D K
	Italian      P C A T D R	it
	Norwegian    B S L T D K
	Polish       P S G W H K
	Portuguese   P C B T D R
	Romanian     P C N T D R
	Spanish      P C A T D R
	Swedish      B S L T D K
*/
);

// parametres par defaut
function diag_echecs_config_default() {
	jeux_config_init("
		taille=29		// Taille des images en pixels (29 ou 35)
		blancs=blanc	// Couleur des cases 'blanches'
		noirs=brun		// Couleur des cases 'noires'
		fond=blanc		// Couleur de fond de la page web
		bordure=2		// Epaisseur de la bordure de l'échiquier, en pixels
		police=5		// Code de la police utilisée pour les coordonnées (1 à 5)
		flip=non		// Faut-il retourner l'echiquier ?
		coords=oui		// Afficher les coordonnées ?

	", false);
}

function diag_echecs_config_supplementaire() {
	jeux_config_set('base_url', _DIR_PLUGIN_JEUX.'img/diag_echecs'.jeux_config('taille').'/');
//	jeux_config_set('base_url', './img/diag_echecs'.jeux_config('taille').'/');
	jeux_config_set('board_size', intval(jeux_config('taille'))*8);
	if (function_exists("imagepng")) $type = 'png';
	elseif (function_exists("imagegif")) $type = 'gif';
	else { jeux_config_set('fonction_gd_absentes'); return; }
   jeux_config_set('img_suffix', '.'.$type);
   jeux_config_set('img_create', 'imagecreatefrom'.$type);
   jeux_config_set('img_header', 'Content-type: image/'.$type);
   jeux_config_set('img_img', 'image'.$type);
}

/*
Format F.E.N.

FEN correspond à "Forsyth-Edwards Notation". C'est un standard de description d'une position aux échecs. Ce standard utilise la norme ASCII (caractère) pour représenter une position.

Une codification FEN s'identifie par une chaîne de caractères composée de 6 zones séparées par un caractère "espace"

Exemple (position de départ) :

rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1

Zone 1 :

Position des pièces sur l'échiquier et description (format PGN) du contenu de toutes les cases. Les BLANCS en majuscules et les NOIRS en minuscules,

r ou R = TOUR
n ou N = CAVALIER
b ou B = FOU
k ou K = ROI
q ou Q = DAME
p ou P = PION
une caractère numérique = nombre de cases vides
un "/" = saut de ligne

Zone 2 :

w = trait aux BLANCS
b = trait aux NOIRS

Zone 3 :

Possibilité(s) de ROQUE
K = ROQUE BLANC coté ROI possible
k = ROQUE NOIR coté ROI possible
Q= ROQUE BLANC coté DAME possible
q= ROQUE NOIR coté DAME possible
- = (TIRET) aucune possibilité de ROQUE (pour les deux camps)

Zone 4 :

Cette zone précise si une "prise en passant" est possible. La zone contient la case de destination de la prise en passant. La zone contient le caractère "-" si le dernier coup n'est pas un pion qui a avancé de deux cases. (exemple : e3)

Zone 5 :

Cette zone contient le nombre de demi coups joués depuis le dernier pion joué ou depuis une dernière prise. Cette valeur est utilisée pour la règle de la partie nulle au bout de 50 coups. Si le dernier coup est l'avance d'un pion ou une prise, la valeur de la zone est 0.

Zone 6 :

Zone contenant le nombre de coups "terminés" incrémenté après chaque coup des noirs.

Exemples :

FEN pour une position de départ :
rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1
Après le déplacement des BLANCS 1. e4:
rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3 0 1
Après la réponse NOIR 1. ... c5:
rnbqkbnr/pp1ppppp/8/2p5/4P3/8/PPPP1PPP/RNBQKBNR w KQkq c6 0 2
Après le coup des BLANCS 2. Nf3:
rnbqkbnr/pp1ppppp/8/2p5/4P3/5N2/PPPP1PPP/RNBQKB1R b KQkq - 1 2

Pour en savoir plus (en anglais) : http://www.lokasoft.nl/uk/tbapi.htm 
*/

# ----------------------------------------------------------------
# Pour certaines installation de php-gd il peut être nécessaire
#   de décommenter la ligne suivante 

# dl("gd.so"); 

?>