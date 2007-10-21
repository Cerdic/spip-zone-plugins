<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_PREG_CRAYON', ',crayon\b[^<>\'"]+?\b((\w+)-(\w+)-(\d+(?:-\w+)?))\b,');

if ($GLOBALS['spip_version_code']<1.9300) {
	function sql_fetch($res, $serveur=''){ return spip_fetch_array($res); }
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

// cette fonction de revision recoit le fichier upload a passer en logo
// en reference : le nom du widget, pour aller chercher d'autres donnees
// (ex: supprimer)
function logo_revision($id, $file, $type, $ref) {

	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$_id_objet = id_table_objet($type);

	// Chargement d'un nouveau logo ?
	if ($file['logo']) {
		define('FILE_UPLOAD', true); // message pour json_export :(

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
		if ($id_new = array_pop($actifs)
		AND $s = spip_query("SELECT fichier, taille, largeur, hauteur, extension, distant FROM spip_documents
			WHERE id_document="._q($id_new))
		AND $new = sql_fetch($s)) {
			define('FILE_UPLOAD', true); // message pour json_export :(

			// Une vignette doit rester une image
			if ($t['mode'] == 'vignette'
			AND !in_array($new['extension'], array('jpg', 'gif', 'png')))
				return false;

			// Maintenant on est bon, on recopie les nouvelles donnees
			// dans l'ancienne ligne spip_documents
			include_spip('inc/modifier');
			modifier_contenu('document', $id,
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


function colonne_table($table, $col) {
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

function table_where($table, $id)
{
	$nom_table = '';
	if (!(($tabref = &crayons_get_table($table, $nom_table))
			&& ($tabid = explode(',', $tabref['key']['PRIMARY KEY'])))) {
		spip_log('crayons: table ' . $table . ' inconnue');
		return array(false, false);
	}
	if (is_scalar($id)) {
		$id = explode('-', $id);
	}
	$where = $and = '';
	foreach ($id as $idcol => $idval) {
		$where .= $and . '`' . (is_int($idcol) ? trim($tabid[$idcol]) : $idcol) . '`=' . _q($idval);
		$and = ' AND ';
	}
	return array($nom_table, $where);
}
//	var_dump(colonne_table('forum', 'id_syndic')); die();

function valeur_colonne_table_dist($table, $col, $id) {
	list($nom_table, $where) = table_where($table, $id);
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
			'` FROM ' . $nom_table . ' WHERE ' . $where)
	AND $t = sql_fetch($s))
		$r = array_merge($r, $t);

	return $r;
}

function valeur_colonne_table($table, $col, $id) {
	if (!is_array($col))
		$col = array($col);

	if (function_exists($f = $table.'_valeur_colonne_table_dist')
	OR function_exists($f = $table.'_valeur_colonne_table')
	OR $f = 'valeur_colonne_table_dist')
		return $f($table, $col, $id);
}

/**
    * Transform a variable into its javascript equivalent (recursive)
    * @access private
    * @param mixed the variable
    * @return string js script | boolean false if error
    */
function var2js($var) {
    $asso = false;
    switch (true) {
        case is_null($var) :
            return 'null';
        case is_string($var) :
            return '"' . str_replace('&', '\x26', addcslashes($var, "\"\\\n\r")) . '"';
        case is_bool($var) :
            return $var ? 'true' : 'false';
        case is_scalar($var) :
            return $var;
        case is_object( $var) :
            $var = get_object_vars($var);
            $asso = true;
        case is_array($var) :
            $keys = array_keys($var);
            $ikey = count($keys);
            while (!$asso && $ikey--) {
                $asso = $ikey !== $keys[$ikey];
            }
            $sep = '';
            if ($asso) {
                $ret = '{';
                foreach ($var as $key => $elt) {
                    $ret .= $sep . '"' . $key . '":' . var2js($elt);
                    $sep = ',';
                }
                return $ret ."}\n";
            } else {
                $ret = '[';
                foreach ($var as $elt) {
                    $ret .= $sep . var2js($elt);
                    $sep = ',';
                }
                return $ret ."]\n";
            }
    }
    return false;
}

function json_export($var) {
	$var = var2js($var);

	// flag indiquant qu'on est en iframe et qu'il faut proteger nos
	// donnees dans un <textarea> ; attention $_FILES a ete vide par array_pop
	if (defined('FILE_UPLOAD'))
		return "<textarea>".htmlspecialchars($var)."</textarea>";
	else
		return $var;
}

function return_log($var) {
	die(json_export(array('$erreur'=> var_export($var,true))));
}

function _U($texte)
{
    include_spip('inc/charsets');
    return unicode2charset(html2unicode(_T($texte)));
}

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

function &crayons_get_table($table, &$nom_table) {
	static $catab = array('tables_principales',	'tables_auxiliaires');
	static $return = array();
	static $noms = array();
	if (!isset($return[$table])) {
		$return[$table] = $noms[$table] = '';
		include_spip('base/serial');
		include_spip('base/auxiliaires');
		include_spip('public/parametrer');
		$try = array('spip_'.table_objet($table), 'spip_' . $table . 's', $table . 's', 'spip_' . $table, $table);
		foreach ($catab as $i=>$categ) {
			foreach ($try as $nom) {
				if (isset($GLOBALS[$categ][$nom])) {
					$noms[$table] = $nom;
					$return[$table] = & $GLOBALS[$categ][$nom];
					break 2;
				}
			}
		}
	}
	$nom_table = $noms[$table];
	return $return[$table];
}

?>
