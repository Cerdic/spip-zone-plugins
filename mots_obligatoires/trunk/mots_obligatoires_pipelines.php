<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function mots_obligatoires_formulaire_verifier($flux){
	$statut = _request ('statut');

	if ($flux['args']['form'] == 'instituer_objet' and $statut == 'publie'){// seulement si on publie
		$groupes_erreur = array();// stocker les groupes qui posent problème
		$objet = $flux['args']['args'][0];
		$id_objet = $flux['args']['args'][1];
		
		$info_plugin = chercher_filtre('info_plugin');
		$motus = $info_plugin('motus','est_actif');
		// Récupérer les groupes de mot clefs 
		// 1. Liés à cet objet 
		// 2. Marqués comme important
		// 3. Concernés par la rubrique en cours, si motus chargé
		$groupes = sql_select('id_groupe,titre','spip_groupes_mots',
			array("obligatoire=".sql_quote('oui'),
			"tables_liees LIKE ".sql_quote("$objet%")
		));
		$groupes_erreurs = array();
		while ($groupe = sql_fetch($groupes)){
			$id_groupe = $groupe['id_groupe'];
			if ($motus){// motus?
				$autorisation = autoriser('afficherselecteurmots','groupemots',$id_groupe,'',array('objet'=>$objet,'id_objet'=>$id_objet));
				if (!$autorisation){// si le groupe de mot clef courant ne peut pas être associé à l'objet courant?
					continue; // on passe au groupe de mots suivant
				}
			}
			$mots = sql_select("mots.id_mot",'spip_mots AS `mots` INNER JOIN spip_mots_liens AS L1 ON (L1.id_mot = mots.id_mot)',
				array(
					"L1.objet=".sql_quote($objet),
					"L1.id_objet=$id_objet",
					"mots.id_groupe=$id_groupe"
				)
			);
			if (sql_count($mots) < 1){
				$groupes_erreurs[] = $groupe['titre'];
			}
		}
		if (count($groupes_erreurs)>0){
			include_spip('inc/texte');
			set_request('statut',_request('statut_old'));
			if (count($groupes_erreurs)==1){
				$erreur = _T("mots_obligatoires:mot_manquant")."\n";
			}
			else{
				$erreur = _T("mots_obligatoires:mots_manquants")."\n";
			}
			foreach ($groupes_erreurs as $titre){
				$erreur .= "<br />- ".typo(supprimer_numero($titre))."\n";//on ne pas utiliser de <li> ici parce que instituer_objet.html entoure le message d'erreur de <p>
			}
			$flux['message_erreur'] = $erreur;
		}
	}
	return $flux;
}
