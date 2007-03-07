<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_PREG_CRAYON', ',crayon\b[^<>\'"]+\b((\w+)-(\w+)-(\d+(?:-\w+)?))\b,');

define('MODIFIER_FORUMS', false);
define('MODIFIER_SIGNATURES', false);

// Modifier un forum ?
// = un super-admin (ici, pour tests de crayons forum)
if (MODIFIER_FORUMS AND !function_exists('autoriser_forum_modifier')) {
	function autoriser_forum_modifier($faire, $type, $id, $qui, $opt) {
		return
			$qui['statut'] == '0minirezo'
			AND !$qui['restreint'];
	}
}

// Modifier une signature ?
// = un super-admin (ici, pour tests de crayons forum)
if (MODIFIER_SIGNATURES AND !function_exists('autoriser_signature_modifier')) {
	function autoriser_signature_modifier($faire, $type, $id, $qui, $opt) {
		return
			$qui['statut'] == '0minirezo'
			AND !$qui['restreint'];
	}
}
function colonne_table($table, $col)
{
	static $catab = array(
		'tables_principales' => 'base/serial',
		'tables_auxiliaires' => 'base/auxiliaires',
	);
	$brut = '';
	foreach ($catab as $categ => $catinc) {
		include_spip($catinc);
		if (isset($GLOBALS[$categ]['spip_' . table_objet($table)]['field'][$col])) {
			$brut = $GLOBALS[$categ]['spip_' . table_objet($table)]['field'][$col];
			break;
		}
	}
	if (!$brut) {
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
					if (count($vir = explode(',', substr($mot, $pos, -1))) > 1) {
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

function valeur_colonne_table($table, $col, $id) {
	if (function_exists($f = $table.'_valeur_colonne_table_dist')
	OR function_exists($f = $table.'_valeur_colonne_table'))
		return $f($table, $col, $id);
	if (is_scalar($id)) {
		$where = id_table_objet($table) . '=' . $id;
	} else {
		$where = $and = '';
		foreach ($id as $col => $id) {
			$where .= $and . $col . '=' . $id;
			$and = ' AND';
		}
	}
    $s = spip_query(
        'SELECT ' . (is_array($col) ? implode($col, ', ') : $col) .
         ' FROM spip_' . table_objet($table) .
         ' WHERE ' . $where);
    if ($t = spip_fetch_array($s)) {
        return is_array($col) ? $t : $t[$col];
    }
    return false;
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
	foreach (array('msgNoChange' => false, 'msgAbandon' => true,
					'filet' => false, 'yellow_fade' => false)
			as $cfgi => $def) {
		$wdgcfg[$cfgi] = isset($php[$cfgi]) ? $php[$cfgi] :
			isset($metacrayons[$cfgi]) ? $metacrayons[$cfgi] : $def;
	}
	return $wdgcfg;
}
?>
