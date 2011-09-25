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
	if (defined('_DIR_PLUGIN_Z') OR defined('_DIR_PLUGIN_ZCORE'))
		$config_chemin = (isset($GLOBALS['z_blocs'])?reset($GLOBALS['z_blocs']):'contenu/');

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
 * @return string
 */
function compositions_styliser_auto(){
	$config_styliser = true;
	if (defined('_DIR_PLUGIN_Z') OR defined('_DIR_PLUGIN_ZCORE')){
		$config_styliser = false; // Z s'occupe de styliser les compositions
	}
	elseif (isset($GLOBALS['meta']['compositions'])){
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
	if (!in_array($table_sql, sql_alltable())) return;
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
 * Decrire une composition pour un objet
 * @param string $type
 * @param string $composition
 * @return array|bool|string
 */
function compositions_decrire($type, $composition){
	static $compositions = array();
	if (!function_exists('compositions_charger_infos'))
		include_spip('inc/compositions');
	if ($type=='syndic') $type='site'; //grml
	if (isset($compositions[$type][$composition]))
		return $compositions[$type][$composition];
	$ext = "html";
	$fond = compositions_chemin() . $type;
	if (strlen($composition)
		AND $f = find_in_path("$fond-$composition.$ext")
		AND $desc = compositions_charger_infos($f))
		return $compositions[$type][$composition] = $desc;
	return $compositions[$type][$composition] = false;
}

/**
 * Un filtre a utiliser sur [(#COMPOSITION|composition_class{#ENV{type}})]
 * pour poser des classes generiques sur le <body>
 * si une balise <class>toto</class> est definie dans la composition c'est elle qui est appliquee
 * sinon on pose simplement le nom de la composition
 * 
 * @param string $composition
 * @param string $type
 * @return string
 */
function composition_class($composition,$type){
	if ($desc = compositions_decrire($type, $composition)
		AND isset($desc['class'])
		AND strlen($desc['class']))
		return $desc['class'];
	return $composition;
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
 * si etoile=true on renvoi dire le champ sql
 *
 * @param string $type
 * @param integer $id
 * @param string $serveur
 * @param bool $etoile
 * @return string
 */
function compositions_determiner($type, $id, $serveur='', $etoile = false){
	static $composition = array();

	if (isset($composition[$etoile][$serveur][$type][$id]))
		return $composition[$etoile][$serveur][$type][$id];

	include_spip('base/abstract_sql');
	$table = table_objet($type);
	$table_sql = table_objet_sql($type);
	$_id_table = id_table_objet($type);

	$retour = '';

	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table($table,$serveur);
	if (isset($desc['field']['composition']) AND $id){
		$select = "composition";
		if (isset($desc['field']['id_rubrique']))
			$select .= "," . (($type == 'rubrique') ? 'id_parent' : 'id_rubrique as id_parent');
		$row = sql_fetsel($select, $table_sql, "$_id_table=".intval($id), '', '', '', '', $serveur);
		if ($row['composition'] != '')
			$retour = $row['composition'];
		elseif (!$etoile
		  AND isset($row['id_parent'])
		  AND $row['id_parent'])
			$retour = compositions_heriter($type, $row['id_parent'], $serveur);
	}
	return $composition[$etoile][$serveur][$type][$id] = (($retour == '-') ? '' : $retour);
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
	static $infos = null;
	$id_parent = $id_rubrique;
	$compo_rubrique = '';
	do {
		$row = sql_fetsel(array('id_parent','composition'),'spip_rubriques','id_rubrique='.intval($id_parent),'','','','',$serveur);
		if (strlen($row['composition']) AND $row['composition']!='-')
			$compo_rubrique = $row['composition'];
		elseif (strlen($row['composition'])==0) // il faut aussi verifier que la rub parente n'herite pas elle-meme d'une composition
			$compo_rubrique = compositions_determiner('rubrique', $id_parent, $serveur='');
		
		if (strlen($compo_rubrique) AND is_null($infos))
			$infos = compositions_lister_disponibles('rubrique');
	}
	while ($id_parent = $row['id_parent']
		AND
		(!strlen($compo_rubrique) OR !isset($infos['rubrique'][$compo_rubrique]['branche'][$type])));

	if (strlen($compo_rubrique) AND isset($infos['rubrique'][$compo_rubrique]['branche'][$type]))
		return $infos['rubrique'][$compo_rubrique]['branche'][$type];

	return '';
}

/**
 * #COMPOSITION
 * Renvoie la composition s'appliquant à un objet
 * en tenant compte, le cas échéant, de l'héritage.
 *
 * Sans precision, l'objet et son identifiant sont pris
 * dans la boucle en cours, mais l'on peut spécifier notre recherche
 * en passant objet et id_objet en argument de la balise :
 * #COMPOSITION{article, 8}
 *
 * #COMPOSITION* renvoie toujours le champs brut, sans tenir compte de l'heritage
 *
 * @param array $p 	AST au niveau de la balise
 * @return array	AST->code modifié pour calculer le nom de la composition
 */
function balise_COMPOSITION_dist($p) {
	$_composition = "";
	if ($_objet = interprete_argument_balise(1, $p)) {
		$_id_objet = interprete_argument_balise(2, $p);
	} else {
		$_composition = champ_sql('composition',$p);
		$_id_objet = champ_sql($p->boucles[$p->id_boucle]->primary, $p);
		$_objet = "objet_type('" . $p->boucles[$p->id_boucle]->id_table . "')";
	}
	// si on veut le champ brut, et qu'on l'a sous la main, inutile d'invoquer toute la machinerie
	if ($_composition AND $p->etoile)
		$p->code = $_composition;
	else {
		$connect = $p->boucles[$p->id_boucle]->sql_serveur;
		$p->code = "compositions_determiner($_objet, $_id_objet, '$connect', ".($p->etoile?'true':'false').")";
		// ne declencher l'usine a gaz que si composition est vide ...
		if ($_composition)
			$p->code = "((\$zc=$_composition)?(\$zc=='-'?'':\$zc):".$p->code.")";
	}
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
