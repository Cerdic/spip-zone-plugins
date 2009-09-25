<?php
	$GLOBALS['panoramas_base_version'] = 0.1;
	function Panoramas_verifier_base(){
		$version_base = $GLOBALS['panoramas_base_version'];
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta']['panoramas_base_version']) )
				|| (($current_version = $GLOBALS['meta']['panoramas_base_version'])!=$version_base)){
			include_spip('base/panoramas_visites_virtuelles');
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				ecrire_meta('panoramas_base_version',$current_version=$version_base,'non');
			}
			ecrire_metas();
		}
		
		if (isset($GLOBALS['meta']['INDEX_elements_objet'])){
			$INDEX_elements_objet = unserialize($GLOBALS['meta']['INDEX_elements_objet']);
			if (!isset($INDEX_elements_objet['spip_visites_virtuelles'])){
				$INDEX_elements_objet['spip_visites_virtuelles'] = array('titre'=>8,'descriptif'=>4);
				ecrire_meta('INDEX_elements_objet',serialize($INDEX_elements_objet));
				ecrire_metas();
			}
		}
	}
	
	function Panoramas_vider_tables() {
		include_spip('base/panoramas_visites_virtuelles');
		include_spip('base/abstract_sql');
		spip_query("DROP TABLE spip_visites_virtuelles");
		spip_query("DROP TABLE spip_visites_virtuelles_lieux");
		spip_query("DROP TABLE spip_visites_virtuelles_interactions");
		effacer_meta('panoramas_base_version');
		ecrire_metas();
	}
	
	function Panoramas_install($action){
		$version_base = $GLOBALS['panoramas_base_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['panoramas_base_version']) AND ($GLOBALS['meta']['panoramas_base_version']>=$version_base)
				AND isset($GLOBALS['meta']['INDEX_elements_objet'])
				AND $t = unserialize($GLOBALS['meta']['INDEX_elements_objet'])
				AND isset($t['spip_visites_virtuelles']));
				break;
			case 'install':
				Panoramas_verifier_base();
				break;
			case 'uninstall':
				Panoramas_vider_tables();
				break;
		}
	}	
?>