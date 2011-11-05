<?php
/**
 * @name 		JavascriptPopup
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_POPUP_dist($p) {
	// Les arguments
	$p->code = "popup_balise(".popup_arguments_balise($p).')';
	$p->interdire_scripts = false;
	return $p;
}


function popup_balise($param='', $page=false, $width=false, $height=false, $title='') {
//	spipopup_config();
	if(!$width) $width = POPUP_WIDTH;
	if(!$height) $height = POPUP_HEIGHT;
	// Cas des objets SPIP
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
		if(!$page) $page = POPUP_SKEL;
//		$page = str_replace('.html','',$page);
		$url = generer_url_public($page, $param);
		$url_nopopup = generer_url_public($type,$param);
	// Cas d'une URL complete
	} elseif(
		substr($param, 0, strlen('http://'))=='http://' ||
		substr($param, 0, strlen('https://'))=='https://'
	) {
		$url = $url_nopopup = $param;
	// Sinon, on considere que c'est une erreur, on renvoie le param sans traitement
	} else {
		$url = generer_url_public($page, $param);
		$url_nopopup = generer_url_public($type,$param);
	}
	$_title = (strlen($title) ? $title." " : '')._T('spipopup:nouvelle_fenetre');
	return "$url_nopopup\" onclick=\"_popup_set('$url',$width,$height);return false;\" title=\"".$_title;
}

function popup_arguments_balise(&$p) {
	$i = 0; $args = array();
	while(($a = interprete_argument_balise(++$i,$p)) != NULL) $args[] = $a;
	return join(",", $args);
}

/**
 * Fonction pour transformer les liens dans la popup pour qu'ils renvoient vers l'opener et ferment la fenetre
 */
function popup_liens_retour($texte,$_popup='oui'){
	$popup = ($_popup=='non') ? false : true;
	if(!$popup) return $texte;
	$regs = $match = array();
	// pour chaque lien
	if (preg_match_all(_RACCOURCI_LIEN, $texte, $regs, PREG_SET_ORDER)) {	
		foreach ($regs as $reg) {
			$done = false;
			// si le lien est de type raccourcis "art40"
			if (preg_match(_RACCOURCI_URL, $reg[4], $match)) {
				if(in_array($match[1], array('art', 'article', 'breve', 'auteur'))){
					$type = ($match[1] == 'art') ? 'article' : $match[1];
					$lien_nouveau = popup_liens_retour_transformer_liens("id_$type=".$match[2], 'in');
					$done = true;
				}
			}
			// sinon
			if(!$done)
				$lien_nouveau = popup_liens_retour_transformer_liens($reg[4]);
			$lien = substr_replace($reg[0], $lien_nouveau, strpos($reg[0], '->')+2, strlen($reg[4]));
			$texte = str_replace($reg[0], $lien, $texte);
		}	
	}
	return $texte;
}

function popup_liens_retour_transformer_liens($lien, $type='out'){
	if(!strlen($lien)) return;
	if($type == 'out') {
		if(!substr_count($lien, 'http'))
			$lien = $GLOBALS['meta']['adresse_site'].'/?'.str_replace(array('?', '/'), '', $lien);
		$new_lien = texte_script('javascript:_goto("'.$lien.'", true, true);');
	}
	else
		$new_lien = generer_url_public(POPUP_SKEL, $lien);
	return $new_lien;
}

function popup_liens_retour_transformer_liens_ajax($url=''){
	if(!strlen($url)) return;
	$out = true;
	$rester_en_popup = array('article', 'auteur', 'breve');

	if(!substr_count($lien, 'http')) {
		foreach($rester_en_popup as $cache) {
			if(substr_count($lien, $cache)) $out = false;
			else $lien = $GLOBALS['meta']['adresse_site'].'/?'.str_replace(array('?', '/'), '', $lien);
		}
	}

	if($out)
		$new_lien = texte_script('javascript:_goto("'.$lien.'", true, true);');
	else
		$new_lien = generer_url_public(POPUP_SKEL, $lien);
	return $new_lien;
}
?>