<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// renvoyer un mim_type text/oembed pour les videos oembed
function mime_type_oembed($id_document) {
    if(!($id_document = intval($id_document)))
    	return '';
    $mime_type = sql_getfetsel('mime_type', 'spip_types_documents',
    	"extension IN (SELECT extension FROM spip_documents where id_document=$id_document)");
    if ($mime_type == 'text/html' AND sql_getfetsel('oembed', 'spip_documents',"id_document=$id_document"))
    	$mime_type = 'text/oembed';
    return $mime_type;
}

// balise #MIME_TYPE pour oembed
function balise_MIME_TYPE_dist($p) {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->id_boucle;
	$key = $p->boucles[$b]->primary;
	/**
	 * Si la clé est extension, on est dans une boucle sur la table spip_documents
	 */
	if($key == 'extension'){
		$p->code = champ_sql('mime_type', $p);
	}else{
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
function json_encode_html($texte){
	#$texte = json_encode($texte,JSON_HEX_TAG);
	$texte = json_encode($texte);
	$texte = str_replace(array("<",">"),array("\u003C","\u003E"),$texte);
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
function oembed($url, $maxwidth=0, $maxheight=0){
	include_spip('inc/oembed');
	if (oembed_verifier_provider($url)) {
		$fond = recuperer_fond(
			'modeles/oembed',
			array('url'=>$url,'lien'=>'','maxwidth'=>$maxwidth,'maxheight'=>$maxheight)
		);
		if ($fond = trim($fond))
			return $fond;
	}

	return $url;
}


function inc_ressource_dist($rac) {
	static $null_allowed = null;

	include_spip('inc/lien');
	$url = explode(' ', trim($rac, '<>'));
	$url = $url[0];

	$texte = null;
	# <http://url/absolue>
	if (preg_match(',^https?://,i', $url)){
		include_spip('inc/oembed');
		$lien = PtoBR(propre("[->".$url."]"));
		// null si pas embedable
		$texte = oembed_embarquer_lien($lien);
		if ($texte){
			$texte = "<html>$texte</html>";
		}
	}
	// compat SPIP < 3.0.14
	// sans le patch http://zone.spip.org/trac/spip-zone/changeset/79139/_core_/branches/spip-3.0/plugins/textwheel
	if (is_null($texte)) {
		if (is_null($null_allowed)){
			if (version_compare($GLOBALS['spip_version_branche'],"3.0.14","<"))
				$null_allowed = false;
			else
				$null_allowed = true;
		}

		if(!$null_allowed){
			include_spip('inc/lien');
			$url = explode(' ', trim($rac, '<>'));
			$url = $url[0];
			# <http://url/absolue>
			if (preg_match(',^https?://,i', $url))
				$texte = PtoBR(propre("<span class='ressource spip_out'>&lt;[->".$url."]&gt;</span>"));
			# <url/relative>
			elseif (false !== strpos($url, '/'))
				$texte = PtoBR(propre("<span class='ressource spip_in'>&lt;[->".$url."]&gt;</span>"));
			# <fichier.rtf>
			else {
				preg_match(',\.([^.]+)$,', $url, $regs);
				if (file_exists($f = _DIR_IMG.$regs[1].'/'.$url)) {
					$texte = PtoBR(propre("<span class='ressource spip_in'>&lt;[".$url."->".$f."]&gt;</span>"));
				} else {
					$texte = PtoBR(propre("<span class='ressource'>&lt;".$url."&gt;</span>"));
				}
			}

		}
	}

	return $texte;
}

