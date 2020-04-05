<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Consigner une phrase dans le journal de bord du site
 *
 * Cette fonction surcharge celle de SPIP, afin d'enregistrer les informations en base
 * Il est toutefois possible de surcharger ce comportement par défaut pour une action précise
 * en créant une fonction journal_FAIRE_dist($opt).
 *
 * @param string $phrase Une phrase humaine décrivant ce qu'on journalise (pour l'écriture en log)
 * @param array $opt Un tableau listant les informations plus détaillées, qui seront enregistrées en base
 * @return bool|int Retourne l'identifiant de journal inséré en base, sinon false
 */
function inc_journal_dist($phrase, $opt = array()) {
	// Dans un hit PHP, on va garder en mémoire ce qui a déjà été fait
	static $action = array();
	
	// Si c'est un robot ou s'il n'y a aucune phrase, on ne fait jamais rien
	if (_IS_BOT or !strlen($phrase)) {
		return false;
	}
	
	// On ajoute les précisions à la fin de la phrase de log
	if ($opt) {
		$phrase .= " :: ".str_replace("\n", ' ', join(', ',$opt));
	}
	
	// Garde-t-on aussi l'IP en mémoire ?
	if((lire_config('bigbrother/enregistrer_ip') == 'oui') and !$opt['infos']['ip']) {
		$opt['infos']['ip'] = $GLOBALS['ip'];
	}
	
	// On peut surcharger la fonction pour une action précise
	if($f = charger_fonction($opt['faire'], 'journal', true)) {
		$ok = $f($opt);
	}
	// Sinon par défaut on enregistre en base de données
	else {
		$champs = array(
			'id_auteur' => $opt['qui'],
			'action'    => $opt['faire'],
			'id_objet'  => $opt['id'],
			'objet'     => $opt['quoi'],
			'infos'     => $opt['infos'],
			'date'      => $opt['date'] ? $opt['date'] : date('Y-m-d H:i:s', time()),
		);
		// Envoyer aux plugins
		$champs = pipeline('pre_edition',
			array(
				'args' => array(
					'table' => 'spip_journal',
					'action' =>'inserer'
				),
				'data' => $champs
			)
		);
		
		// Maintenant que l'on sait exactement ce qui va être fait,
		// on enregistre en base que si ça n'a pas déjà été fait pour les mêmes arguments
		// et si ça a déjà été fait, on met plutôt à jour la ligne d'avant en fusionnant
		// le tableau des informations supplémentaires
		
		// Si on a pas déjà journalisé cette action dans CE hit PHP
		// ou si ça s'est mal passé (pas d'id_journal correct)
		// alors on réinsère une ligne de journal
		if (
			!isset($action[$champs['action']][$champs['objet']][$champs['id']]['id_journal'])
			or !intval($action[$champs['action']][$champs['objet']][$champs['id']]['id_journal'])
		) {
			$infos = $champs['infos'];
			
			// Si les infos sont vides, on fait chaine vide
			if (is_null($infos) or empty($infos)){
				$infos = '';
			}
			
			// On sérialise les infos supplémentaires pour la base
			if(is_array($champs['infos'])) {
				$champs['infos'] = serialize($infos);
			}
			
			// On insère
			$ok = sql_insertq(
				'spip_journal',
				$champs
			);
			
			// On garde en mémoire ce qui vient d'être journalisé dans ce hit PHP
			$action[$champs['action']][$champs['objet']][$champs['id']] = array('id_journal'=>$ok,'infos'=>$infos);
		}
		// Sinon on met à jour, et on fusionne les infos
		else {
			$id_journal = $action[$champs['action']][$champs['objet']][$champs['id']]['id_journal'];
			$infos = $action[$champs['action']][$champs['objet']][$champs['id']]['infos'];
			
			if (is_array($champs['infos']) and is_array($infos)) {
				// On fusionne les infos supplémentaires
				$infos = array_merge_recursive($infos, $champs['infos']);
				// Si les infos sont vides, on fait chaine vide
				if (empty($infos)){
					$infos = '';
				}
				// On sérialise pour la base
				$champs['infos'] = serialize($infos);
				// On met à jour la ligne en base
				sql_updateq(
					'spip_journal',
					$champs,
					'id_journal = '.intval($id_journal)
				);
				// On met à jour la variable statique
				$action[$champs['action']][$champs['objet']][$champs['id']] = array(
					'id_journal' => $id_journal,
					'infos'      => $infos
				);
			}
		}
	}
	
	// Dans tous les cas, on log dans un fichier
	include_spip('inc/filtres');
	spip_log(filtrer_entites($phrase), 'journal');
	
	return $ok;
}
