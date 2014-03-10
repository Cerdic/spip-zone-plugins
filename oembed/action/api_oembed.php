<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function action_api_oembed_dist(){

	$args = array(
		'url' => $url = _request('url'),
		'maxheight' => _request('maxheight'),
		'maxwidth' => _request('maxwidth'),
		'format' => _request('format'),
		// support du jsonp: http://json-p.org/
		'callback_jsonp' => _request('callback_jsonp'),
	);

	$format = ($args['format']=='xml'?'xml':'json');

	$md5 = md5(serialize($args));
	$oembed_cache = sous_repertoire(_DIR_CACHE,substr($md5,0,1))."oe-".$md5.".".$format;

	// si cache oembed dispo et pas de recalcul demande, l'utiliser (perf issue)
	if (file_exists($oembed_cache)
		AND _VAR_MODE!=='recalcul'){
		lire_fichier($oembed_cache,$res);
	}
	else {
		include_spip('inc/urls');
		define('_DEFINIR_CONTEXTE_TYPE_PAGE',true);
		list($fond,$contexte,$url_redirect) = urls_decoder_url($url,'',$args);
		if (!isset($contexte['type-page'])
		  OR !$type=$contexte['type-page'])
			return "";

		$res = "";
		// chercher le modele json si il existe
		if (trouver_fond($f="oembed/output/modeles/$type.json")){
			$res = trim(recuperer_fond($f,$contexte));

			if ($format=='xml'){
				$res = json_decode($res,true);
				$output = charger_fonction("xml","oembed/output");
				$res = $output($res, false);
			}
		}
		ecrire_fichier($oembed_cache,$res);
	}

	if (!$res) {
		include_spip('inc/headers');
		http_status(404);
		echo "404 Not Found";
	}
	else {
		$content_type = ($format=='xml'?"text/xml":"application/json");
		header("Content-type: $content_type; charset=utf-8");
		echo $res;
	}

}