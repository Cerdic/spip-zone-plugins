<?

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function mots_obligatoires_formulaire_verifier($flux){
	$statut = _request ('statut');
	if ($flux['args']['form'] == 'instituer_objet' and $statut == 'publie'){// seulement si on publie
		$objet = $flux['args']['args'][0];
		$id_objet = $flux['args']['args'][1];
		
		// Récupérer les groupes de mot clefs 
		// 1. Liés à cet objet 
		// 2. Marqués comme important
		$groupes = sql_select('id_groupe,titre','spip_groupes_mots',
			array("obligatoire=".sql_quote('oui'),
			"tables_liees LIKE ".sql_quote("$objet%")
		));
		while ($groupe = sql_fetch($groupes)){
			$id_groupe = $groupe['id_groupe'];
			$mots = sql_select("mots.id_mot",'spip_mots AS `mots` INNER JOIN spip_mots_liens AS L1 ON (L1.id_mot = mots.id_mot)',
				array(
					"L1.objet=".sql_quote($objet),
					"L1.id_objet=$id_objet",
					"mots.id_groupe=$id_groupe"
				)
			);
			if (sql_count ($mots) < 1){
				pass; // a completer ici pour remplir les erreurs
			}
		}
	}
	return $flux;
}
