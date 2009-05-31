<?php

$GLOBALS['documents_auteurs_base_version'] = 0.01;

function documents_auteurs_verifier_base(){
	$version_base = $GLOBALS['documents_auteurs_base_version'];
	$current_version = 0.0;

	if (   (!isset($GLOBALS['meta']['documents_auteurs_base_version']) )
		|| (($current_version = $GLOBALS['meta']['documents_auteurs_base_version'])!=$version_base))

	if (   (isset($GLOBALS['meta']['documents_auteurs_base_version']) )
		&& (($current_version = $GLOBALS['meta']['documents_auteurs_base_version'])==$version_base))
		return;

	include_spip('base/documents_auteurs');

	if ($current_version==0.0){
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
		ecrire_meta('documents_auteurs_base_version',$current_version=$version_base);
	}
	ecrire_metas();
}

	function documents_auteurs_vider_tables() {
		spip_query("DROP TABLE spip_documents_auteurs");
		effacer_meta('documents_auteurs_base_version');
		ecrire_metas();
	}

	function documents_auteurs_install($action){
		$version_base = $GLOBALS['documents_auteurs_base_version'];
		switch ($action){
			case 'test':
 				return (isset($GLOBALS['meta']['documents_auteurs_base_version']) AND ($GLOBALS['meta']['documents_auteurs_base_version']>=$version_base));
				break;
			case 'install':
				documents_auteurs_verifier_base();
				break;
			case 'uninstall':
				documents_auteurs_vider_tables();
				break;
		}
	}
?>