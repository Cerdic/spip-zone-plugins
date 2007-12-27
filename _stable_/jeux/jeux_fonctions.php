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

include_spip('base/jeux_tables');

// filtre de compatibilite avec SPIP 1.92
function puce_compat192($couleur) {
 if (!defined('_SPIP19300')) {
 	return http_img_pack("puce-$couleur.gif", "puce $couleur", " style='margin: 1px;'");
 }
 return $couleur;
}

function boucle_JEUX($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$mstatut = $id_table .'.statut';
	// non requis sous 1.93
	if(!defined('_SPIP19300')) $boucle->from[] =  "spip_jeux AS $id_table";
//	if (!($boucle->modificateur['criteres']['statut']))
//		{$boucle->where[] = array("'='", "'$mstatut'", "'\\'publie\\''");}
	return calculer_boucle($id_boucle, $boucles);
}

// non requis sous 1.93
if(!defined('_SPIP19300')) {
	function boucle_JEUX_RESULTATS($id_boucle, &$boucles) {
		$boucle = &$boucles[$id_boucle];
		$id_table = $boucle->id_table;
		$boucle->from[] =  "spip_jeux_resultats AS $id_table";
		return calculer_boucle($id_boucle, $boucles);
	}
}

// filtre retournant un lien cliquable si $nb!=0, sinon un simple tiret
function jeux_lien_jeu($nb='0', $exec='', $id_jeu=0) {
	$lien = generer_url_ecrire($exec,'id_jeu='.$id_jeu);
	return $nb=='0'?'-':"<a href='$lien'>$nb</a>";
}
// filtre qui evite d'afficher le resultat obtenu par certains plugins
// grace aux espions : <!-- PLUGIN-DEBUT --> et <!-- PLUGIN-FIN -->
// ou : <!-- PLUGIN-DEBUT-#xxxx --> et <!-- PLUGIN-FIN-#xxxx --> ou xxxx est le numero d'identification du plugin.
if (!function_exists("pas_de_plugin")) {	
 function pas_de_plugin($texte){
		return preg_replace(",<!--\s*PLUGIN-DEBUT(-#[0-9]*)?.*<!--\s*PLUGIN-FIN\\1?\s*-->,UimsS", '', $texte);
 }
}

// filtre qui retire le code source des jeux du texte original
function pas_de_balise_jeux($texte) {
	return preg_replace(",<jeux>.*?</jeux>,UimsS", '', $texte);
}

// aide le Couteau Suisse a calculer la balise #INTRODUCTION
$GLOBALS['cs_introduire'][] = 'pas_de_balise_jeux';

// ajoute un identifiant dans le formulaire, correspondant au jeu
function ajoute_id_jeu($texte, $id_jeu) {
	$texte = str_replace('</form>', "<input type='hidden' name='id_jeu' value='".$id_jeu."'/>\n</form>", $texte);
	return $texte;
;}

?>