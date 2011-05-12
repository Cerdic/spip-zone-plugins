<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Une fonction qui retourne les différents statuts possibles pour une commande ou le nom d'un statut précis
function commandes_lister_statuts($statut=false){
	$statuts =  array(
		'encours' => _T('commandes:statut_encours'),
		'attente' => _T('commandes:statut_attente'),
		'partiel' => _T('commandes:statut_partiel'),
		'paye' => _T('commandes:statut_paye'),
		'envoye' => _T('commandes:statut_envoye'),
		'retour' => _T('commandes:statut_retour'),
		'retour_partiel' => _T('commandes:statut_retour_partiel'),
	);
	
	if ($statut and $nom = $statuts[$statut])
		return $nom;
	else
		return $statuts;
}

?>
