<?php
function formulaires_configurer_deplacer_rubriques_charger_dist(){
		$valeurs = array('rubriques_a_deplacer'=>'','rubrique_cible'=>'','dry_run'=>'','confirmation'=>_request('confirmation'));
		return $valeurs;
}

function formulaires_configurer_deplacer_rubriques_verifier_dist(){
		$erreurs = array();
		
		foreach(array('rubriques_a_deplacer','rubrique_cible') as $obligatoire) {
			if (!_request($obligatoire)) {
				$erreurs[$obligatoire] = 'Ce champ est obligatoire';
				$erreur_obligatoire = true;
			}
		}
		//on ne va pas plus loin si les champs obligatoires ne sont pas remplis
		if ($erreur_obligatoire)
			return $erreurs;
	   
		if (_request('dry_run')== 'oui') $erreurs['dry_run'] = 'oui';
			
		
		include_spip('base/abstract_sql');
		$rubrique_cible = intval(_request('rubrique_cible'));
		$rubriques_a_deplacer = explode(',' , _request('rubriques_a_deplacer'));
		

		// Vérifier que la rubrique cible existe
		if (!sql_fetsel('1', "spip_rubriques", "id_rubrique=$rubrique_cible"))
			$erreurs['rubrique_cible'] = _T('deplacer_rubrique:erreur_rubrique_cible_inexistante');
		
		// Vérifier que la rubrique cible n'est pas dans les rubriques à déplacer
		if (in_array($rubrique_cible, $rubriques_a_deplacer))
			$erreurs['rubrique_cible'] = _T('deplacer_rubrique:erreur_rubrique_cible_dans_rubriques_a_deplacer');

		foreach ($rubriques_a_deplacer as $key => $value) {
			if (!sql_fetsel('1', 'spip_rubriques', 'id_rubrique='.intval($value))){
				$erreurs['rubriques_a_deplacer'] = _T('deplacer_rubrique:erreur_rubrique_inexistante', array('id_rubrique', intval($value)));
			}
		}
		
		if (count($erreurs))
			$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
		return $erreurs;
}

function formulaires_configurer_deplacer_rubriques_traiter_dist(){
		return array('message_ok'=>'');
}