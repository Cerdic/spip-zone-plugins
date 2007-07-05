<?php
/**
* Plugin Abonnement
*
* Copyright (c) 2007
* BoOz booz@rezo.net 
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/

//version actuelle du plugin à changer en cas de maj
	$GLOBALS['abonnement_version'] = 0.1;
	
		function abonnement_verifier_base(){
		//install
		$version_base = $GLOBALS['abonnement_version'];
		
		// Comparaison de la version actuelle avec la version installée
		
if (  (!isset($GLOBALS['meta']['abonnement_version']) OR (($current_version = $GLOBALS['meta']['abonnement_version'])!=$version_base) ) ) {
				include_spip('base/abonnement');
				include_spip('base/abstract_sql');
				$desc = spip_abstract_showtable("spip_abonnements", '', true);
				if (!isset($desc['field']['id_abonnement'])){
					// Verifie que les tables spip_listes existent, sinon les creer
					spip_log('creation des tables spip_abonnements');
					echo "creation des tables spip_abonnements";
					include_spip('base/create');
					creer_base();
				}
			//autres maj
			
			ecrire_meta('abonnement_version',$current_version=$version_base,'non');
			}
		ecrire_metas();
		}
		
		
		function abonnement_vider_tables() {
		include_spip('base/abstract_sql');
		// suppression du champ evenements a la table spip_groupe_mots
		spip_query("DROP TABLE spip_abonnements");
		effacer_meta('abonnement_version');
		ecrire_metas();
	}
	
	function abonnement_install($action){
		$version_base = $GLOBALS['abonnement_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['abonnement_version']) 
				  AND ($GLOBALS['meta']['abonnement']>=$version_base));
				break;
			case 'install':
				abonnement_verifier_base();
				break;
			case 'uninstall':
				abonnement_vider_tables();
				break;
		}
	}
		

?>