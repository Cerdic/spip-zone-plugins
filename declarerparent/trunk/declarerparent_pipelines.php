<?php
/**
 * Utilisations de pipelines par Déclarer parent
 *
 * @plugin     Déclarer parent
 * @copyright  2017
 * @author     nicod_
 * @licence    GNU/GPL
 * @package    SPIP\Declarerparent\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Definir la relation a l‘objet parent dans la declaration de l‘objet (en attendant https://core.spip.net/issues/3844)
 *
 * @param array $tables
 * @return array
 */
function declarerparent_declarer_tables_objets_sql($tables) {
	$tables['spip_articles']['parent']  = array('type' => 'rubrique', 'champ' => 'id_rubrique');
	$tables['spip_syndic']['parent']  	= array('type' => 'rubrique', 'champ' => 'id_rubrique');
	$tables['spip_rubriques']['parent'] = array('type' => 'rubrique', 'champ' => 'id_parent');
	$tables['spip_mots']['parent']      = array('type' => 'groupe_mot', 'champ' => 'id_groupe');
	
	$tables['spip_forum']['parent']     = array(
		array('condition' => 'id_parent=0', 'champ_type' => 'objet', 'champ' => 'id_objet', 'exclus' => array('forum')),
		array('condition' => 'id_parent>0', 'type' => 'forum', 'champ' => 'id_parent'),
	);

	return $tables;
}
