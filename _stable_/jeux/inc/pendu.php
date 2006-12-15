<?php

#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#
/*

Insere un jeu de pendu dans vos articles !
------------------------------------------

balises du plugin : <jeux></jeux>
separateurs obligatoires : [pendu]
separateurs optionnels   : [titre], [texte]
paramètres de configurations par defaut :
	regle=non	// Afficher la règle du jeu ?
	pendu=1		// dessin du pendu à utiliser dans : /jeux/img/pendu?

Règles du jeu :
- Vous devez choisir une lettre à chaque essai.
- Si la lettre existe dans le mot, elle apparaît à la bonne place.
- La tête, un bras, une jambe... A chaque erreur, vous êtes un peu plus pendu. 
- Vous avez droit à 6 erreurs. Bonne chance!

Exemple de syntaxe dans l'article :
-----------------------------------

<jeux>
	[titre]
	Le Jazz...
	[pendu]
	morton oliver armstrong ellington whiteman henderson nichols dorsey beiderbecke 
	teagarden freeman kaminsky teschemacher davis goodman wilson hampton crosby parker 
	gillespie powell monk clarke johnson mulligan evans hawkins basie coltrane coleman
</jeux>

*/
// fonctions d'affichage
function pendu_titre($texte) {
 return $texte?"<p class=\"jeux_titre pendu_titre\">$texte</p>":'';
}
function pendu_pendu($texte) {
 $mots = jeux_liste_mots_maj($texte);
 $mot = $mots[array_rand($mots)];
 $question = str_repeat('_ ', strlen($mot));
 for($i=1; $i<=7; $i++) $p .= affiche_un_pendu($i) . '<br>';
 $texte = $p . $mot . '<br>' . $question. '<br>' . affiche_un_clavier();
 return $texte;
 //return $p . ($texte?"<p class=\"jeux_question pendu_pendu\">$texte</p>":'');
}
function pendu_reponse($texte, $id) {
 if (!jeux_config('reponse')) return '';
 include_spip('inc/filtrer');
 $image = image_typo($texte, 'taille=10');
 $image = aligner_droite(filtrer('image_flip_vertical', filtrer('image_flip_horizontal', $image)));
 $texte = jeux_block_invisible($id, _T('jeux:reponse'), $image);
 return $texte?"<span class=\"pendu_reponse\">$texte</span>":'';
}

function affiche_un_pendu($etat) {
 $img = preg_split('/\s*,\s*/', jeux_config($etat));
 $debut = '<img src="'.jeux_config('base_img');
 $fin = '">';
//echo "<br>etat:$etat"; print_r($img);
 return $debut.join($fin.'<br />'.$debut, $img).$fin;
}

function affiche_un_clavier() {
 $clav = preg_split('//', _T('jeux:alphabet'), -1, PREG_SPLIT_NO_EMPTY);
 $debut = '<a href="#">';
 $fin = '</a>';
//echo "<br>etat:$etat"; print_r($img);
 return $debut.join($fin.'&nbsp;'.$debut, $clav).$fin;
}

// fonction principale 
function jeux_pendu($texte, $indexJeux) {
  $html = false;
  jeux_block_init();
  
  // parcourir tous les #SEPARATEURS
  $tableau = jeux_split_texte('pendu', $texte);
  jeux_config_init("
	regle=non	// Afficher la règle du jeu ?
	pendu=1		// dessin du pendu à utiliser dans : /jeux/img/pendu?
  ", false);
  jeux_config_set('base_img', $f = _DIR_PLUGIN_JEUX.'img/pendu'.jeux_config('pendu').'/');
  lire_fichier ($f.'config.ini', $images);
  jeux_config_init($images, false);
//global $jeux_config; print_r($jeux_config);
  foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $html .= pendu_titre($tableau[$i+1]);
	  elseif ($valeur==_JEUX_PENDU) $html .= pendu_pendu($tableau[$i+1], "pendu_$indexJeux_$i");
	  elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
  }
  return $html;
}


/*
TROMBONE XYLOPHONE TAMBOUR PIANO GUITARE VIOLON FLUTE ACCORDEON TROMPETTE DJEMBE SAXOPHONE VIOLONCELLE CLARINETTE HARMONICA TAMBOURIN CASTAGNETTES HAUTBOIS CONTREBASSE HARPE TAM-TAM TRIANGLE CYMBALE FLUTE A BEC FLUTE TRAVERSIERE BANJO GUITARE ELECTRIQUE CORNEMUSE ORGUE MARACAS GROSSE CAISSE VIBRAPHONE BANDONEON BONGOS GUIMBARDE EPINETTE VIELLE PIPEAU CLAIRON BUGLE CLAVES FLUTE DE PAN BALAFON MIRLITON GRELOT TIMBALE DARBOUKA LUTH CARILLON GONG BASSON CLAVECIN HELICON CITHARE MANDOLINE CORNET A PISTONS ALTO COR CAISSE CLAIRE SYNTHETISEUR TUBA

compositeurs :
albeniz bach bartok beethoven bellini berg berlioz bizet borodine boulez brahms britten bruch bruckner charpentier chausson chopin corelli couperin debussy delalande dukas dvorak falla faure franck gluck gounod granados grieg haendel haynd honegger janacek lalo lassus liszt lully machaut mahler massenet mendelsshon messiaen milhaud monteverdi moussorgski mozart offenbach gershwin paganini palestrina pergolesi poulenc prokofiev puccini purcell rachmaninov rameau ravel rossini roussel satie scarlatti schmitt schonberg schubert schumann schutz sibelius smetana strauss stravinski tchaikovski telemann varese verdi vivaldi wagner weber webern

instruments :
flute piccolo syrinx flageolet hautbois cor basson contrebasson clarinette saxophone trompette trombone cornet tuba bugle cornemuse accordeon harmonica harmonium orgue harpe lyre luth mandoline guitare viele violon alto violoncelle contrebasse clavecin epinette piano glockenspiel celesta xylophone marimba vibraphone timbales tambour bongoes catagnettes claves cymbales eoliphone gong maracas tambourin templeblock triangle
		  
jazz :
morton oliver armstrong ellington whiteman henderson nichols dorsey beiderbecke teagarden freeman kaminsky teschemacher davis goodman wilson hampton crosby parker gillespie powell monk clarke johnson mulligan evans hawkins basie coltrane coleman
		  
danses :
allemande bourree chaconne courante forlane gaillarde gavotte gigue menuet passacaille passepied pavane sarabande barcarolle bolero czarda ecossaise habanera laendler mazurka passamezzo polka polonaise ragtime rigaudon saltarelle tango tarentelle tourdion valse

chanson francaise :
adamo aubert aznavour balavoine barbara bashung becaud berger birkin bourvil brassens brel brillant bruel cabrel clerc cordy couture croisille daho dalida dassin dion duteil fabian farmer ferrat ferre fiori gainsbourg goldman gotainer hallyday hardy higelin jonasz kaas lama lapointe lara lavilliers mitchell mouskouri moustaki murat nougaro obispo pagny paradis piaf polnareff voulzy renaud sanson sardou sheller souchon torr trenet vartan


*/

?>