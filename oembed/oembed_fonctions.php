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
    /* explorer la pile memoire pour atteindre le 'vrai' champ */
    $id_document = champ_sql('id_document', $p);
    /* le code php qui sera execute */
    $p->code = "mime_type_oembed(".$id_document.")";
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

function oembed($url){
	if (oembed_verifier_provider($url)) {
		$fond = recuperer_fond('modeles/oembed',array('url'=>$url,'lien'=>''));
		if ($fond = trim($fond))
			return $fond;
	}

	return $url;
}

?>