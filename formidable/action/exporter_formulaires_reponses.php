<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Exporter toutes les réponses d'un formulaire
 * @param unknown_type $arg
 * @return unknown_type
 */
function action_exporter_formulaires_reponses_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// on ne fait des choses seulements si le formulaire existe et qu'il a des enregistrements
	$ok = false;
	if (
		$id_formulaire = intval($arg)
		and $formulaire = sql_fetsel('*','spip_formulaires','id_formulaire = '.$id_formulaire)
		and $reponses = sql_allfetsel('*', 'spip_formulaires_reponses', 'id_formulaire = '.$id_formulaire.' and statut = '.sql_quote('publie'))
	) {
		$reponses_completes = array();
		// On parcourt chaque réponse
		foreach ($reponses as $reponse){
			// Est-ce qu'il y a un auteur avec un nom
			$nom_auteur = '';
			if ($id_auteur = intval($reponse['id_auteur'])){
				$nom_auteur = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur = '.$id_auteur);
			}
			if (!$nom_auteur) $nom_auteur = '';
			
			// Le début de la réponse avec les infos (date, auteur, etc)
			$reponse_complete = array($reponse['date'], $nom_auteur, $reponse['ip']);
			
			// Ensuite tous les champs
			include_spip('inc/saisies');
			$champs = saisies_lister_champs(unserialize($formulaire['saisies']), false);
			foreach ($champs as $nom){
				$valeur = sql_getfetsel(
					'valeur',
					'spip_formulaires_reponses_champs',
					'id_formulaires_reponse = '.intval($reponse['id_formulaires_reponse']).' and nom = '.sql_quote($nom)
				);
				if (is_array(unserialize($valeur)))
					$valeur = join(', ', unserialize($valeur));
				$reponse_complete[] = $valeur;
			}
			
			// On ajoute la ligne à l'ensemble des réponses
			$reponses_completes[] = $reponse_complete;
		}
		
		if ($reponses_completes and $exporter_csv = charger_fonction('exporter_csv', 'inc/', true)){
			echo $exporter_csv("export-form-$id_formulaire", $reponses_completes);
			exit();
		}
	}

	if (_request('redirect')) {
		$redirect = urldecode(_request('redirect'));
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
	else
		return $ok;
}

?>
