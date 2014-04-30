<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Une fonction qui retourne les différents statuts possibles pour une commande ou le nom d'un statut précis
function commandes_lister_statuts($statut=false){
	$statuts =  array(
		'encours' => _T('commandes:statut_encours'),
		'erreur' => _T('commandes:statut_erreur'),
		'attente' => _T('commandes:statut_attente'),
		'partiel' => _T('commandes:statut_partiel'),
		'paye' => _T('commandes:statut_paye'),
		'envoye' => _T('commandes:statut_envoye'),
		'retour' => _T('commandes:statut_retour'),
		'retour_partiel' => _T('commandes:statut_retour_partiel'),
	);

	if ($statut and $nom = $statuts[$statut])
		return $nom;
	if ($statut) return $statut;
	else
		return $statuts;
}

// Qui est le premier webmester ?
function commandes_id_premier_webmestre()
{
	include_spip('base/abstract_sql');
	$query = sql_select("id_auteur","spip_auteurs","statut = '0minirezo' AND webmestre = 'oui'","","id_auteur");
	if ($row = sql_fetch($query)) {
		return( $row["id_auteur"] );
	}
	return false;
}


?>
