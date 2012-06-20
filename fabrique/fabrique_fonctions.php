<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Determiner le repertoire de travail
 * de la Fabrique. Dans
 * - plugins/fabrique_auto si possible, sinon dans
 * - tmp/cache/fabrique
 *
 * @return string
 * 		Le chemin de destination depuis la racine de SPIP.
**/
function fabrique_destination() {
	static $destination = null;
	if (is_null($destination)) {
		if (is_writable(_DIR_PLUGINS . rtrim(FABRIQUE_DESTINATION_PLUGINS, '/'))) {
			$destination = _DIR_PLUGINS . FABRIQUE_DESTINATION_PLUGINS;
		} else {
			sous_repertoire(_DIR_CACHE, rtrim(FABRIQUE_DESTINATION_CACHE, '/'));
			$destination = _DIR_CACHE . FABRIQUE_DESTINATION_CACHE;
		}
	}
	return $destination;
}


/**
 * Crée l'arborescence manquante 
 * sous_repertoire_complet('a/b/c/d');
 * appelle sous_repertoire() autant de fois que necessaire.
**/
function sous_repertoire_complet($arbo) {
	$a = explode('/', $arbo);
	if ($a[0] == '.' OR $a[0] == '..') {
		$base = $a[0] . '/' . $a[1];
		array_shift($a);
		array_shift($a);
	} else {
		$base = $a[0];
		array_shift($a);
	}

	foreach ($a as $dir) {
		$base .= '/' . $dir;
		sous_repertoire($base);
	}
}


/**
 * Concatene en utilisant implode un tableau, de maniere recursive 
 *
 * @param array $tableau
 * 		Tableau a transformer
 * @param string $glue
 * 		Chaine inseree entre les valeurs
 * @return string
 * 		Chaine concatenee
**/
function fabrique_implode_recursif($tableau, $glue='') {
	if (!is_array($tableau)) {
		return false;
	}

	foreach ($tableau as $c =>$valeur) {
		if (is_array($valeur)) {
			$tableau[$c] = fabrique_implode_recursif($valeur, $glue);
		}
	}

	return implode($glue, $tableau);
}


/**
 * Fait écrire <?php  
 * sans que ce php soit execute par SPIP !
**/
function balise_PHP_dist($p) {
	$p->code = "'<?php echo \'<?php\n\'; ?>'";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Convertie une chaine pour en faire une chaine de langue
 * &#xxx => le bon caractère
 * ' => \' 
 *
**/
function chaine_de_langue($texte) {
	$texte = html_entity_decode($texte, ENT_QUOTES, 'UTF-8');
	# egalement 
	# http://www.php.net/manual/fr/function.html-entity-decode.php#104617

	return addslashes($texte);
}

/**
 * Modifie le nom de la cle de langue pour
 * utiliser le vrai nom de l'objet
 *
 * titre_objets => titre_chats
 * icone_creer_objet => icone_creer_chat
**/
function cle_de_langue($cle, $desc_objet) {
	// on permet d'echapper \objets pour trouver 'objets' au bout du compte
	// sans qu'il soit transforme dans le nom reel de l'objet
	// cas : 'prive/\objets/infos/objet.html' => 'prive/objets/infos/nom.html'
	$cle = str_replace("\o", "\1o\\", $cle);
	$cle =  str_replace(
		array('objets', 'objet'),
		array($desc_objet['objet'], $desc_objet['type']), $cle);
	return str_replace("\1o\\", "o", $cle);
}

/**
 * Identique a |cle_de_langue sur toutes les valeurs d'un tableau 
 *
**/
function tab_cle_de_langue($tableau, $desc_objet) {
	foreach($tableau as $c => $v) {
		$tableau[$c] = cle_de_langue($v, $desc_objet);
	}
	return $tableau;
}

/**
 * Cherche s'il existe une chaine de langue pour les cles de tableaux
 * et ajoute alors la traduction dans la valeur de ce tableau
 *
 * @param array $tableau
 * 		Tableau cle => texte
 * @param string $prefixe_cle
 * 		Prefixe ajoute aux cles pour chercher les trads
 * @param string $sep
 * 		Séparateur entre l'ancienne valeur et la concaténation de traduction
 * @return array
 * 		Le tableau complété
**/
function tab_cle_traduite_ajoute_dans_valeur($tableau, $prefixe_cle="", $sep = "&nbsp;: ") {
	foreach($tableau as $c => $v) {
		$trad = _T("fabrique:". $prefixe_cle . $c, array(), array('force' => false));
		if ($trad) {
			$tableau[$c] = $v . $sep . $trad;
		} else {
			$tableau[$c] = $v;
		}
	}
	return $tableau;
}

/**
 * Équivalent de wrap() sur les valeurs du tableau
 * 
 * @param array $tableau
 * 		Tableau cle => texte
 * @param string $balise
 * 		Balise qui encapsule
 * @return array $tableau
 * 		Tableau cle => <balise>texte</balise>
**/
function tab_wrap($tableau, $balise) {
	foreach ($tableau as $c => $v) {
		$tableau[$c] = wrap($v, $balise);
	}
	return $tableau;
}


/**
 * Fabrique un tableau de chaines de langues
 * avec des cles d'origines passees dans la fonctions
 * cle_de_langue, et trie. 
**/
function fabriquer_tableau_chaines($objet) {
	$chaines = array();
	if (!is_array($objet)) { return $chaines; }
	if (!$table = $objet['table'] OR !is_array($objet['chaines'])) { return $chaines; }
	// les chaines definies pour l'objet
	foreach ($objet['chaines'] as $cle => $chaine) {
		$chaines[ cle_de_langue($cle, $objet) ] = $chaine;
	}
	// les chaines definies pour chaque champ de l'objet
	if (is_array($objet['champs'])) {
		foreach ($objet['champs'] as $info) {
			$chaines[ cle_de_langue('label_' . $info['champ'], $objet) ] = $info['nom'];
			if ($info['explication']) {
				$chaines[ cle_de_langue('explication_' . $info['champ'], $objet) ] = $info['explication'];
			}
		}
	}
	ksort($chaines);
	return $chaines;
}


/**
 * Indique si le champ est présent dans l'objet
 * (un champ au sens sql)
**/
function champ_present($objet, $champ) {
	if (is_array($objet['champs'])) {
		foreach ($objet['champs'] as $info) {
			if ($info['champ'] == $champ) {
				return " "; // true
			}
		}
	}
	// id_rubrique, id_secteur
	if (isset($objet['rubriques']) AND is_array($objet['rubriques'])) {
		if (in_array($champ, $objet['rubriques'])) {
			return " "; // true
		}
	}
	// lang, langue_choisie, id_trad
	if (isset($objet['langues']) AND is_array($objet['langues'])) {
		if (in_array($champ, $objet['langues'])) {
			return " "; // true
		}
		if ($objet['langues']['lang'] and ($champ == 'langue_choisie')) {
			return " "; // true
		}
	}
	// date
	if ($objet['champ_date']) {
		if ($champ == $objet['champ_date']) {
			return " "; // true
		}
	}
	// statut
	if ($objet['statut']) {
		if ($champ == 'statut') {
			return " "; // true
		}
	}

	return ""; // false
}


/**
 * Indique si toutes les options sont présentes dans l'objet
 * (c'est a dire une cle de configuration, pas un nom de champ SQL)
**/
function options_presentes($objet, $champs) {
	if (!$champs) return false;
	if (!is_array($champs)) $champs = array($champs);
	foreach ($champs as $champ) {
		if (!option_presente($objet, $champ)) {
			return ""; // false
		}
	}
	return " "; // true

}

/**
 * Indique si une option est présente dans l'objet
 * (c'est a dire une cle de configuration, pas un nom de champ SQL)
**/
function option_presente($objet, $champ) {
	// a la racine
	if (isset($objet[$champ]) and $objet[$champ]) {
		return " "; // true
	}

	// id_rubrique, vue_rubrique
	if (isset($objet['rubriques']) AND is_array($objet['rubriques'])) {
		if (in_array($champ, $objet['rubriques'])) {
			return " "; // true
		}
	}

	// lang, id_trad
	if (isset($objet['langues']) AND is_array($objet['langues'])) {
		if (in_array($champ, $objet['langues'])) {
			return " "; // true
		}
	}
	
	// menu_edition, outils_rapides
	if (isset($objet['boutons']) AND is_array($objet['boutons'])) {
		if (in_array($champ, $objet['boutons'])) {
			return " "; // true
		}
	}

	return ""; // false
}


// indique si une option donnée est presente dans le champ
function champ_option_presente($champ, $option) {
	if (isset($champ[$option]) and $champ[$option]) {
		return " "; // true
	}

	// editable, versionne, obligatoire
	if (is_array($champ['caracteristiques'])) {
		if (in_array($option, $champ['caracteristiques'])) {
			return " "; // true
		}
	}

	return false;
}

/**
 * Retourne les objets possedant un certain champ (au sens sql)
 * Cela simplifie des boucles DATA
 * #OBJETS|objets_champ_present{id_rubrique}
 *
 * On peut ne retourner qu'une liste de type de valeur (objet, type, id_objet)
 * #OBJETS|objets_champ_present{id_rubrique, objet} // chats,souris
**/
function objets_champ_present($objets, $champ, $type='') {
	return _tableau_option_presente('champ_present', $objets, $champ, $type);
}


/**
 * Retourne les objets possedant une certaine option
 * (au sens des cles du formulaire de configuration de l'objet)
 * 
 * #OBJETS|objets_option_presente{vue_rubrique}
 * #OBJETS|objets_option_presente{auteurs_liens}
 *
 * On peut ne retourner qu'une liste de type de valeur (objet, type, id_objet)
 * #OBJETS|objets_option_presente{auteurs_liens, objet} // chats,souris
 * 
**/
function objets_option_presente($objets, $option, $type='') {
	return _tableau_option_presente('option_presente', $objets, $option, $type);
}


/**
 * Retourne les objets possedant plusieurs options
 * (au sens des cles du formulaire de configuration de l'objet)
 * 
 * #OBJETS|objets_options_presentes{#LISTE{table_liens,vue_liens}}
 * 
 * On peut ne retourner qu'une liste de type de valeur (objet, type, id_objet)
 * #OBJETS|objets_options_presentes{#LISTE{table_liens,vue_liens}, objet} // chats,souris
 * 
**/
function objets_options_presentes($objets, $options, $type='') {
	return _tableau_options_presentes('option_presente', $objets, $options, $type);
}

/**
 * Retourne des champs en fonction des options trouvees
 * #CHAMPS|champs_option_presente{editable}
 * #CHAMPS|champs_option_presente{versionne}
**/
function champs_option_presente($champs, $option, $type='') {
	return _tableau_option_presente('champ_option_presente', $champs, $option, $type);
}

/**
 * Retourne des champs en fonction des options trouvees
 * #CHAMPS|champs_options_presentes{#LISTE{obligatoire,saisie}}
**/
function champs_options_presentes($champs, $options, $type='') {
	return _tableau_options_presentes('champ_option_presente', $champs, $options, $type);
}


// fonction generique pour retourner une liste de choses dans un tableau
function _tableau_option_presente($func, $tableau, $option, $type='') {
	$o = array();

	if (!is_array($tableau) OR !$func) {
		return $o;
	}
	// tableau est un tableau complexe de donnee
	foreach ($tableau as $objet) {
		// on cherche la donnee 'option' dans le tableau
		// en utilisant une fonction specifique de recherche (option_presente, champ_present, ...)
		if ($func($objet, $option)) {
			// si on a trouve notre option :
			// type permet de recuperer une cle specifique dans la liste des cles parcourues.
			// sinon, retourne tout le sous tableau.
			if ($type and isset($objet[$type])) {
				$o[] = $objet[$type];
			} elseif (!$type) {
				$o[] = $objet;
			}
		}
	}
	return $o;
}

// fonction generique pour retourner une liste de choses multiples dans un tableau
function _tableau_options_presentes($func, $tableau, $options, $type='') {
	if (!$options) return array();

	if (!is_array($options)) {
		$options = array($options);
	}

	$first = false;
	foreach ($options as $option) {
		$r = _tableau_option_presente($func, $tableau, $option, $type);
		if (!$first) {
			$res = $r;
			$first = true;
		} else {
			$res = array_intersect($res, $r);
		}
	}

	return $res;
}


/**
 * Retourne une ecriture de criteres
 * {id_parent?}{id_documentation?}
 * avec tous les champs id_x declares dans l'interface
 * dans la liste des champs.
 * 
 * Cela ne concerne pas les champs speciaux (id_rubrique, id_secteur, id_trad)
 * qui ne seront pas inclus. 
 *
 * @param array $objet
 * 		Description de l'objet
 * @return string
 * 		L'écriture des criteres de boucle
**/
function criteres_champs_id($objet) {
	$ids = array();
	if (is_array($objet['champs'])) {
		foreach ($objet['champs'] as $info) {
			if (substr($info['champ'], 0, 3) == 'id_') {
				$ids[] = $info['champ'];
			}
		}
	}
	if (!$ids) {
		return "";
	}
	return "{" . implode("?}{", $ids) . "?}";
}

/**
 * Retourne un tableau de toutes les tables SQL
 * pour tous les objets.
 *
 * Avec le second paramètre, on peut ne récupérer que :
 * - 'tout' => toutes les tables (par défaut)
 * - 'objets' => les tables d'objet (spip_xx, spip_yy)
 * - 'liens' => les tables de liens (spip_xx_liens, spip_yy_liens)
 *
**/
function fabrique_lister_tables($objets, $quoi='tout') {
	static $tables = array();

	if (!$objets) return array();

	$hash = md5(serialize($objets));
	
	if (!isset($tables[$hash])) {
		$tables[$hash] = array(
			'tout' => array(),
			'objets' => array(),
			'liens' => array(),
		);
		foreach ($objets as $o) {
			// tables principales
			if (isset($o['table']) and $o['table']) {
				$tables[$hash]['objets'][] = $o['table'];
				$tables[$hash]['tout'][] = $o['table'];
				// tables de liens
				if ($o['table_liens']) {
					$tables[$hash]['liens'][] = $o['nom_table_liens'];
					$tables[$hash]['tout'][]  = $o['nom_table_liens'];
				}
			}
		}
	}
	
	return $tables[$hash][$quoi];
}


// indique si un des objets a besoin du pipeline demande
function fabrique_necessite_pipeline($objets, $pipeline) {

	if (!$objets) return false;

	switch ($pipeline) {
		case "autoriser":
		case "declarer_tables_objets_sql":
		case "declarer_tables_interfaces":
			return (bool)fabrique_lister_tables($objets, 'objets');
			break;

		case "declarer_tables_auxiliaires":
			return (bool)fabrique_lister_tables($objets, 'liens');
			break;

		case "affiche_enfants":
			if (objets_option_presente($objets, 'vue_rubrique')) {
				return true;
			}
			break;

		case "affiche_milieu":
			if (objets_option_presente($objets, 'auteurs_liens')
			OR  objets_options_presentes($objets, array('table_liens', 'vue_liens'))) {
				return true;
			}
			break;

		case "affiche_auteurs_interventions":
			if (objets_option_presente($objets, 'vue_auteurs_liens')) {
				return true;
			}
			break;

		case "afficher_contenu_objet":
			return false;
			break;

		case "optimiser_base_disparus":
			# nettoie depuis spip_{objet}_liens
			# mais aussi les liaisions vers spip_{objet} (uniquement si une table de liens existe)
			return (bool)fabrique_lister_tables($objets, 'liens');
			#return (bool)fabrique_lister_tables($objets, 'objets');
			break;
	}
	return false;
}


/**
 * Un peu equivalent a var_export
 * si $quote = true, on applique sql_quote sur tous les champs
 * 
 * @param array $tableau
 * 		Le tableau dont on veut obtenir le code de creation array( ... )
 * @param bool $quote
 * 		Appliquer sql_quote() sur chaque valeur (dans le code retourne)
 * @param string $defaut
 * 		Si $tableau est vide ou n'est pas une chaine, la fonction retourne cette valeur
 * @return string
 * 		Le code de creation du tableau, avec eventuellement le code pour appliquer sql_quote.
 * 
**/
function ecrire_tableau($tableau, $quote = false, $defaut = "array()") {
	// pas de tableau ?
	if (!is_array($tableau) OR !count($tableau)) {
		return $defaut;
	}

	$res = "array('" . implode("', '", array_map('addslashes', $tableau)) . "')";

	if ($quote) {
		$res = "array_map('sql_quote', $res)";
	}
	return $res;
}

/**
 * Identique a ecrire_tableau() mais ne retourne rien si le tableau est vide
 *
**/
function ecrire_tableau_sinon_rien($tableau, $quote = false) {
	return ecrire_tableau($tableau, $quote, "");
}

// un peu equivalent a str_pad mais avec une valeur par defaut.
function espacer($texte, $taille = 0) {
	if (!$taille) $taille = _FABRIQUE_ESPACER;
	return str_pad($texte, $taille);
}


// tabule a gauche chaque ligne du nombre de tabulations indiquees
// + on enleve les espaces sur les lignes vides
function fabrique_tabulations($texte, $nb_tabulations) {
	$tab = "";
	if ($nb_tabulations) {
		$tab = str_pad("\t", $nb_tabulations);
	}
	$texte = explode("\n", $texte);
	foreach ($texte as $c => $ligne) {
		$l = ltrim(ltrim($ligne), "\t");
		if (!$l) {
			$texte[$c] = "";
		} else {
			$texte[$c] = $tab . $ligne;
		}
	}
	return implode("\n", $texte);
}




/**
 * Passer en majuscule en utilisant mb de preference
 * s'il est disponible. 
 *
 * @param string $str
 * 		La chaine a passer en majuscule
 * @return string
 * 		La chaine en majuscule
**/
function fabrique_mb_strtoupper($str) {
	if (function_exists('mb_strtoupper')) {
		return mb_strtoupper($str);
	} else {
		return strtoupper($str);
	}
}

/**
 * Passer en minuscule en utilisant mb de preference
 * s'il est disponible. 
 *
 * @param string $str
 * 		La chaine a passer en minuscule
 * @return string
 * 		La chaine en minuscule
**/
function fabrique_mb_strtolower($str) {
	if (function_exists('mb_strtolower')) {
		return mb_strtolower($str);
	} else {
		return strtolower($str);
	}
}


// Afficher une image a partir d'un fichier, selon une reduction donnee
// (evite un |array_shift qui passe pas en PHP 5.4)
// Attention à bien rafraichir l'image reduite lorsqu'on change de logo
// #URL_IMAGE|image_reduire{128}|extraire_attribut{src}|explode{?}|array_shift|timestamp|balise_img
function filtre_fabrique_miniature_image($fichier, $taille=256) {
	$im = filtrer('image_reduire', $fichier, $taille);
	$im = extraire_attribut($im, 'src');
	$im = explode('?', $im);
	$im = array_shift($im);
	$im = timestamp($im);
	$im = filtrer('balise_img', $im);
	return $im;
}



/**
 * Retourne un tableau table_sql=>Nom des objets de SPIP
 * complété des objets declares dans la fabrique ainsi
 * que de tables indiquees même si elles ne font pas parties
 * de declarations connues.
 *
 * @param array $objets_fabrique
 * 		Déclaration d'objets de la fabrique
 * @param array $inclus
 * 		Liste de tables SQL que l'on veut forcement presentes
 * 		meme si l'objet n'est pas declare
 * @param array $exclus
 * 		Liste de tables SQL que l'on veut forcement exclues
 * 		meme si l'objet n'est pas declare
 * @return array
 * 		Tableau table_sql => Nom
**/
function filtre_fabrique_lister_objets_editoriaux($objets_fabrique, $inclus=array(), $exclus=array()) {

	// les objets existants
	$objets = lister_tables_objets_sql();
	foreach ($objets as $sql => $o) {
		if ($o['editable']) {
			$liste[$sql] = _T($o['texte_objets']);
		}
	}
	unset($objets);

	// les objets de la fabrique
	foreach ($objets_fabrique as $o) {
		if (isset($o['table']) and !isset($liste[$o['table']])) {
			$liste[ $o['table'] ] = $o['nom'];
		}
	}

	// des objets qui n'existent pas mais qui sont actuellement coches dans la saisie
	foreach ($inclus as $sql) {
		if (!isset($liste[$sql])) {
			$liste[$sql] = $sql; // on ne connait pas le nom
		}
	}

	// tables forcement exclues
	foreach ($exclus as $sql) {
		unset($liste[$sql]);
	}
	// enlever un eventuel element vide
	unset($liste['']);

	asort($liste);

	return $liste;
}


/**
 * Retourne le code pour tester un type d'autorisation
 *
 * @param string $type
 * 		Quelle type d'autorisation est voulue
 * @return string
 * 		Code de test de l'autorisation
**/
function fabrique_code_autorisation($type) {
	switch($type) {

		case "jamais":
			return "false";
			break;

		case "toujours":
			return "true";
			break;

		case "redacteur":
			return "in_array(\$qui['statut'], array('0minirezo', '1comite'))";
			break;

		case "administrateur_restreint":
			return "\$qui['statut'] == '0minirezo'";
			break;

		case "administrateur":
			return "\$qui['statut'] == '0minirezo' AND !\$qui['restreint']";
			break;

		case "webmestre":
			return "autoriser('webmestre', '', '', \$qui)";
			break;

	}

	return "";
}

/**
 * Retourne la valeur de type d'autorisation
 * qui s'applique par defaut pour une autorisation donnee 
 *
 * @param string $autorisation
 * 		Nom de l'autorisation (objet et objets remplacent le veritable type et nom d'objet)
 * @return string
 * 		Type d'autorisation par defaut (jamais, toujours, redacteur, ...)
**/
function fabrique_autorisation_defaut($autorisation) {
	switch($autorisation) {
		case 'objet_voir':
			return "toujours";
			break;

		case 'objet_creer':
		case 'objet_modifier':
			return "redacteur";
			break;

		case 'objet_supprimer':
		case 'associerobjet':
			return "administrateur";
			break;
	}
}

/**
 * Retourne le code d'autorisation indique
 * sinon celui par defaut pour une fonction d'autorisation
 *
 * @param array $autorisations
 * 		Les autorisations renseignees par l'interface pour un objet
 * @param string $autorisation
 * 		Le nom de l'autorisation souhaitee
 * @return string
 * 		Code de l'autorisation
**/
function fabrique_code_autorisation_defaut($autorisations, $autorisation) {
	if (!$autorisation) return "";

	// trouver le type d'autorisation souhaitee, soit indiquee, soit par defaut
	if (!isset($autorisations[$autorisation]) OR !$type = $autorisations[$autorisation]) {
		$type = fabrique_autorisation_defaut($autorisation);
	}

	// retourner le code PHP correspondant
	return fabrique_code_autorisation($type);
}

/**
 * Retourne le type pour le nom d'une fonction d'autorisation 
 * 'article' => 'article'
 * 'truc_muche' => 'trucmuche'
 * 
 * @param string $type
 * 		Type ou objet
 * @return string
 * 		Type pour le nom d'autorisation
**/
function fabrique_type_autorisation($type) {
	return str_replace('_', '', $type);
}

?>
