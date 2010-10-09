<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/*
 * Consigner une phrase dans le journal de bord du site
 * Cette API travaille a minima, mais un plugin pourra stocker
 * ces journaux en base et fournir des outils d'affichage, de selection etc
 *
 * @param string $journal
 * @param array $opt
 */
function inc_journal_dist($phrase, $opt = array()) {
	if (_IS_BOT)
		return;

	if (!strlen($phrase))
		return;
	if ($opt)
		$phrase .= " :: ".str_replace("\n", ' ', join(', ',$opt));

	/**
	 * Journalise t on en base ou pas
	 * @var unknown_type
	 */
	$config = unserialize($GLOBALS['meta']['bigbrother']);
	if(isset($config[$opt['faire']]) && ($config[$opt['faire']] == 'oui')){
		if((lire_config('bigbrother/enregistrer_ip') == 'oui') && !$opt['infos']['ip']){
			$opt['infos']['ip'] = $GLOBALS['ip'];
		}
		if($f = charger_fonction($opt['faire'],'journal',true)){
			$f($opt);
		}else{
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
	include_spip('inc/filtres');
	spip_log(filtrer_entites($phrase), 'journal');
}

?>