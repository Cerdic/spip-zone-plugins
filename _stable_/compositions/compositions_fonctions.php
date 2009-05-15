<?php
/*
 * Plugin Compositions
 * (c) 2007-2009 Cedric Morin
 * Distribue sous licence GPL
 *
 */


define('_COMPOSITIONS_MATCH','-([^0-9][^.]*)');

/**
 * Retrouver le nom du dossier ou sont stockees les compositions
 * reglage par defaut, ou valeur personalisee via cfg
 * 
 * @return string
 */
function compositions_chemin(){
	$config_chemin = 'compositions/';
	if (isset($GLOBALS['meta']['compositions'])){
		$config = unserialize($GLOBALS['meta']['compositions']);
		$config_chemin = rtrim($config['chemin_compositions'],'/').'/';
	}
	return $config_chemin;
}


/**
 * Tester si les reglages permettent d'utiliser les articles d'accueil ou non
 *
 * @return string
 */
function compositions_utiliser_article_accueil(){
		$config_accueil = true;
		if (isset($GLOBALS['meta']['compositions'])){
			$config = unserialize($GLOBALS['meta']['compositions']);
			$config_accueil = $config['utiliser_article_accueil'] != 'non';
		}
		return $config_accueil?' ':'';
}

/**
 * Tester si la stylisation auto est activee
 * @return <type>
 */
function compositions_styliser_auto(){
		$config_styliser = true;
		if (isset($GLOBALS['meta']['compositions'])){
			$config = unserialize($GLOBALS['meta']['compositions']);
			$config_styliser = $config['styliser_auto'] != 'non';
		}
		return $config_styliser?' ':'';
}

/**
 * Lister les compositions disponibles : toutes ou pour un type donne
 * Si informer est a false, on ne charge pas les infos du xml
 *
 * @param string $type
 * @param bool $informer
 * @return array
 */
function compositions_lister_disponibles($type, $informer=true){
	include_spip('inc/compositions');
	$type = preg_replace(',\W,','',$type);
	if ($type=='syndic') $type='site'; //grml
	if (!strlen($type)) $type="[a-z0-9]+";


	// rechercher les skel du type article-truc.html
	// truc ne doit pas commencer par un chiffre pour eviter de confondre avec article-12.html
	$match = "/($type)("._COMPOSITIONS_MATCH.")?[.]html$";

	// lister les compositions disponibles
	$liste = find_all_in_path(compositions_chemin(),$match);
	$res = array();
	if (count($liste)){
		foreach($liste as $s) {
			$base = preg_replace(',[.]html$,i','',$s);
			if (preg_match(",$match,ims",$s,$regs)
			  AND ($composition = !$informer
					   OR $composition = compositions_charger_infos($base)))
				$res[$regs[1]][$regs[3]] = $composition;
			// retenir les skels qui ont un xml associe
		}
	}
	return $res;
}

/**
 * Liste les id d'un type donne utilisant une composition donnee
 *
 * @param string $type
 * @param string $composition
 * @return array
 */
function compositions_lister_utilisations($type,$composition){
	$table_sql = table_objet_sql($type);
	$_id_table_objet = id_table_objet($type);
	return sql_allfetsel("$_id_table_objet as id,titre", $table_sql, "composition=".sql_quote($composition));
}


/**
 * Selectionner le fond en fonction du type et de la composition
 * en prenant en compte la configuration pour le chemin
 * et le fait que la composition a pu etre supprimee
 *
 * @param string $composition
 * @param string $type
 * @param string $defaut
 * @param string $ext
 * @param bool $fullpath
 * @param string $vide
 * @return string
 */
function compositions_selectionner($composition,$type,$defaut="",$ext="html",$fullpath = false, $vide="composition-vide"){
	if ($type=='syndic') $type='site'; //grml
	$fond = compositions_chemin() . $type;

	// regarder si compositions/article-xxx est disponible
	if (strlen($composition)
		AND $f = find_in_path("$fond-$composition.$ext"))
		return $fullpath ? $f : $fond . "-$composition";
	else
		// sinon regarder si compositions/article-defaut est disponible
		if (strlen($defaut)
			AND $f = find_in_path("$fond-$defaut.$ext"))
			return $fullpath ? $f : $fond . "-$defaut";

	// se rabattre sur compositions/article si disponible
	if ($f = find_in_path("$fond.$ext"))
		return $fullpath ? $f : $fond;

	// sinon une composition vide pour ne pas generer d'erreur
	if ($vide AND $f = find_in_path("$vide.$ext"))
		return $fullpath ? $f : $vide;

	// rien mais ca fera une erreur dans le squelette si appele en filtre
	return '';
}


/**
 * Liste les types d'objets qui ont une composition
 * utilise la valeur en cache meta sauf si demande de recalcul
 * ou pas encore definie
 *
 * @staticvar array $liste
 * @return array
 */
function compositions_types(){
	static $liste = null;
	if (is_null($liste)) {
		if ($GLOBALS['var_mode'] OR !isset($GLOBALS['meta']['compositions_types'])){
			include_spip('inc/compositions');
			compositions_cacher();
		}
		$liste = explode(',',$GLOBALS['meta']['compositions_types']);
	}
	return $liste;
}
?>