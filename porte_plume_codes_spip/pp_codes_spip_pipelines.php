<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function pp_codes_spip_porte_plume_barre_pre_charger($barres){
	// on ajoute les boutons dans la barre d'edition de SPIP
	foreach (array('edition') as $nom) {
		$barre = &$barres[$nom];

		$barre->ajouterPlusieursApres('grpCode', array(
			// Lien vers le Core 
			array(
				"id"          => 'lienCore',
				"name"        => _T('pp_codes_spip:outil_inserer_lien_core'),
				"className"   => 'outil_lien_core',
				"openWith"    => "[?",
				"closeWith"   => "#core]",
				"display"     => true,
				"dropMenu"    => array(

					// Lien vers les Plugins du Core 
					array(
						"id"          => 'lienCorePlugin',
						"name"        => _T('pp_codes_spip:outil_inserer_lien_plugins_core'),
						"className"   => 'outil_lien_plugins_core',
						"openWith"    => "[?",
						"closeWith"   => "#core_plugins]",
						"display"     => true,
					),

					// Lien vers les Plugins de la zone
					array(
						"id"          => 'lienZonePlugin',
						"name"        => _T('pp_codes_spip:outil_inserer_lien_plugins_zone'),
						"className"   => 'outil_lien_plugins_zone',
						"openWith"    => "[?",
						"closeWith"   => "#zone_plugins]",
						"display"     => true,
					),
				)
			),
		));
	}
	return $barres;
}



function pp_codes_spip_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(
		'outil_lien_core'=>'lien_spip_core-16.png',
		'outil_lien_plugins_core'=>'lien_spip_plugins_core-16.png',
		'outil_lien_plugins_zone'=>'lien_spip_plugins_zone-16.png',
	));
}
?>
