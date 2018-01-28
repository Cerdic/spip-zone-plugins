<?php
/**
 * Crayons
 * plugin for spip
 * (c) Fil, toggg 2006-2013
 * licence GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Les fonctions de toggg pour faire du JSON

/**
 * Transform a variable into its javascript equivalent (recursive)
 *
 * @access private
 * @param mixed the variable
 * @return string|boolean
 *     - string : js script
 *     - false if error
 */
function crayons_var2js($var) {
	$asso = false;
	switch (true) {
		case is_null($var):
			return 'null';
		case is_string($var):
			return '"' .addcslashes($var, "\"\\\n\r\t/") . '"';
		case is_bool($var):
			return $var ? 'true' : 'false';
		case is_scalar($var):
			return (string)$var;
		case is_object($var):
			$var = get_object_vars($var);
			$asso = true;
		case is_array($var):
			$keys = array_keys($var);
			$ikey = count($keys);
			while (!$asso && $ikey--) {
				$asso = $ikey !== $keys[$ikey];
			}
			$sep = '';
			if ($asso) {
				$ret = '{';
				foreach ($var as $key => $elt) {
					$ret .= $sep . '"' . $key . '":' . crayons_var2js($elt);
					$sep = ',';
				}
				return $ret .'}';
			} else {
				$ret = '[';
				foreach ($var as $elt) {
					$ret .= $sep . crayons_var2js($elt);
					$sep = ',';
				}
				return $ret .']';
			}
	}
	return false;
}

// Un json_encode qui marche en iso (la spec JSON exige utf-8)
function crayons_json_encode($v) {
	if ($GLOBALS['meta']['charset'] == 'utf-8' and function_exists('json_encode')) {
		return json_encode($v);
	}

	$v = crayons_var2js($v);

	if ($GLOBALS['meta']['charset'] != 'utf-8') {
		include_spip('inc/charsets');
		$v = charset2unicode($v);
	}

	return $v;
}

// https://code.spip.net/@json_export
function crayons_json_export($var) {
	return crayons_json_encode($var);

/// Il semble que ce hack n'ait plus lieu d'etre avec jquery 1.5
/*
	// flag indiquant qu'on est en iframe et qu'il faut proteger nos
	// donnees dans un <textarea> ; attention $_FILES a ete vide par array_pop
	if (defined('FILE_UPLOAD'))
		return "<textarea>".htmlspecialchars($var)."</textarea>";
	else
		return $var;
*/
}
