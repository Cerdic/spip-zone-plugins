<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_PREG_CRAYON', ',crayon\b[^<>\'"]+?\b((\w+)-(\w+)-(\w+(?:-\w+)*))\b,');

// Compatibilite pour 1.92 : on a besoin de sql_fetch et table_objet_sql
if ($GLOBALS['spip_version_code'] < '1.93' AND $f = charger_fonction('compat_crayons', 'inc'))
	$f();

// Autoriser les crayons sur les tables non SPIP ?
// Par defaut : oui (pour les admins complets, si autoriser_defaut_dist()) ;
// mettre a false en cas de mutualisation par prefixe de table,
// sinon on ne peut pas garantir que les sites sont hermetiques
define('_CRAYONS_TABLES_EXTERNES', true);

// Autorisations non prevues par le core
include_spip('inc/autoriser');

include_spip('inc/crayons-json');


if (!function_exists('autoriser_meta_modifier_dist')) {
/**
 * Autorisation d'éditer les configurations dans spip_meta
 *
 * Les admins complets OK pour certains champs,
 * Sinon, il faut être webmestre
 *
 * @note
 *  Attention sur les SPIP < 11515 (avant 04/2008) inc/autoriser
 *  passe seulement intval($id) alors qu'ici la cle est une chaine...
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_meta_modifier_dist($faire, $type, $id, $qui, $opt) {
	// Certaines cles de configuration sont echapées ici (cf #EDIT_CONFIG{demo/truc})
	// $id = str_replace('__', '/', $id);
	if (in_array("$id", array(
		'nom_site', 'slogan_site', 'descriptif_site', 'email_webmaster'
	)))
		return autoriser('configurer', null, null, $qui);
	else
		return autoriser('webmestre', null, null, $qui);
}
}

// table spip_messages, la c'est tout simplement non (peut mieux faire,
// mais c'est a voir dans le core/organiseur ou dans autorite)
if (defined('_DIR_PLUGIN_ORGANISEUR'))
	include_spip('organiseur_autoriser');
if (!function_exists('autoriser_message_modifier_dist')) {
	function autoriser_message_modifier_dist($faire, $type, $id, $qui, $opt) {
		return false;
	}
}
//compat 192 documents
if ($GLOBALS['spip_version_code'] < '1.93'){
	if (!function_exists('get_spip_doc')){
			function get_spip_doc($fichier) {
					// fichier distant
					if (preg_match(',^\w+://,', $fichier))
							return $fichier;

					// gestion d'erreurs, fichier=''
					if (!strlen($fichier))
							return false;

					// fichier normal
					return (strpos($fichier, _DIR_IMG) === false)
					? _DIR_IMG . $fichier
					: $fichier;
		   }
	}
}

// Autoriser l'usage des crayons ?
function autoriser_crayonner_dist($faire, $type, $id, $qui, $opt) {
	// Le type pouvant etre une table, verifier les autoriser('modifier')
	// correspondant ; ils demandent le nom de l'objet: spip_articles => article
	// ex: spip_articles => 'article'
	$type = preg_replace(',^spip_(.*?)s?$,', '\1', $type);
	if (strlen($GLOBALS['table_prefix']))
		$type = preg_replace(',^'.$GLOBALS['table_prefix'].'_(.*?)s?$,', '\1', $type);

	// Tables non SPIP ? Si elles sont interdites il faut regarder
	// quelle table on appelle, et verifier si elle est "interne"
	if (!_CRAYONS_TABLES_EXTERNES) {
		include_spip('base/serial');
		include_spip('base/auxiliaires');
		include_spip('public/parametrer');
		if (!isset($GLOBALS['tables_principales']['spip_'.table_objet($type)])
		AND !isset($GLOBALS['tables_auxiliaires']['spip_'.table_objet($type)]))
			return false;
	}

	// Traduire le modele en liste de champs
	if (isset($opt['modele']))
		$opt['champ'] = $opt['modele'];

	// Pour un auteur, si le champ est statut ou email, signaler l'option
	// ad hoc (cf. inc/autoriser)
	if ($type == 'auteur'
	AND in_array($opt['champ'], array('statut', 'email')))
		$opt[$opt['champ']] = true;

	return (
		 autoriser('modifier', $type, $id, $qui, $opt)
	);
}

// Si un logo est demande, on renvoie la date dudit logo (permettra de gerer
// un "modifie par ailleurs" si la date a change, rien de plus)
function valeur_champ_logo($table, $id, $champ) {
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$on = $chercher_logo($id, id_table_objet($table), 'on');
	return $on ? filemtime($on[0]) : false;
}

// Idem : si un doc est demande, on renvoie la date du doc
function valeur_champ_document($table, $id, $champ) {
	$s = spip_query("SELECT date FROM spip_documents WHERE id_document="._q($id));
	if ($t = sql_fetch($s))
		return $t['date'];
}

function valeur_champ_vignette($table, $id, $champ) {
	$vignette = sql_getfetsel('id_vignette','spip_documents','id_document='.intval($id));
	if(is_numeric($vignette) && ($vignette > 0)){
		$date = sql_getfetsel('date','spip_documents','id_document='.intval($vignette));
	}
	return $date ? $date : false;
}
// cette fonction de revision recoit le fichier upload a passer en logo
// en reference : le nom du widget, pour aller chercher d'autres donnees
// (ex: supprimer)
function logo_revision($id, $file, $type, $ref) {

	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$_id_objet = id_table_objet($type);

	// Chargement d'un nouveau logo ?
	if ($file['logo']) {
		define('FILE_UPLOAD', true); // message pour crayons_json_export :(

		// supprimer l'ancien logo
		$on = $chercher_logo($id, $_id_objet, 'on');
		if ($on) @unlink($on[0]);

		// ajouter le nouveau
		include_spip('action/iconifier');
		action_spip_image_ajouter_dist(
			type_du_logo($_id_objet).'on'.$id, false, false
		); // beurk
	}

	else

	// Suppression du logo ?
	if ($wid = array_pop($ref)
	AND $_POST['content_'.$wid.'_logo_supprimer'] == 'on') {
		if ($on = $chercher_logo($id, $_id_objet, 'on'))
			@unlink($on[0]);
	}


	// Reduire le logo ?
	if (is_array($cfg = @unserialize($GLOBALS['meta']['crayons']))
	AND $max = intval($cfg['reduire_logo'])) {
		$on = $chercher_logo($id, $_id_objet, 'on');
		include_spip('inc/filtres');
		@copy($on[0], $temp = _DIR_VAR.'tmp'.rand(0,999).'.'.$on[3]);
		$img1 = filtrer('image_reduire', $temp, $max);
		$img2 = preg_replace(',[?].*,', '', extraire_attribut($img1, 'src'));
		if (@file_exists($img2)
		AND $img2 !=  $temp) {
			@unlink($on[0]);
			$dest = $on[1].$on[2].'.'
				.preg_replace(',^.*\.(gif|jpg|png)$,', '\1', $img2);
			@rename($img2,$dest);
		}
		@unlink($temp);
	}

	return true;
}


// cette fonction de revision recoit le fichier upload a passer en document
function document_fichier_revision($id, $data, $type, $ref) {

	$s = spip_query("SELECT * FROM spip_documents WHERE id_document="._q($id));
	if (!$t = sql_fetch($s))
		return false;

	/*
	// Envoi d'une URL de document distant ?
	// TODO: verifier l'extension distante, sinon tout explose
	if ($data['fichier']
	AND preg_match(',^(https?|ftp)://.+,', $data['fichier'])) {
		include_spip('inc/modifier');
		modifier_contenu('document', $id,
			array('champs' => array('fichier', 'distant')),
			array('fichier' => $data['fichier'], 'distant' => 'oui')
		);
		return true;
	}
	else
	*/

	// Chargement d'un nouveau doc ?
	if ($data['document']) {

		$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
		$arg = $data['document'];
		check_upload_error($arg['error']);
		$x = $ajouter_documents($arg['tmp_name'], $arg['name'],
			'article', 0, 'document', null, $actifs);

		// $actifs contient l'id_document nouvellement cree
		// on recopie les donnees interessantes dans l'ancien
		$extension=", extension ";
		//compat 192
		if ($GLOBALS['spip_version_code'] < '1.93')
			$extension="";

		if ($id_new = array_pop($actifs)
		AND $s = spip_query("SELECT fichier, taille, largeur, hauteur $extension, distant FROM spip_documents
			WHERE id_document="._q($id_new))
		AND $new = sql_fetch($s)) {
			define('FILE_UPLOAD', true); // message pour crayons_json_export :(

			// Une vignette doit rester une image
			if ($t['mode'] == 'vignette'
			AND !in_array($new['extension'], array('jpg', 'gif', 'png')))
				return false;

			// Maintenant on est bon, on recopie les nouvelles donnees
			// dans l'ancienne ligne spip_documents
			include_spip('inc/modifier');
			modifier_contenu('document', $id,
				# 'champs' inutile a partir de SPIP 11348
				array('champs' => array_keys($new)),
				$new);

			// supprimer l'ancien document (sauf s'il etait distant)
			if ($t['distant'] != 'oui'
			AND file_exists(get_spip_doc($t['fichier'])))
				supprimer_fichier(get_spip_doc($t['fichier']));

			// Effacer la ligne temporaire de spip_document
			spip_query("DELETE FROM spip_documents WHERE id_document="._q($id_new));

			// oublier id_document temporaire (ca marche chez moi, sinon bof)
			spip_query("ALTER TABLE spip_documents AUTO_INCREMENT="._q($id_new));

			return true;
		}
	}

}

// cette fonction de revision soit supprime la vignette d'un document,
// soit recoit le fichier upload a passer ou remplacer la vignette du document
function vignette_revision($id, $data, $type, $ref) {
	$s = sql_fetsel("*","spip_documents","id_document=".intval($id));
	if (!is_array($s))
		return false;

	include_spip('inc/modifier');
	include_spip('inc/documents');
	// Chargement d'un nouveau doc ?
	if ($data['vignette']) {
		define('FILE_UPLOAD', true);
		if(is_numeric($s['id_vignette']) && ($s['id_vignette']>0)){
			spip_log('suppression de la vignette');
			// Suppression du document
			$vignette = sql_getfetsel('fichier', 'spip_documents', 'id_document='.intval($s['id_vignette']));
			if (@file_exists($f = get_spip_doc($vignette))) { 
				spip_log("efface $f"); 
				supprimer_fichier($f); 
			}
			sql_delete('spip_documents', 'id_document='.intval($s['id_vignette']));
			sql_delete('spip_documents_liens',  'id_document='.intval($s['id_vignette']));

			pipeline('post_edition',
				array(
					'args' => array(
						'operation' => 'supprimer_document',
						'table' => 'spip_documents',
						'id_objet' => $s['id_vignette']
					),
					'data' => null
				)
			);
			
			// On remet l'id_vignette a 0
			revision_document($s['id_document'], array('id_vignette'=>0));
		}
		// Ajout du document comme vignette
		$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
		$arg = $data['vignette'];
		check_upload_error($arg['error']);
		$x = $ajouter_documents($arg['tmp_name'], $arg['name'],'','', 'vignette', $id, $actifs);
	}else
		// Suppression de la vignette ?
		if ($wid = array_pop($ref)
			AND $_POST['content_'.$wid.'_vignette_supprimer'] == 'on') {
			if(is_numeric($s['id_vignette']) && ($s['id_vignette']>0)){
				// Suppression du document
				$vignette = sql_getfetsel('fichier', 'spip_documents', 'id_document='.intval($s['id_vignette']));
				if (@file_exists($f = get_spip_doc($vignette))) { 
					spip_log("efface $f"); 
					supprimer_fichier($f); 
				}
				sql_delete('spip_documents', 'id_document='.intval($s['id_vignette']));
				sql_delete('spip_documents_liens',  'id_document='.intval($s['id_vignette']));
				
				pipeline('post_edition',
					array(
						'args' => array(
							'operation' => 'supprimer_document',
							'table' => 'spip_documents',
							'id_objet' => $s['id_vignette']
						),
						'data' => null
					)
				);
				
				// On remet l'id_vignette a 0
				revision_document($s['id_document'], array('id_vignette'=>0));
			}
		}
	return true;
}

function colonne_table($type, $col) {
	list($distant,$table) = distant_table($type);
	$nom_table = '';
	if (!(($tabref = &crayons_get_table($table, $nom_table)) && ($brut = $tabref['field'][$col]))) {
		return false;
	}
	$ana = explode(' ', $brut);
	$sta = 0;
	$sep = '';
	$ret = array('brut' => $brut,
		'type' => '', 'notnull' => false, 'long' => 0, 'def' => '');
	foreach ($ana as $mot) {
		switch ($sta) {
			case 0:	$ret['type'] = ($mot = strtolower($mot));
			case 1:	if ($mot[strlen($mot) - 1] == ')') {
					$pos = strpos($mot, '(');
					$ret['type'] = strtolower(substr($mot, 0, $pos++));
					$vir = explode(',', substr($mot, $pos, -1));
					if ($ret['type'] == 'enum') {
						$ret['enum'] = $vir;
					} elseif (count($vir) > 1) {
						$ret['long'] = $vir;
					} else {
						$ret['long'] = $vir[0];
					}
					$sta = 1;
					continue;
				}
				if (!$sta) {
					$sta = 1;
					continue;
				}
			case 2: switch (strtolower($mot)) {
				case 'not':
					$sta = 3;
					continue;
				case 'default':
					$sta = 4;
					continue;
				}
				continue;
			case 3: 	$ret['notnull'] = strtolower($mot) == 'null';
				$sta = 2;
				continue;
			case 4:	$df1 = strpos('"\'', $mot[0]) !== false? $mot[0] : '';
				$sta = 5;
			case 5:	$ret['def'] .= $sep . $mot;
				if (!$df1) {
					$sta = 2;
					continue;
				}
				if ($df1 == $mot[strlen($mot) - 1]) {
					$ret['def'] = substr($ret['def'], 1, -1);
					$sta = 2;
				}
				$sep = ' ';
				continue;
		}
	}
	return $ret;
}
//	var_dump(colonne_table('forum', 'id_syndic')); die();

function table_where($type, $id, $where_en_tableau = false)
{
	list($distant,$table) = distant_table($type);
	$nom_table = '';
	if (!(($tabref = &crayons_get_table($type, $nom_table))
			&& ($tabid = explode(',', $tabref['key']['PRIMARY KEY'])))) {
		spip_log('crayons: table ' . $table . ' inconnue');
		return array(false, false);
	}
	if (is_scalar($id)) {
		$id = explode('-', $id);
	}
	// sortie tableau pour sql_updateq
	if ($where_en_tableau) {
		$where = array();
		foreach ($id as $idcol => $idval) {
			$where[] = '`' . (is_int($idcol) ? trim($tabid[$idcol]) : $idcol) . '`=' . sql_quote($idval);
		}
	// sinon sortie texte pour sql_query
	} else {
		$where = $and = '';
		foreach ($id as $idcol => $idval) {
			$where .= $and . '`' . (is_int($idcol) ? trim($tabid[$idcol]) : $idcol) . '`=' . _q($idval);
			$and = ' AND ';
		}
	}
	return array($nom_table, $where);
}
//	var_dump(colonne_table('forum', 'id_syndic')); die();

function valeur_colonne_table_dist($type, $col, $id) {
	list($distant,$table) = distant_table($type);
	list($nom_table, $where) = table_where($type, $id);

	if (!$nom_table)
		return false;

	$r = array();

	// valeurs non SQL
	foreach ($col as $champ) {
		if (function_exists($f = 'valeur_champ_'.$table.'_'.$champ) OR function_exists($f = 'valeur_champ_'.$champ)) {
			$r[$champ] = $f($table, $id, $champ);
			$col = array_diff($col, array($champ));
		}
	}

	// valeurs SQL
	if (count($col)
	AND $s = spip_query(
			'SELECT `' . implode($col, '`, `') .
			'` FROM ' . $nom_table . ' WHERE ' . $where, $distant)
	AND $t = sql_fetch($s))
		$r = array_merge($r, $t);

	return $r;
}

/**
 * Extrait la valeur d'une ou plusieurs colonnes d'une table
 *
 * @param string $table
 *   Type d'objet de la table (article)
 * @param string|array $col
 *   Nom de la ou des colonnes (ps)
 * @param string $id
 *   Identifiant de l'objet
 * @return array
 *   Couples Nom de la colonne => Contenu de la colonne
**/
function valeur_colonne_table($table, $col, $id) {
	if (!is_array($col))
		$col = array($col);

	if (function_exists($f = $table.'_valeur_colonne_table_dist')
	OR function_exists($f = $table.'_valeur_colonne_table')
	OR $f = 'valeur_colonne_table_dist')
		return $f($table, $col, $id);
}

/**
 * Extrait la valeur d'une configuration en meta
 *
 * Pour ces données, il n'y a toujours qu'une colonne (valeur),
 * mais on gère l'enregistrement et la lecture via lire_config ou ecrire_config
 * dès que l'on demande des sous parties d'une configuration.
 *
 * On ne retourne alors ici dans 'valeur' que la sous-partie demandée si
 * c'est le cas.
 *
 * @param string $table
 *   Nom de la table (meta)
 * @param array $col
 *   Nom des colonnes (valeur)
 * @param string $id
 *   Nom ou clé de configuration (descriptif_site ou demo__truc pour demo/truc)
 * @return array
 *   Couple valeur => Contenu de la configuration
**/
function meta_valeur_colonne_table_dist($table, $col, $id) {
	// Certaines clés de configuration sont echapées ici (cf #EDIT_CONFIG{demo/truc})
	$id = str_replace('__', '/', $id);

	// Éviter de planter les vieux SPIP
	if (false === strpos($id, '/')) {
		$config = isset($GLOBALS['meta'][$id]) ? $GLOBALS['meta'][$id] : '';
	// SPIP 3 ou Bonux 2 ou CFG
	} else {
		include_spip('inc/config');
		$config =  lire_config($id, '');
	}
	return array('valeur' => $config);
}


function return_log($var) {
	die(crayons_json_export(array('$erreur'=> var_export($var,true))));
}

function _U($texte, $params=array())
{
    include_spip('inc/charsets');
    return unicode2charset(html2unicode(_T($texte, $params)));
}

// wdgcfg = widget config :-)
function wdgcfg() {
	$php = function_exists('crayons_config') ? crayons_config() : array();
	include_spip('inc/meta');
	lire_metas();
	global $meta;
	$metacrayons = empty($meta['crayons']) ? array() : unserialize($meta['crayons']);
	$wdgcfg = array();
	foreach (array(
		'msgNoChange' => false,
		'msgAbandon' => false,  /* etait: true */
		'filet' => false,
		'yellow_fade' => false,
		'clickhide' => false /* etait: true */
	)
	as $cfgi => $def) {
		$wdgcfg[$cfgi] = isset($php[$cfgi]) ? $php[$cfgi] :
			isset($metacrayons[$cfgi]) ? $metacrayons[$cfgi] : $def;
	}
	return $wdgcfg;
}

function &crayons_get_table($type, &$nom_table) {
	list($distant,$table) = distant_table($type);
	static $return = array();
	static $noms = array();
	if (!isset($return[$table])) {
		$try = array(table_objet_sql($table), 'spip_'.table_objet($table), 'spip_' . $table . 's', $table . 's', 'spip_' . $table, $table);

		// premiere possibilite (à partir de 1.9.3) : regarder directement la base
		if (function_exists('sql_showtable')) {
			foreach ($try as $nom) {
				if ($q = sql_showtable($nom , !$distant , $distant)) {
					$noms[$table] = $nom;
					$return[$table] = $q;
					break;
				}
			}
		}

		// seconde, une heuristique 1.9.2
		if (!isset($return[$table])) {
			include_spip('base/serial');
			include_spip('base/auxiliaires');
			include_spip('public/parametrer');
			foreach(array('tables_principales', 'tables_auxiliaires') as $categ)
			{
				foreach ($try as $nom) {
					if (isset($GLOBALS[$categ][$nom])) {
						$noms[$table] = $nom;
						$return[$table] = & $GLOBALS[$categ][$nom];
						break 2;
					}
				}
			}
		}

	}

	$nom_table = $noms[$table];
	return $return[$table];
}

function distant_table($type) {
	//separation $type en $distant $table
	//separateur double underscore "__"
	strstr($type,'__')? list($distant,$table) = explode('__',$type) : list($distant,$table) = array(False,$type);
	return array($distant,$table);
}
?>
