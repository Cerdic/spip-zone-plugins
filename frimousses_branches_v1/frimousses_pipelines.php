<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function frimousses_porte_plume_barre_pre_charger($barres) {
	// Commun aux 2 barres
	$frimousses = frimousses_smileys_uniques(frimousses_liste_smileys()[0]);
	$outil_frimousses = array();
	for ( $compteur=0; $compteur<count($frimousses[2]); $compteur++ ){
		$outil_frimousses[] = array(
			"id"          => "barre_frimousse$compteur",
			"name"        => _T('smileys:'.$frimousses[0][$compteur]).' '.$frimousses[0][$compteur],
			"className"   => "outil_frimousses$compteur", 
			"replaceWith" => ' '.$frimousses[0][$compteur].' ',
			"display"     => true,
		);
	}
	
	// On rajoute les boutons aussi bien pour l'édition du contenu que pour les forums
	foreach (array('edition', 'forum') as $nom) {
		$barre = &$barres[$nom];

		$module_barre = "barre_outils";
		if (intval($GLOBALS['spip_version_branche'])>2)
			$module_barre = "barreoutils";

		$smiley_par_defaut = ':-)';
		$barre->ajouterApres('grpCaracteres', array(
			"id"          => 'barre_frimousses',
			"name"        => _T("smileys:$smiley_par_defaut").' '.$smiley_par_defaut,
			"className"   => "outil_frimousses",
			"replaceWith" => " $smiley_par_defaut ",
			"display"     => true,
			"dropMenu"    => $outil_frimousses,
		));
	}
	return $barres;
}

function frimousses_porte_plume_lien_classe_vers_icone($flux) {
	$outils_frimousses["outil_frimousses"] = array(find_in_path('frimousses/mort_de_rire.png'), '0');	
	$frimousses = array_values(frimousses_smileys_uniques(frimousses_liste_smileys()[0])[2]);
        foreach($frimousses as $compteur => $file) {
            $outils_frimousses["outil_frimousses$compteur"] = array(find_in_path('frimousses/'.$file), '0');
	}
	return array_merge($flux, $outils_frimousses);
}

function frimousses_porte_plume_barre_charger($barres) {
	if (isset($barres['forum'])) {
		$barre = &$barres['forum'];
		$barre->afficher('barre_frimousses', 'barre_frimousse0', 'barre_frimousse1');
	}
	return $barres;
}
