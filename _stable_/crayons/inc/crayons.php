<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_PREG_CRAYON', ',crayon\b[^<>\'"]+\b((\w+)-(\w+)-(\d+))\b,');

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

function valeur_colonne_table($table, $col, $id) {
    $s = spip_query(
        'SELECT ' . (is_array($col) ? implode($col, ', ') : $col) .
         ' FROM spip_' . table_objet($table) .
         ' WHERE ' . id_table_objet($table) . '=' . $id);
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
	$prepare_wdgcfg = function_exists('crayons_config') ? crayons_config() : array();
	$wdgcfg = array();
	foreach (array('msgNoChange' => false, 'msgAbandon' => true)
				as $prepare_wdgcfgi => $def) {
		if (isset($prepare_wdgcfg[$prepare_wdgcfgi])) {
			$wdgcfg[$prepare_wdgcfgi] = $prepare_wdgcfg[$prepare_wdgcfgi];
		} else {
			$wdgcfg[$prepare_wdgcfgi] = $def;
		}
	}
	return $wdgcfg;
}
?>
