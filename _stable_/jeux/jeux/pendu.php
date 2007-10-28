<?php

#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : http://www.spip-contrib.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#
/*
Insere un jeu de pendu dans vos articles !
------------------------------------------

separateurs obligatoires : [pendu]
separateurs optionnels   : [titre], [texte], [config]
parametres de configurations par defaut :
	pendu=1		// dessin du pendu a utiliser (voir : /jeux/img/pendu?)
	regle=non	// Afficher la regle du jeu ?
	indices=non // Afficher les premieres et dernieres lettres?

Regles du jeu :
- Vous devez choisir une lettre a chaque essai.
- Si la lettre existe dans le mot, elle apparait a la bonne place.
- La tete, un bras, une jambe... A chaque erreur, vous etes un peu plus pendu. 
- Vous avez droit a 6 erreurs. Bonne chance!

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
function pendu_pendu($js, $indexJeux) {
 $nb_images = jeux_config('nb_images');
 $js = "\n<script type=\"text/javascript\"><!--$js
	var T_fini=\""._T('pendu:fini').'";
	var T_bravo="'._T('jeux:bravo').'";';
 $proposition = '<input class="pendu_deviner" type="button" readonly=\"readonly" value="ABCDEF" name="cache">';
 $reset = '<input class="pendu_reset" type="button" value="'._T('jeux:rejouer').'" onclick="pendu_init('.$indexJeux.')">'; 
 $images = '';
 $path = _DIR_PLUGIN_JEUX.'img/pendu'.jeux_config('pendu').'/';
 $images_init = preg_split('/\s*,\s*/', jeux_config(1));
 for($i=0; $i<=$nb_images-1; $i++)
 	$images .= "<img class=\"no_image_filtrer pendu_image\" name=\"pict{$indexJeux}_$i\" src=\"$path".$images_init[$i]."\" />";
 $regles = jeux_config('regle')?'<p class="jeux_regle">'.definir_puce()._T('pendu:regle').'</p>' : '';
 $js = echappe_html("$js
 	pendu_init($indexJeux);
// --></script>", 'JEUX', true);
 // scripts autorises ?
 if ((!_DIR_RESTREINT && $GLOBALS["filtrer_javascript"]!=1) || ($GLOBALS["filtrer_javascript"]==-1)) $js = _T('jeux:erreur_scripts');
 // les scripts etaient totalement interdits avant 1.92
 if (defined('_SPIP19100')) $js = _T('jeux:erreur_spip');
 return '<table class="pendu" border=0><tr><td align="center">'
 	. "<p align=\"center\"><div class=\"pendu_images\" align=center>$images</div><br/>\n$proposition</p></td>"
	. "<td width=\"20\">&nbsp;</td><td valign=\"bottom\">\n" . affiche_un_clavier($indexJeux) . '<br/></td></tr>'
	. "<tr><td colspan=\"3\" align=\"right\">$reset</td></tr></table>\n"
 	. $regles . $js;
}

function affiche_un_clavier($indexJeux) {
 $clav = preg_split('//', _T('jeux:alphabet'), -1, PREG_SPLIT_NO_EMPTY);
 foreach ($clav as $i=>$lettre) $clav[$i] = "<input class=\"jeux_bouton pendu_clavier\" type=\"button\" name=\"$lettre\" value=\"$lettre\" onclick=\"pendu_trouve('$lettre', '$indexJeux');\">";
 $i = floor(count($clav)/2);
 $clav = join('', array_slice($clav, 0, $i)) . "<br />\n" . join('', array_slice($clav, $i));
 return "\n<table class=\"pendu_clavier\" border=0><tr><td class=\"pendu_clavier\" >$clav</td></tr></table>";
}

// fonction principale 
function jeux_pendu($texte, $indexJeux) {
  $html = false;
  jeux_block_init();
  
  // parcourir tous les #SEPARATEURS
  $tableau = jeux_split_texte('pendu', $texte);
  // configuration par defaut
  jeux_config_init("
	pendu=1		// dessin du pendu a utiliser dans : /jeux/img/pendu?
	regle=non	// Afficher la regle du jeu ?
	indices=non // Afficher les premieres et dernieres lettres?
  ", false);
  // initialisation des images de pendu
  $path = _DIR_PLUGIN_JEUX.'img/pendu'.jeux_config('pendu').'/';
  lire_fichier ($path.'config.ini', $images);
  jeux_config_init($images, false);
  $i=1; $c=0; $js2=false;
  $extremes = jeux_config('indices')?'true':'false'; // Affiche-t-on les lettres extremes?
  $js="\n\tpendu_Extremes[$indexJeux]=$extremes;\n\tpendu_Paths[$indexJeux]='$path';\n\tpendu_Images[$indexJeux]=new Array(\n\t";
  while(jeux_config($i)) {
    $images = preg_split('/\s*,\s*/', jeux_config($i++));
	$j=1; 
	$js2[]= " new Array('".join("','",$images)."')";
	$c = max($x, count($images));
  } $i-=2;
  jeux_config_set('nb_images',$c);
  $js .= join(",\n\t",$js2) . "\n\t);\n\tnb_pendu_Images[$indexJeux]=$c;\n\tnb_Pendus[$indexJeux]=$i;";
  foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $html .= pendu_titre($tableau[$i+1]);
	  elseif ($valeur==_JEUX_PENDU) $mots = jeux_liste_mots_maj($tableau[$i+1]);
	  elseif ($valeur==_JEUX_TEXTE) $html .= $tableau[$i+1];
  }
  $js .= "\n\tpendu_Mots[$indexJeux]=new Array('".join("','",$mots)."');";
  $html .= pendu_pendu($js, $indexJeux);
  return jeux_form_debut('pendu', $indexJeux).$html.jeux_form_fin();
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