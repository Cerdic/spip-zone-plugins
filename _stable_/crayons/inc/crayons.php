<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_PREG_CRAYON', ',crayon\b[^<>\'"]+?\b((\w+)-(\w+)-(\d+(?:-\w+)?))\b,');

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
	if (!$nom_table) {
		return false;
	}

    $s = spip_query(
        'SELECT `' . (is_array($col) ? implode($col, '`, `') : $col) .
         '` FROM ' . $nom_table . ' WHERE ' . $where);
    if ($t = spip_fetch_array($s)) {
        return is_array($col) ? $t : $t[$col];
    }
    return false;
}
function valeur_colonne_table($table, $col, $id) {
	if (function_exists($f = $table.'_valeur_colonne_table_dist')
	OR function_exists($f = $table.'_valeur_colonne_table')) {
		return $f($table, $col, $id);
	}
	return valeur_colonne_table_dist($table, $col, $id);
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
            return '"' . addcslashes($var, "\"\\\n\r") . '"';
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
		$try = array( 'spip_' . $table . 's', $table . 's', 'spip_' . $table, $table);
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
