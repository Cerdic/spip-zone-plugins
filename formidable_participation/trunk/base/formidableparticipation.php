<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function formidableparticipation_declarer_champs_extras($champs = array()) {
	$id_formulaires_reponse=  array(
      'saisie' => 'hidden',//Type du champ (voir plugin Saisies)
      'options' => array(
            'nom' => 'id_formulaires_reponse',
						'label' => 'id_formulaires_reponse',
            'sql' => "bigint(21)",
            'defaut' => '',// Valeur par défaut
            'restrictions'=>array('voir' => array('auteur' => ''),//Tout le monde peut voir
						'modifier' => array('auteur' => 'noone')),//
						'versionner' => true
      ),
		);
	$champs['spip_evenements_participants']['id_formulaires_reponse'] = $id_formulaires_reponse;
	return $champs;
}
