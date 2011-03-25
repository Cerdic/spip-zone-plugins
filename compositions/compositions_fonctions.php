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
	$config_chemin = defined('_DIR_PLUGIN_Z')?'contenu/':'compositions/';
	if (isset($GLOBALS['meta']['compositions'])){
		$config = unserialize($GLOBALS['meta']['compositions']);
		if (isset ($config['chemin_compositions'])){
			$config_chemin = rtrim($config['chemin_compositions'],'/').'/';
		}
	}
	
	return $config_chemin;
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
	// Pipeline compositions_lister_disponibles
	if ($type=="[a-z0-9]+")
		$type = '';
	$res = pipeline('compositions_lister_disponibles',array(
		'args'=>array('type' => $type,'informer' => $informer), 
		'data'=> $res
		)
	);
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

/**
 * Renvoie la composition qui s'applique à un objet
 * en tenant compte, le cas échéant, de la composition héritée
 *
 * @param string $type
 * @param integer $id
 * @param string $serveur
 * @return string
 */
function compositions_determiner($type, $id, $serveur=''){
	include_spip('base/abstract_sql');
	$table = table_objet($type);
	$table_sql = table_objet_sql($type);
	$_id_table = id_table_objet($type);

	$retour = '';

	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table($table,$serveur);
	if (isset($desc['field']['composition']) AND $id){
			$composition = sql_getfetsel('composition', $table_sql, "$_id_table=".intval($id), '', '', '', '', $serveur);
			if ($composition != '')
				$retour = $composition;
			elseif (isset($desc['field']['id_rubrique'])) {
				$_id_rubrique = ($type == 'rubrique') ? 'id_parent' : 'id_rubrique';
				$id_rubrique = sql_getfetsel($_id_rubrique,$table_sql,"$_id_table=".intval($id),'','','','',$serveur);
				$retour = compositions_heriter($type, $id_rubrique, $serveur);
			} else
				$retour = '';
	}
	return ($retour == '-') ? '' : $retour;
}

/**
 * Renvoie la composition héritée par un objet selon sa rubrique
 *
 * @param string $type
 * @param integer $id_rubrique
 * @param string $serveur
 * @return string
 */
function compositions_heriter($type, $id_rubrique, $serveur=''){
	if ($type=='syndic') $type='site'; //grml
	if (intval($id_rubrique) < 1) return '';
	if($infos_rubrique = sql_fetsel(array('id_parent','composition'),'spip_rubriques','id_rubrique='.intval($id_rubrique),'','','','',$serveur)) {
		if (
			$infos_rubrique['composition'] != ''
			AND $infos = compositions_lister_disponibles('rubrique')
			AND isset($infos['rubrique'][$infos_rubrique['composition']])
			AND isset($infos['rubrique'][$infos_rubrique['composition']]['branche'])
			AND isset($infos['rubrique'][$infos_rubrique['composition']]['branche'][$type])
			)
			return $infos['rubrique'][$infos_rubrique['composition']]['branche'][$type];
		else
			return compositions_heriter($type, $infos_rubrique['id_parent'],$serveur);
	}
	return '';
}

/**
 * #COMPOSITION
 * Renvoie la composition s'appliquant à un objet
 * en tenant compte, le cas échéant, de l'héritage
 *
 * @param <type> $p
 * @return <type>
 */
function balise_COMPOSITION_dist($p) {
	$_id_objet = $p->boucles[$p->id_boucle]->primary;
	$id_objet = champ_sql($_id_objet, $p);
	$objet = $p->boucles[$p->id_boucle]->id_table;
	$p->code = "compositions_determiner(objet_type('$objet'), $id_objet)";
	return $p;
}

/**
 * Indique si la composition d'un objet est verrouillée ou non,
 * auquel cas, seul le webmaster peut la modifier
 *
 * @param string $type
 * @param integer $id
 * @param string $serveur
 * @return string
 */
function compositions_verrouiller($type, $id, $serveur=''){
	$config = unserialize($GLOBALS['meta']['compositions']);
	if ($config['tout_verrouiller'] == 'oui')
		return true;
	
	include_spip('base/abstract_sql');
	$table = table_objet($type);
	$table_sql = table_objet_sql($type);
	$_id_table = id_table_objet($type);

	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table($table,$serveur);
	if (isset($desc['field']['composition_lock']) AND $id){
		$lock = sql_getfetsel('composition_lock', $table_sql, "$_id_table=".intval($id), '', '', '', '', $serveur);
		if ($lock)
			return true;
		elseif (isset($desc['field']['id_rubrique'])) {
			$id_rubrique = sql_getfetsel('id_rubrique', $table_sql, "$_id_table=".intval($id), '', '', '', '', $serveur);
			return compositions_verrou_branche($id_rubrique, $serveur);
		}
		else
			return false;
	}
	else return false;
}

/**
 * Indique si les objets d'une branche sont verrouillés
 * @param integer $id_rubrique
 * @param string $serveur
 * @return string
 */
function compositions_verrou_branche($id_rubrique, $serveur=''){
	
	if (intval($id_rubrique) < 1) return false;
	if($infos_rubrique = sql_fetsel(array('id_parent','composition_branche_lock'),'spip_rubriques','id_rubrique='.intval($id_rubrique),'','','','',$serveur)) {
		if ($infos_rubrique['composition_branche_lock'])
			return true;
		else
			return compositions_verrou_branche($infos_rubrique['id_parent'],$serveur);
	}
	return '';
}
?>