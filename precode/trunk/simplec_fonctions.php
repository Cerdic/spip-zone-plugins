<?php

// Surcharge des fonctions de inc/texte_mini pour corriger les <code> générés par SPIP :
// on utilise toujours <pre class="langage"><code>...</code></pre>
// pour les raccourcis SPIP <code class="langage"> et <cadre class="langage">

// Echapper les <code>...</ code>
// http://code.spip.net/@traiter_echap_code_dist
function traiter_echap_code($regs) {
	$att = $regs[2];
	$corps = $regs[3];

	$echap = spip_htmlspecialchars($corps); // il ne faut pas passer dans entites_html, ne pas transformer les &#xxx; du code !

	// ne pas mettre le <div...> s'il n'y a qu'une ligne
	if (is_int(strpos($echap, "\n"))) {
		// supprimer les sauts de ligne debut/fin
		// (mais pas les espaces => ascii art).
		$echap = preg_replace("/^[\n\r]+|[\n\r]+$/s", "", $echap);
		$echap = "<pre dir='ltr' style='text-align: left;'$att><code>" . $echap . "</code></pre>";
	} else {
		$echap = "<code$att>" . $echap . "</code>";
	}

	$echap = str_replace("\t", "&nbsp; &nbsp; &nbsp; &nbsp; ", $echap);
	$echap = str_replace("  ", " &nbsp;", $echap);

	return $echap;
}

// Echapper les <cadre>...</ cadre> aka <frame>...</ frame>
// http://code.spip.net/@traiter_echap_cadre_dist
function traiter_echap_cadre($regs) {
	$att = $regs[2];
	$echap = trim(entites_html($regs[3]));
	$echap = "<pre dir='ltr' style='text-align: left;' $att><code>" . $echap . "</code></pre>";

	return $echap;
}

// définir spip_htmlspecialchars() pour SPIP 2

if(!function_exists('spip_htmlspecialchars')) {
	/**
	 * htmlspecialchars wrapper (PHP >= 5.4 compat issue)
	 *
	 * @param string $string
	 * @param int $flags
	 * @param string $encoding
	 * @param bool $double_encode
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