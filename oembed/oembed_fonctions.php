<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// renvoyer un mim_type text/oembed pour les videos oembed
function mime_type_oembed($id_document) {
	if (!($id_document = intval($id_document))) {
		return '';
	}
	$mime_type = sql_getfetsel(
		'mime_type',
		'spip_types_documents',
		"extension IN (SELECT extension FROM spip_documents where id_document=$id_document)"
	);
	if ($mime_type == 'text/html'
		and sql_getfetsel('oembed', 'spip_documents', "id_document=$id_document")) {
		$mime_type = 'text/oembed';
	}
	return $mime_type;
}

// balise #MIME_TYPE pour oembed
function balise_MIME_TYPE_dist($p) {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->id_boucle;
	$key = $p->boucles[$b]->primary;
	/**
	 * Si la clé est extension, on est dans une boucle sur la table spip_documents
	 */
	if ($key == 'extension') {
		$p->code = champ_sql('mime_type', $p);
	} else {
		/* explorer la pile memoire pour atteindre le 'vrai' champ */
		$id_document = champ_sql('id_document', $p);
		/* le code php qui sera execute */
		$p->code = "mime_type_oembed(".$id_document.")";
	}
	return $p;
}

/**
 * un filtre pour json_encode avec les bonnes options, pour l'export json des modeles
 * @param $texte
 * @return string
 */
function json_encode_html($texte) {
	#$texte = json_encode($texte,JSON_HEX_TAG);
	$texte = json_encode($texte);
	$texte = str_replace(array('<', '>'), array('\u003C','\u003E'), $texte);
	return $texte;
}

/**
 * Filtre utilisable dans un squelette
 * |oembed{550,300}
 *
 * @param string $url
 * @param int $maxwidth
 * @param int $maxheight
 * @return string
 */
function oembed($url, $maxwidth = 0, $maxheight = 0) {
	include_spip('inc/oembed');
	if (oembed_verifier_provider($url)) {
		$fond = recuperer_fond(
			'modeles/oembed',
			array('url'=>$url, 'lien' => '', 'maxwidth' => $maxwidth, 'maxheight' => $maxheight)
		);
		if ($fond = trim($fond)) {
			return $fond;
		}
	}

	return $url;
}

/**
 * Modifier l'iframe d'une video pour la passer en autoplay
 * quand on l'injecte en async dans le html
 * @param $html
 * @return mixed
 */
function oembed_force_video_autoplay($html) {
	if ($e = extraire_balise($html, 'iframe')
		and $src = extraire_attribut($e, 'src')) {
		$src_amp = parametre_url($src, 'dummy', null);
		if (strpos($src, 'soundcloud') !== false) {
			$src_autoplay = parametre_url($src, 'auto_play', '1', '&');
		} else {
			$src_autoplay = parametre_url($src, 'autoplay', '1', '&');
		}
		/**
		 * Ne pas mettre d'autoplay sur les vidéos facebook sinon elles perdent le son
		 * Le son est plus compliqué à remettre qu'à recliquer une seconde fois pour voir la vidéo
		 */
		if (strpos($src, 'facebook') == false) {
			if (strpos($html, $src_amp)) {
				$html = str_replace($src_amp, $src_autoplay, $html);
			} else {
				$html = str_replace($src, $src_autoplay, $html);
			}
		}
	}
	return $html;
}

include_spip('inc/ressource');
if (function_exists('inc_ressource_dist')) {
	// SPIP 3.1
	function inc_ressource($rac) {
		$html = oembed_traiter_ressource($rac);
		if (is_null($html)) {
			$html = inc_ressource_dist($rac);
		}
		return $html;
	}
} else {
	// SPIP 3.0
	function inc_ressource_dist($rac) {
		return oembed_traiter_ressource($rac);
	}
}

function oembed_traiter_ressource($rac) {
	static $null_allowed = null;

	include_spip('inc/lien');
	$url = explode(' ', trim($rac, '<>'));
	$url = $url[0];

	$texte = null;
	# <http://url/absolue>
	if (preg_match(',^https?://,i', $url)) {
		include_spip('inc/oembed');
		$lien = PtoBR(propre('[->'.$url.']'));
		// null si pas embedable
		$texte = oembed_embarquer_lien($lien);
		if ($texte) {
			$texte = "<html>$texte</html>";
		}
	}
	// compat SPIP < 3.0.14
	// sans le patch https://zone.spip.org/trac/spip-zone/changeset/79139/_core_/branches/spip-3.0/plugins/textwheel
	if (is_null($texte)) {
		if (is_null($null_allowed)) {
			if (version_compare($GLOBALS['spip_version_branche'], '3.0.14', '<')) {
				$null_allowed = false;
			} else {
				$null_allowed = true;
			}
		}

		if (!$null_allowed) {
			include_spip('inc/lien');
			$url = explode(' ', trim($rac, '<>'));
			$url = $url[0];
			# <http://url/absolue>
			if (preg_match(',^https?://,i', $url)) {
				$texte = PtoBR(propre("<span class='ressource spip_out'>&lt;[->".$url.']&gt;</span>'));
			# <url/relative>
			} elseif (false !== strpos($url, '/')) {
				$texte = PtoBR(propre("<span class='ressource spip_in'>&lt;[->".$url.']&gt;</span>'));
			# <fichier.rtf>
			} else {
				preg_match(',\.([^.]+)$,', $url, $regs);
				if (file_exists($f = _DIR_IMG.$regs[1].'/'.$url)) {
					$texte = PtoBR(propre("<span class='ressource spip_in'>&lt;[".$url.'->'.$f.']&gt;</span>'));
				} else {
					$texte = PtoBR(propre("<span class='ressource'>&lt;".$url.'&gt;</span>'));
				}
			}
		}
	}
	return $texte;
}

/**
 * Securiser la vignette utilisee pour les videos oembed en mode async :
 * si c'est une image locale il faut
 * - en faire une copie dans local/ via image_reduire pour le cas ou acces_retreint
 * - appliquer url_absolue dessus car si on est sur une page avec url arbo le <base> ne s'appliquera pas dans le style inline
 * @param string $img
 * @return string
 */
function oembed_safe_thumbnail($img) {

	if (!tester_url_absolue($img) and file_exists($img)) {
		if (!function_exists('image_filtrer')) {
			include_spip('inc/filtres');
		}
		$img = image_filtrer(array('image_reduire', $img, 1200, 1200));
		$img = image_filtrer(array('image_graver', $img));
		$img = extraire_attribut($img, 'src');
		$img = url_absolue($img);
	}

	return $img;
}
