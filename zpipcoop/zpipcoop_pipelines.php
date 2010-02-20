<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction Page automatique a partir de contenu/page-xx
 *
 * @param array $flux
 * @return array
 */
function zpipcoop_styliser($flux){

	$squelette = $flux['data'];
	if (!$squelette // non trouve !
		AND $fond = $flux['args']['fond']
		AND $ext = $flux['args']['ext']){
	  if ($flux['args']['contexte'][_SPIP_PAGE] == $fond) {
			$base = "contenu/page-".$fond.".".$ext;
			if ($base = find_in_path($base)){
				$flux['data'] = substr(find_in_path("page.$ext"), 0, - strlen(".$ext"));
			}
		}
		if (strncmp($fond, "navigation/", 11)==0
		  AND find_in_path("contenu/".substr($fond,11).".$ext")){
			$flux['data'] = substr(find_in_path("navigation/page-dist.$ext"), 0, - strlen(".$ext"));
		}
		if (strncmp($fond, "extra/", 6)==0
			AND find_in_path("contenu/".substr($fond,6).".$ext")){
			$flux['data'] = substr(find_in_path("extra/page-dist.$ext"), 0, - strlen(".$ext"));
		}
	}

	return $flux;

}

/**
 * Surcharger les intertires avant que le core ne les utilise
 * pour y mettre la class h3
 * une seule fois suffit !
 *
 * @param string $flux
 * @return string
 */
function zpipcoop_pre_propre($flux){
	static $init = false;
	if (!$init){
		$intertitre = $GLOBALS['debut_intertitre'];
		$class = extraire_attribut($GLOBALS['debut_intertitre'],'class');
		$class = ($class ? " $class":"");
		$GLOBALS['debut_intertitre'] = inserer_attribut($GLOBALS['debut_intertitre'], 'class', "h3$class");
		foreach($GLOBALS['spip_raccourcis_typo'] as $k=>$v){
			$GLOBALS['spip_raccourcis_typo'][$k] = str_replace($intertitre,$GLOBALS['debut_intertitre'],$GLOBALS['spip_raccourcis_typo'][$k]);
		}
		$init = true;
	}
	return $flux;
}

function zpipcoop_insert_head($flux){
	if (find_in_path('inc-insert-head.html')){
		$flux .= recuperer_fond('inc-insert-head',array());
	}
	return $flux;
}

//
// fonction standard de calcul de la balise #INTRODUCTION
// mais retourne toujours dans un <p> comme propre
//
// http://doc.spip.org/@filtre_introduction_dist
function filtre_introduction($descriptif, $texte, $longueur, $connect) {
	// Si un descriptif est envoye, on l'utilise directement
	if (strlen($descriptif))
		return propre($descriptif,$connect);

	// Prendre un extrait dans la bonne langue
	$texte = extraire_multi($texte);

	// De preference ce qui est marque <intro>...</intro>
	$intro = '';
	$texte = preg_replace(",(</?)intro>,i", "\\1intro>", $texte); // minuscules
	while ($fin = strpos($texte, "</intro>")) {
		$zone = substr($texte, 0, $fin);
		$texte = substr($texte, $fin + strlen("</intro>"));
		if ($deb = strpos($zone, "<intro>") OR substr($zone, 0, 7) == "<intro>")
			$zone = substr($zone, $deb + 7);
		$intro .= $zone;
	}
	$texte = $intro ? $intro : $texte;

	// On ne *PEUT* pas couper simplement ici car c'est du texte brut, qui inclus raccourcis et modeles
	// un simple <articlexx> peut etre ensuite transforme en 1000 lignes ...
	// par ailleurs le nettoyage des raccourcis ne tient pas compte des surcharges
	// et enrichissement de propre
	// couper doit se faire apres propre
	//$texte = nettoyer_raccourcis_typo($intro ? $intro : $texte, $connect);

	// ne pas tenir compte des notes ;
	// bug introduit en http://trac.rezo.net/trac/spip/changeset/12025
	$mem = array($GLOBALS['les_notes'], $GLOBALS['compt_note'], $GLOBALS['marqueur_notes'], $GLOBALS['notes_vues']);
	// memoriser l'etat de la pile unique
	$mem_unique = unique('','_spip_raz_');


	$texte = propre($texte,$connect);


	// restituer les notes comme elles etaient avant d'appeler propre()
	list($GLOBALS['les_notes'], $GLOBALS['compt_note'], $GLOBALS['marqueur_notes'], $GLOBALS['notes_vues']) = $mem;
	// restituer l'etat de la pile unique
	unique($mem_unique,'_spip_set_');


	@define('_INTRODUCTION_SUITE', '&nbsp;(...)');
	$texte = couper($texte, $longueur, _INTRODUCTION_SUITE);

	// Fermer les paragraphes ; mais ne pas en creer si un seul
	$texte = paragrapher($texte, $GLOBALS['toujours_paragrapher']);


	return $texte;
}

?>
