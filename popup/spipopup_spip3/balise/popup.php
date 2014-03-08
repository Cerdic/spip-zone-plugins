<?php
/**
 * @name 		JavascriptPopup_balise
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Balise popup : on appelle la balise dynamique en lui passant l'arbre recu
 */
function balise_POPUP_dist($p) {
	return calculer_balise_dynamique($p, 'POPUP', array());
}

/**
 * Balise popup dynamique
 */
function balise_POPUP_dyn($param='', $page=false, $width=false, $height=false, $title='', $_options=null) {
	// On charge la config
	spipopup_config();

	// Certaines valeurs par defaut
	if(!$width) $width = POPUP_WIDTH;
	if(!$height) $height = POPUP_HEIGHT;

	// Les options, dernier parametre, doivent etre passees sous la forme :
	// variable1:valeur1, variable2:valeur2, ...
	$options=array();
	if (is_string($_options)) {
		$opts_values = explode(',', $_options);
		if (count($opts_values))
		foreach ($opts_values as $_opts_v) {
			$_opts = explode(':', $_opts_v);
			if (count($_opts)==2)
				$options[$_opts[0]] = $_opts[1];
		}
	}
	if (defined('POPUP_OPTIONS') && is_string('POPUP_OPTIONS')) {
		$def_options=array();
		$def_opts_values = explode(',', POPUP_OPTIONS);
		if (count($def_opts_values))
		foreach ($def_opts_values as $def_opts_v) {
			$def_opts = explode(':', $def_opts_v);
			if (count($def_opts)==2)
				$def_options[$def_opts[0]] = $def_opts[1];
		}
		$options = array_merge($def_options, $options);
	}

	// Cas des objets SPIP
	include_spip('inc/lien');
	if (preg_match(_RACCOURCI_URL, $param, $match)) {

		if(in_array($match[1], array(
			'art', 'article', 'breve', 'auteur', 'syndic', 'mot', 'document', 'doc', 'site'
		))){
			$type = ($match[1] == 'art') ? 'article' : (
				($match[1] == 'site') ? 'syndic' : (
					($match[1] == 'doc') ? 'document' : $match[1]
				)
			);
			$param = "id_$type=".$match[2];
		}

		// Definition du squelette
		if(!$page) $page = POPUP_SKEL;
		$page = str_replace('.html','',$page);
		$url = generer_url_public($page, $param);
		$url_nopopup = generer_url_public($type,$param);

	// Cas d'une URL complete
	} elseif(
		substr($param, 0, strlen('http://'))=='http://' ||
		substr($param, 0, strlen('https://'))=='https://'
	) {
		$url = $url_nopopup = $param;

	// Sinon, on considère que c'est une erreur, on renvoie le param sans traitement
	} else {
		$url = generer_url_public($page, $param);
		$url_nopopup = generer_url_public($type,$param);

	}
	// Le titre du lien
	$_title = (strlen($title) ? $title." " : '')._T('spipopup:nouvelle_fenetre');

	// On reconstruit la chaine d'options a la JS
	$opts = '';
	foreach ($options as $opt_n => $opt_v) {
		if (is_bool($opt_v))
			$opt_v = ($opt_v==true) ? '1' : '0';
		$opts .= ( strlen($opts)>0 ? ', ' : '' )."$opt_n=$opt_v";
	}

	// On retourne 
	echo "$url_nopopup\" onclick=\"_popup_set('$url',$width,$height,1,'$opts');return false;\" title=\"".$_title;

}
?>