<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function lister_actions_multiples($objet='') {
	static $actions = null;

	if (is_null($actions)) {
		$actions = array(
			'prive/objets/liste/articles' => array(
				'objet' => 'articles',
				'nb_colonnes' => 5,
				'colonne_id' => 'id',
			),
			'prive/objets/liste/rubriques' => array(
				'objet' => 'rubriques',
				'nb_colonnes' => 5,
				'colonne_id' => 'id',
			),
			'prive/objets/liste/auteurs' => array(
				'objet' => 'auteurs',
				'nb_colonnes' => 5,
				'colonne_id' => 'id',
			),
		);
		$actions = pipeline('declarer_actions_multiples', $actions);
	}

	if ($objet)
		return isset($actions[$objet]) ? $actions[$objet] : array();
	else
		return $actions;

}
?>