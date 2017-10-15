<?php

include_spip('inc/plugin');

// si le plugin coloration_code est activé, c'est lui qui prend la main sur le balisage
if (!defined('_DIR_PLUGIN_COLORATION_CODE')) {

	// Surcharge des fonctions de inc/texte_mini pour corriger les <code> générés par SPIP :
	// on utilise toujours <pre class="langage"><code>...</code></pre>
	// pour les raccourcis SPIP <code class="langage"> et <cadre class="langage">

	function traiter_echap_code($regs) {
		return precode_traiter_echap_code($regs);
	}

	function traiter_echap_cadre($regs) {
		return precode_traiter_echap_cadre($regs);
	}

}

// Echapper les <code>...</ code>
// http://code.spip.net/@traiter_echap_code_dist
function precode_traiter_echap_code($regs) {
	$attributs = $regs[2];
	$corps     = $regs[3];

	$code = spip_htmlspecialchars($corps); // il ne faut pas passer dans entites_html, ne pas transformer les &#xxx; du code !

	// ne pas mettre le <div...> s'il n'y a qu'une ligne
	if (is_int(strpos($code, "\n"))) {
		// supprimer les sauts de ligne debut/fin
		// (mais pas les espaces => ascii art).
		$code = preg_replace("/^[\n\r]+|[\n\r]+$/s", "", $code);
		$code = precode_balisage_code($attributs, $code);
	} else {
		$code = "<code$attributs>" . $code . "</code>";
	}

	$code = str_replace("\t", "&nbsp; &nbsp; &nbsp; &nbsp; ", $code);
	$code = str_replace("  ", " &nbsp;", $code);

	return $code;
}

// Echapper les <cadre>...</ cadre> aka <frame>...</ frame>
// http://code.spip.net/@traiter_echap_cadre_dist
function precode_traiter_echap_cadre($regs) {
	$attributs = $regs[2];
	$code      = trim(entites_html($regs[3]));

	return precode_balisage_code($attributs, $code);
}

function precode_balisage_code($attributs, $code) {
	return "<div class='precode'>"
		. "<pre dir='ltr' style='text-align: left;'$attributs><code>"
		. $code
		. "</code></pre>"
		. "</div>";
}

// définir spip_htmlspecialchars() pour SPIP 2

if (!function_exists('spip_htmlspecialchars')) {
	/**
	 * htmlspecialchars wrapper (PHP >= 5.4 compat issue)
	 *
	 * @param string $string
	 * @param int    $flags
	 * @param string $encoding
	 * @param bool   $double_encode
	 *
	 * @return string
	 */
	function spip_htmlspecialchars($string, $flags = null, $encoding = 'ISO-8859-1', $double_encode = true) {
		if (is_null($flags)) {
			$flags = ENT_COMPAT;
			if (defined('ENT_HTML401')) {
				$flags |= ENT_HTML401;
			}
		}

		if (PHP_VERSION_ID < 50203) {
			return htmlspecialchars($string, $flags, $encoding);
		}

		return htmlspecialchars($string, $flags, $encoding, $double_encode);
	}
}