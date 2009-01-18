<?php

// sur les envois html,
// compter les visites.
function stats_affichage_entetes_final($entetes){
	if ($GLOBALS['meta']["activer_statistiques"] != "non") {
		$html = preg_match(',^\s*text/html,', $entetes['Content-Type']);

		// decomptage des visites, on peut forcer a oui ou non avec le header X-Spip-Visites
		// par defaut on ne compte que les pages en html (ce qui exclue les js,css et flux rss)
		$spip_compter_visites = $html?'oui':'non';
		if (isset($page['entetes']['X-Spip-Visites'])){
			$spip_compter_visites = in_array($entetes['X-Spip-Visites'],array('oui','non'))
				?$entetes['entetes']['X-Spip-Visites']
				:$spip_compter_visites;
			unset($entetes['X-Spip-Visites']);
		}
			
		// Gestion des statistiques du site public
		
		if ($spip_compter_visites!='non') {
			$stats = charger_fonction('stats', 'public');
			$stats();
		}
	}
	return $entetes;
}


// contenus des pages exec
function stats_affiche_milieu($flux){
	// afficher le formulaire de configuration (activer ou desactiver les statistiques).
	if ($flux['args']['exec'] == 'config_fonctions') {
		$compteur = charger_fonction('compteur', 'configuration');
		$flux['data'] .= $compteur(); 
	}
	
	// afficher le formulaire de suppression des visites (configuration > maintenance du site).
	if ($flux['args']['exec'] == 'admin_effacer') {

		$res = generer_form_ecrire('delete_statistiques', "", '', _T('bouton_effacer_statistiques'));

		$flux['data'] .= 
			debut_cadre_trait_couleur('',true,'',_T('texte_effacer_statistiques'))
			. '<img src="' .  chemin_image('warning.gif') . '" alt="'
			. _T('info_avertissement')
			. "\" style='width: 48px; height: 48px; float: right;margin: 10px;' />"
			. _T('texte_admin_effacer_stats')
			. "<br class='nettoyeur' />"
			. "\n<div style='text-align: center'>"
			. "\n<div class='serif'>"
			. "\n<b>"._T('avis_suppression_base')."&nbsp;!</b>"
			. $res
			. "\n</div>"
			. "</div>"
			. fin_cadre_relief(true);	
		
	}
	return $flux;
}


// les taches crons
function stats_taches_generales_cron($taches_generales){

	// stats : toutes les 5 minutes on peut vider un panier de visites
	if ($GLOBALS['meta']["activer_statistiques"] == "oui") {
		$taches_generales['visites'] = 300; 
		$taches_generales['popularites'] = 7200; # calcul lourd
	}
		
	return $taches_generales;
}

function stats_configurer_liste_metas($metas){
	$metas['activer_statistiques']='non';
	$metas['activer_captures_referers']='non';
	return $metas;
}
?>
