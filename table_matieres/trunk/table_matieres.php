<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


// tester la presence de CFG
$tm = @unserialize($GLOBALS['meta']['table_matieres']);

define('_AUTO_ANCRE', isset($tm['auto']) ? $tm['auto'] : 'oui');
define('_LG_ANCRE', isset($tm['lg']) ? $tm['lg'] : 35);
define('_SEP_ANCRE', isset($tm['sep']) ? $tm['sep'] : '-');
define('_MIN_ANCRE', isset($tm['min']) ? $tm['min'] : 3);
define('_RETOUR_TDM', '<a href="'.ancre_url($GLOBALS['REQUEST_URI'],'tdm').'" class="tdm"><img src="' .
	find_in_path('images/tdm.png') . 
	'" /></a>');

/**
 * Fonction d'API.
 * 
 * Elle reçoit un texte ayant encore les raccourcis SPIP
 * et donc les codes des éventuels intertitres tel que "{{{ intertitre }}}".
 *
 * Elle ajoute une table des matières en entête du texte (par défaut)
 * Et retourne le texte avec les intertitres complétés d'un lien de retour
 * vers le sommaire automatique.
 *
 * En option, on peut demander à la fonction de ne retourner
 * QUE la table des matières.
 *
 * @param string $texte	Texte en entrée
 * @param string $retourner Retourner quoi ? (tout, tdm, texte)
 * @return string	Texte avec une table des matières
 */
function table_matieres($texte, $retourner = 'tout') {
	static $table_matieres = false;

	if (!$texte) {
		return $texte;
	}
	if (!$table_matieres) {
		$table_matieres = charger_fonction('table_matieres', 'inc');
	}
	
	return $table_matieres($texte, $retourner);
}

/**
 * Fonction principale du plugin
 * cf. description sur la fonction d'appel table_matieres()
 *
 * @param string $texte	Texte en entrée
 * @param bool $retourner Retourner quoi ?
 * 		- 'tout' : tout (tdm + texte)
 * 		- 'tdm' : la table des matieres
 * 		- 'texte' : le texte (et les ancres)
 * @return string	Texte avec une table des matières
 */
function inc_table_matieres_dist($texte, $retourner = 'tout') {

	// sauvegarde pour ne pas calculer 2 fois les mêmes choses
	static $textes = array();
	$md5 = md5($texte);

	if (!in_array($retourner, array('tout', 'tdm', 'texte'))) {
		$retourner = 'tout';
		spip_log("Erreur de parametre sur la fonction table_matieres.");
	}
	
	// deja calculé ? on s'en retourne.
	if (isset($textes[$md5])) {
		if ($retourner == 'tout') {
			return $textes[$md5]['tdm'] . $textes[$md5]['texte'];
		}
		return $textes[$md5][$retourner];
	}

	// protection des expressions qui ne font pas partie de la table des matières
	// 
	// le 3e à true pour ne pas utiliser les fonctions d'echappement predefinis
	// et garder les textes tels quels (ex: <code><balise></code>)
	// sinon la transformation est effectuee 2 fois.
	$texte_protege = echappe_html($texte, 'TDM', true);

	// vider les caches d'intertitres trouves
	tdm_vider_intertitres();
	
	// dans un premier temps, on traverse le texte à la recherche d'intertitres.
	// et pour chaque intertitre trouvé, on ajoute un marqueur qui
	// permettra d'ajouter un lien de retour vers l'intertitre.
	// On réalise l'opétation pour chaque type d'intertitre : {{{}}}, ===, {2{...
	// si leurs définitions sont présentes.
	foreach( tdm_remplacements_intertitres() as $regexp => $callback ) {
		$texte_protege = preg_replace_callback($regexp, $callback, $texte_protege);
	}

	// si l'on a trouvé moins d'intertitres que le minimum vital (configurable)
	// on rétablit le texte d'origine et on s'en va !
	$intertitres = tdm_get_intertitres();
	if ( count($intertitres) < _MIN_ANCRE ) {
		$textes[$md5] = array(
			'tdm' => '',
			'texte' => $texte
		);
		if ($retourner == 'tout') {
			return $textes[$md5]['tdm'] . $textes[$md5]['texte'];
		}
		return $textes[$md5][$retourner];
	}
	
	// dévérouillage des protections
	$texte = echappe_retour($texte_protege, 'TDM');

	// calculer la table des matières
	$tdm = tdm_generer_table_des_matieres($intertitres);

	// ajouter les icones de retour vers la table des matieres
	$texte = tdm_ajouter_liens_retour_table_matieres($texte);

	// sauver
	$textes[$md5] = array(
		'tdm' => $tdm,
		'texte' => $texte
	);

	// c'est fini !
	if ($retourner == 'tout') {
		return $textes[$md5]['tdm'] . $textes[$md5]['texte'];
	}
	return $textes[$md5][$retourner];
}

/**
 * Retourne la liste des raccourcis d'intertitres / fonction de remplacements
 * pour la recherche d'intertitres.
 * 
 * Cette fonction pourra être étendue
 * par un pipeline ou une globale 
 *
 * @return arrray  expression régulière / fonction de remplacement
 */
function tdm_remplacements_intertitres() {
	return array(
		"/{{{(.*)}}}/UmsS" => 'tdm_remplacement_raccourcis_standard_callback'
	);
}


/**
 * Intertitre en entrée.
 * L'analyse, stocke l'information et retourne l'intertitre complété 
 *
 * @param string $matches	Captures de l'expression régulière
 * @return l'intertitre complété du code de lien de retour.
 */
function tdm_remplacement_raccourcis_standard_callback($matches) {
	list($titre, $url) = tdm_calculer_titre( $matches[1] ); // intertitre dans /1
	$url = tdm_stocker_intertitre($url, $titre);
	return '{{{ [' . $url . '<-] ' . $matches[1] . ' @@RETOUR_TDM@@ }}}';
}

/**
 * Calcule le titre et l'url de l'ancre
 * a partir d'un intertitre donné 
 *
 * @param string $intertitre
 * @return array titre/url
 */
function tdm_calculer_titre($intertitre) {
	$titre = supprimer_tags(typo($intertitre));
	$titre = preg_replace(",\n[_\s]*,", " ", $titre);
	$url = translitteration($titre);
	$url = @preg_replace(',[[:punct:][:space:]]+,u', ' ', $url);

	// S'il reste des caracteres non latins, utiliser l'id a la place
	if (preg_match(",[^a-zA-Z0-9 ],", $url)) {
		$url = "ancre$cId";
	}
	else {
		$mots = explode(' ', $url);
		$url = '';
		foreach ($mots as $mot) {
			if (!$mot) continue;
			$url2 = $url._SEP_ANCRE.$mot;
			if (strlen($url2) > _LG_ANCRE) {
				break;
			}
			$url = $url2;
		}
		$url = substr($url, 1);
		if (strlen($url) < 2) $url = "ancre$cId";
	}

	return array($titre, $url);
}


/**
 * Remet à zéro la liste des intertitres trouvés
 */
function tdm_vider_intertitres() {
	tdm_stocker_intertitre('', '', true);
}

/**
 * Retourne la liste des intertitres trouvés
 * @return array	Liste des intertitres (url/titre)
 */
function tdm_get_intertitres() {
	return tdm_stocker_intertitre('');
}


/**
 * Stocke les intertitres trouves.
 * Si une url est deja presente, on identifie l'url d'un numero
 *
 * Passer une url vide pour recuperer le tableau.
 *
 * @param string $url	url de l'ancre
 * @param string $titre	titre de l'ancre
 * @param bool $vider	effacer les sauvegarde ?
 * @return 
 */
function tdm_stocker_intertitre($url='', $titre='', $vider = false) {
	static $table = array();
	static $cpt = 0;
	if($vider_table) return ($table = array());
	if (!$url) return $table;
	$cpt++;
	$url = array_key_exists($url, $table) ? $url.$cpt : $url;
	$table[$url] = $titre;
	return $url;
}



/**
 * Remplace les @@RETOUR_TDM@@ laissés par les callback de recherche d'intertitres
 * par le lien de retour correspondant
 *
 * @param string $texte	Texte d'entrée
 * @return string Texte avec retours remplacés
 */
function tdm_ajouter_liens_retour_table_matieres($texte) {
	
	// prendre en compte la langue en cours
	$_RETOUR_TDM = preg_replace(
		',<img,i',
		'<img alt="' . _T('tdm:retour_table_matiere')
		.'" title="' . _T('tdm:retour_table_matiere') . '"',
		_RETOUR_TDM);

	# Si demande en javascript... (pas tres propre, a refaire avec un js externe)
	if (TDM_JAVASCRIPT AND !test_espace_prive() AND !_AJAX # crayons
	) {
		$_RETOUR_TDM = '<script type="text/javascript"><!--
		document.write("'.str_replace('"', '\\"', $_RETOUR_TDM).'");
		--></script>';
	}
	
	return str_replace('@@RETOUR_TDM@@', $_RETOUR_TDM, $texte);
}


/**
 * Générer une table des matieres a partir du tableau
 * d'intertitres donnes 
 *
 * @param array couples liens/intertitres
 * @return string	Code HTML de la table des matieres
 */
function tdm_generer_table_des_matieres($intertitres) {
	// generer un code HTML 
	$code = "";
	foreach ($intertitres as $url=>$titre) {
		$code .= "<li><a href='".ancre_url($GLOBALS['REQUEST_URI'],$url)."'>$titre</a></li>\n";
	}

	// code HTML de la table des matieres
	$_table = recuperer_fond('modeles/table_matieres', array(
		'code' => $code,
		'tableau'=>$intertitres
	));

	# version en javascript (pas tres propre, a refaire avec un js externe)
	if (TDM_JAVASCRIPT AND $_table AND !test_espace_prive()
	AND !_AJAX # crayons
	) {
		$_table = inserer_attribut('<div class="encart"></div>',
			'rel', $_table)
			.'<script type="text/javascript"><!--
			$("div.encart").html($("div.encart").attr("rel")).attr("rel","");
			--></script>';
	}

	return $_table;
}

/**
 * Balise #TABLE_MATIERES
 * Affiche la table des matieres à l'endroit indique
 * A utiliser dans une boucle Articles
 */
function balise_TABLE_MATIERES_dist($p) {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
	if ($b === '') {
		erreur_squelette(
			_T('zbug_champ_hors_boucle',
				array('champ' => '#TABLE_MATIERES')
			), $p->id_boucle);
		$p->code = "''";
	} else {
		$_texte = champ_sql('texte', $p);
		$p->code = "$_texte";
	}
	return $p;
}



?>
