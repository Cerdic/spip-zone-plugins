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

function oembed_output($args){
	if (!is_array($args))
		$args = unserialize($args);

	if (!$args
	  OR !isset($args['url'])
	  OR !$url = $args['url'])
		return "";

	include_spip('inc/url');
	define('_DEFINIR_CONTEXTE_TYPE_PAGE',true);
	list($fond,$contexte,$url_redirect) = urls_decoder_url($url,'',$args);
	if (!isset($contexte['type-page'])
	  OR !$type=$contexte['type-page'])
		return "";

	$res = "";
	// chercher le modele json si il existe
	if (trouver_fond($f="oembed/output/modeles/$type.json")){
		$res = trim(recuperer_fond($f,$contexte));
		if (isset($args['format']) AND $args['format']=='xml'){
			$res = json_decode($res,true);
			$output = charger_fonction("xml","oembed/output");
			$res = $output($res, false);
		}
	}

	return $res;
}
?>