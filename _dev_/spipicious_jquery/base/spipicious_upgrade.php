<?php

	$GLOBALS['spipicious_base_version'] = 0.4;

	function spipicious_upgrade(){
		$version_base = $GLOBALS['spipicious_base_version'];
		$current_version = 0.0;
		if ((!isset($GLOBALS['meta']['spipicious_base_version']) )
				|| (($current_version = $GLOBALS['meta']['spipicious_base_version'])!=$version_base)){
			include_spip('base/spipicious');
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				ecrire_meta('spipicious_base_version',$current_version=$version_base,'non');
				echo "Installation des tables de spip.icio.us";
			}
			if($current_version<0.2){
				sql_alter("TABLE `spip_spipicious` ADD PRIMARY KEY (`id_mot`) ");
				sql_alter("TABLE `spip_spipicious` ADD KEY (`id_auteur`) ");	
				sql_alter("TABLE `spip_spipicious` ADD maj timestamp AFTER position ");
				echo "spipicious update @ 0.2<br/>";
				ecrire_meta('spipicious_base_version',$current_version=0.2,'non');
			}
			if($current_version<0.3){
				sql_alter("TABLE `spip_spipicious` ADD id_rubrique bigint(21) NOT NULL AFTER`id_article` ");
				sql_alter("TABLE `spip_spipicious` ADD id_document bigint(21) NOT NULL AFTER`id_rubrique` ");
				echo "spipicious update @ 0.3<br/>";
				ecrire_meta('spipicious_base_version',$current_version=0.3,'non');
			}
			if($current_version<0.4){
				$desc = sql_showtable("spip_spipicious", true);
				if (isset($desc['PRIMARY KEY']['id_mot'])){
					sql_alter("TABLE `spip_spipicious` DROP PRIMARY KEY (`id_mot`) ");
					sql_alter("TABLE `spip_spipicious` ADD KEY (`id_mot`) ");
				}
				$desc_mots_docs = sql_showtable("spip_mots_documents", true);
				if(!isset($desc_mots_docs['field']['id_mot'])){
					include_spip('base/create');
					include_spip('base/abstract_sql');
					creer_base();
				}
				echo "spipicious update @ 0.4<br/>";
				ecrire_meta('spipicious_base_version',$current_version=0.4,'non');
			}
			ecrire_metas();
		}
	}
	
	function spipicious_vider_tables() {
		sql_drop_table("spip_spipicious");
		effacer_meta('spipicious_base_version');
		ecrire_metas();
	}
	
	function spipicious_install($action){
		$version_base = $GLOBALS['spipicious_base_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['spipicious_base_version']) AND ($GLOBALS['meta']['spipicious_base_version']>=$version_base));
				break;
			case 'install':
				spipicious_upgrade();
				break;
			case 'uninstall':
				spipicious_vider_tables();
				break;
		}
	}
?>