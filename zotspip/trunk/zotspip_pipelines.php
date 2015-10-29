<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function zotspip_insert_head($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/zotspip.css').'" type="text/css" />';
	$flux .= '<link rel="unapi-server" type="application/xml" title="unAPI" href="'.url_absolue(generer_url_public('zotspip_unapi','source=zotspip')).'" />';
	return $flux;
}

function zotspip_header_prive($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/zotspip.css').'" type="text/css" />';
	$flux .= '<link rel="unapi-server" type="application/xml" title="unAPI" href="'.url_absolue(generer_url_public('zotspip_unapi','source=zotspip')).'" />';
	return $flux;
}

function zotspip_jqueryui_forcer($scripts){
	$scripts[] = "jquery.ui.resizable";
	$scripts[] = "jquery.ui.sortable";
	return $scripts;
}

function zotspip_autoriser(){}

function autoriser_zotspip_menu_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre');
}

function autoriser_zitems_menu_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

function autoriser_zotero_modifier_dist($faire, $type, $id, $qui, $opt) {
	include_spip('inc/config');
	$config = lire_config('zotspip/autoriser_modif_zotero');
	if (!$config)
		return false;
	if ($config=='webmestre')
		return autoriser('webmestre');
	if ($config=='admin')
		return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
	if ($config=='admin_restreint')
		return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
	if ($config=='redacteur')
		return $qui['statut'] == '0minirezo' OR $qui['statut'] == '1comite';
	return false;
}

// Pour passer automatiquement les [ref=...] en notes de bas de page
function zotspip_pre_typo($texte) {
	$texte = preg_replace('#\[ref=(.*)\]#U','[[&#32;<ref|id=$1>]]',$texte);
	return $texte;
}

// Insertion du raccourci [ref=XXX] dans le porte-plume
function zotspip_porte_plume_barre_pre_charger($barres) {
	$barre = &$barres['edition'];
	
	$barre->ajouterApres('notes', array(
		"id"             => 'inserer_ref',
		"name"           => _T('zotspip:outil_inserer_ref'),
		"className"      => 'outil_inserer_ref',
		"selectionType"  => '',
		"closeWith"      => "[ref=[!["._T('zotspip:outil_explication_inserer_ref').' '._T('zotspip:outil_explication_inserer_ref_exemple')."]!]]",
		"display"        => true
	 ));
	
	return $barres;
}

// Icone pour le porte-plume
function zotspip_porte_plume_lien_classe_vers_icone($flux) {
	$icones = array();
	$icones['outil_inserer_ref'] = 'inserer_ref.png';
	return array_merge($flux, $icones);
}

// Pipeline post-boucle, utilise pour le champs annee de zitems, cf. http://core.spip.org/issues/2912

function zotspip_post_boucle ($boucle) {
	if ($boucle->id_table == 'zitems') {
		$boucle->where = replaceTree ('YEAR(zitems.date)','zitems.annee',$boucle->where);
	}
	return $boucle;
}

// Source: http://kvz.io/blog/2008/09/05/php-recursive-str-replace-replacetree/
function replaceTree($search="", $replace="", $array=false, $keys_too=false)
{
    if (!is_array($array)) {
        // Regular replace
        return str_replace($search, $replace, $array);
    }

    $newArr = array();
    foreach ($array as $k=>$v) {
        // Replace keys as well?
        $add_key = $k;
        if ($keys_too) {
            $add_key = str_replace($search, $replace, $k);
        }

        // Recurse
        $newArr[$add_key] = replaceTree($search, $replace, $v, $keys_too);
    }
    return $newArr;
}

?>
