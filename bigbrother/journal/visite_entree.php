<?php
/**
 * Fonction spécifique pour le journal concernant l'action visite_entree
 * @param array $opt les options
 */
function journal_visite_entree($opt){
	/**
	 * Si on a une date de fin, c'est une sortie
	 * On met à jour l'entrée du journal correspondante
	 */
	if(isset($opt['date_fin'])){
		$where = 'action='.sql_quote($opt['faire']).' AND id_auteur='.sql_quote($opt['qui']).'
			AND objet='.sql_quote($opt['quoi']).' AND id_objet='.$opt['id'].'
			AND date='.sql_quote($opt['date_debut']);

		$infos = unserialize(sql_getfetsel('infos', 'spip_journal',$where));
		$infos['date_fin'] = $opt['date_fin'];

		sql_updateq(
			'spip_journal',
			array(
				'infos' => serialize($infos)
			),
			$where
		);
	}
	/**
	 * Sinon on ajoute une entrée pour l'entrée sur la page de l'objet
	 */
	else{
		$champs = array(
				'id_auteur' => $opt['qui'],
				'action' => $opt['faire'],
				'id_objet' => $opt['id'],
				'objet' => $opt['quoi'],
				'infos' => $opt['infos'],
				'date' => $opt['date'] ? $opt['date'] : date('Y-m-d H:i:s', time())
			);
		// Envoyer aux plugins
		$champs = pipeline('pre_edition',
			array(
				'args' => array(
					'table' => 'spip_journal',
					'action'=>'inserer'
				),
				'data' => $champs
			)
		);
		if(is_array($champs['infos'])){
			$champs['infos'] = serialize($champs['infos']);
		}
		sql_insertq(
				'spip_journal',
				$champs
			);
	}
}

?>