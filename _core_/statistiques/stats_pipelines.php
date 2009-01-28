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
		$flux['data'] .= recuperer_fond('prive/configurer/compteur',array());
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


// les boutons d'administration : ajouter les popularites et visites
function stats_formulaire_admin($flux) {
	if (
	 isset($flux['args']['contexte']['objet'])
	 AND $objet = $flux['args']['contexte']['objet']
	 AND isset($flux['args']['contexte']['id_objet'])
	 AND $id_objet = $flux['args']['contexte']['id_objet']
	 ) {
		if ($l = admin_stats($objet, $id_objet, $GLOBALS['var_preview'])) {
			$btn = recuperer_fond('prive/bouton/statistiques', array(
				'visites' => $l[0],
				'popularite' => $l[1],
				'statistiques' => $l[2],
			));
			$flux['data'] = preg_replace('%(<!--extra-->)%is', $btn.'$1', $flux['data']);				
		}
	}
	return $flux;
}

// calculer les visites et popularite d'un objet/id_objet
// (uniquement valable pour les articles) ...
// http://doc.spip.org/@admin_stats
function admin_stats($objet, $id_objet, $var_preview)
{
	if ($GLOBALS['meta']["activer_statistiques"] != "non" 
	AND $objet = 'article'
	AND !$var_preview
	AND autoriser('voirstats')
	) {
		$row = sql_fetsel("visites, popularite", "spip_articles", "id_article=$id_objet AND statut='publie'");

		if ($row) {
			return array(intval($row['visites']),
			       ceil($row['popularite']),
			       str_replace('&amp;', '&', generer_url_ecrire_statistiques($id_objet)));
		}
	}
	return false;
}

// http://doc.spip.org/@generer_url_ecrire_statistiques
function generer_url_ecrire_statistiques($id_article) {
	return generer_url_ecrire('statistiques_visites', "id_article=$id_article");
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
