<?php
$GLOBALS['spip_thelia_base_version'] = 0.1;
function spip_thelia_verifier_base(){
	$version_base = $GLOBALS['spip_thelia_base_version'];
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta']['spip_thelia_base_version']))
		|| (($current_version = $GLOBALS['meta']['spip_thelia_base_version'])!=$version_base)
	){
		include_spip('base/spip_thelia_produits_associes');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta('spip_thelia_base_version', $current_version = $version_base, 'non');
		}
		ecrire_metas();
	}
	if (isset($GLOBALS['meta']['INDEX_elements_objet'])){
		$INDEX_elements_objet = unserialize($GLOBALS['meta']['INDEX_elements_objet']);
		if (!isset($INDEX_elements_objet['spip_produits_articles'])){
			$INDEX_elements_objet['spip_produits_articles'] = array('id_produit' => 8, 'id_article' => 4);
			ecrire_meta('INDEX_elements_objet', serialize($INDEX_elements_objet));
			ecrire_metas();
		}
	}
}

function spip_thelia_vider_tables(){
	include_spip('base/spip_thelia_produits_associes');
	include_spip('base/abstract_sql');
	spip_query("DROP TABLE spip_produits_articles");
	spip_query("DROP TABLE spip_produits_rubriques");
	spip_query("DROP TABLE spip_rubriquesthelia_rubriques");
	spip_query("DROP TABLE spip_rubriquesthelia_rubriques");
	effacer_meta('spip_thelia_base_version');
	ecrire_metas();
}

function spip_thelia_install($action){
	include_spip('inc/meta');
	include_spip('base/create');
	$version_base = $GLOBALS['spip_thelia_base_version'];
	switch ($action) {
		case 'test':
			return (isset($GLOBALS['meta']['spip_thelia_base_version']) AND ($GLOBALS['meta']['spip_thelia_base_version']>=$version_base));
			break;
		case 'install':
			spip_thelia_verifier_base();
			break;
		case 'uninstall':
			spip_thelia_vider_tables();
			break;
	}
}

