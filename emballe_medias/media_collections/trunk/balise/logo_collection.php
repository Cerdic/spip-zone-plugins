<?php
/**
 * Plugin Collections (ou albums)
 * (c) 2012-2013 kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Licence GNU/GPL
 *  
 * Code de la balise #LOGO_COLLECTION
 * 
 * @package SPIP\Collections\Compilateur\Balises
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Compile la balise dynamique #LOGO_COLLECTION
 * 
 * Cette balise peut normalement être appelée comme tout autre logo de SPIP
 * 
 * Appelle la fonction logo_collection pour la génération finale
 * 
 * @param Champ $p
 * 		Pile au niveau de la balise
 * @return Champ
 *		Pile complétée par le code à générer
 */
function balise_LOGO_COLLECTION_dist($p){
	preg_match(",^LOGO_COLLECTION(|_NORMAL|_SURVOL)$,i", $p->nom_champ, $regs);
	$type = 'collection';
	$suite_logo = $regs[1];

	$id_objet = 'id_collection';
	$_id_objet = champ_sql($id_objet, $p);

	$fichier = ($p->etoile === '**') ? -1 : 0;
	$coord = array();
	$align = $lien = '';
	$mode_logo = '';

	if ($p->param AND !$p->param[0][0]) {
		$params = $p->param[0];
		array_shift($params);
		foreach($params as $a) {
			if ($a[0]->type === 'texte') {
				$n = $a[0]->texte;
				if (is_numeric($n))
					$coord[]= $n;
				elseif (in_array($n,array('top','left','right','center','bottom')))
					$align = $n;
				elseif (in_array($n,array('auto','icone','apercu','vignette')))
					$mode_logo = $n;
			}
			else $lien =  calculer_liste($a, $p->descr, $p->boucles, $p->id_boucle);
		}
	}

	$coord_x = !$coord  ? 0 : intval(array_shift($coord));
	$coord_y = !$coord  ? 0 : intval(array_shift($coord));
	
	if ($p->etoile === '*') {
		include_spip('balise/url_');
		$lien = generer_generer_url_arg($type, $p, $_id_objet);
	}

	$connect = $p->id_boucle ?$p->boucles[$p->id_boucle]->sql_serveur :'';
	if ($connect) {
		$code = "''";
	} else {
		$code = logo_collection($id_objet, $_id_objet, $type, $align, $fichier, $lien, $p, $suite_logo);
	}

	// demande de reduction sur logo avec ecriture spip 2.1 : #LOGO_xxx{200, 0}
	if ($coord_x OR $coord_y) {
		$code = "filtrer('image_graver',filtrer('image_reduire',".$code.", '$coord_x', '$coord_y'))"; 
	} 

	$p->code = $code;
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Fonction de recherche de logo d'une collection
 * 
 * Dans un premier temps on regarde si un logo pour la collection est défini par 
 * l'API des logos de SPIP (utilisation de quete_logo)
 * 
 * Dans un second temps, si aucun logo est trouvé, on récupère le contenu du modèle 
 * "modeles/logo_collection" auquel on passe en argument l'identifiant numérique de la collection
 * (id_collection)
 * 
 * @param string $id_objet
 * 		Le nom du champ de l'identifiant numérique de la collection (id_collection)
 * @param int $id_objet
 * 		L'identifiant numérique de la collection dont on cherche le logo
 * @param string $type
 * 		Le type d'objet dont on cherche le logo (collection)
 * @param string $align
 * 		Alignement facultatif du logo ('top','left','right','center','bottom')
 * @param int $fichier
 * 		Indique si on doit retourner uniquement le nom du fichier (-1) ou la balise complète du logo (0)
 * @param string $lien
 * 		URL facultative d'un lien entourant le logo
 * @param Champ $p
 * @param string $suite
 * @return string $code 
 * 		Le code html à afficher pour le logo
 * 
 */
function logo_collection($id_objet, $_id_objet, $type, $align, $fichier, $lien, $p, $suite){
	$code = "quete_logo('$id_objet', '" .
		(($suite == '_SURVOL') ? 'off' : 
		(($suite == '_NORMAL') ? 'on' : 'ON')) .
		"', $_id_objet," .
		(($suite == '_RUBRIQUE') ? 
		champ_sql("id_rubrique", $p) :
		(($type == 'rubrique') ? "quete_parent($_id_objet)" : "''")) .
		", " . intval($fichier) . ")";

	if ($fichier) return $code;

	$code = "\n((!is_array(\$l = $code)) ? recuperer_fond('modeles/logo_collection',array('id_collection'=>$_id_objet)):\n (" .
		     '"<img class=\"spip_logos\" alt=\"\"' .
		    ($align ? " align=\\\"$align\\\"" : '')
		    . ' src=\"$l[0]\"" . $l[2] .  ($l[1] ? " onmouseover=\"this.src=\'$l[1]\'\" onmouseout=\"this.src=\'$l[0]\'\"" : "") . \' />\'))';
	
	if (!$lien) return $code;
		return $code;

	return ('(strlen($logo='.$code.')?\'<a href="\' .' . $lien . ' . \'">\' . $logo . \'</a>\':\'\')');
}

?>
