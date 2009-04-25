<?php
// Renvoie la liste des auteurs ou des statuts autorises pour une action donnee
function definir_autorisations_tickets($action){
	$aut = NULL;
		
	switch(strtolower($action))
	{
		case 'ecrire':
			$define = (defined('_TICKETS_AUTORISATION_ECRIRE')) ? _TICKETS_AUTORISATION_ECRIRE : '0minirezo';
			break;
		case 'notifier':
			$define = (defined('_TICKETS_AUTORISATION_NOTIFIER')) ? _TICKETS_AUTORISATION_NOTIFIER : '1comite';
			break;
		case 'assigner':
			$define = (defined('_TICKETS_AUTORISATION_ASSIGNER')) ? _TICKETS_AUTORISATION_ASSIGNER : '0minirezo';
			break;
		case 'commenter':
			$define = (defined('_TICKETS_AUTORISATION_COMMENTER')) ? _TICKETS_AUTORISATION_COMMENTER : '1comite';
			break;
		default:
			$define = '0minirezo';
			break;
	}

	$liste = explode(':', $define);
	if (in_array('webmestre', $liste))
		$aut['auteur'] = explode(':', _ID_WEBMESTRES);
	else if (in_array('0minirezo', $liste))
		$aut['statut'] = array('0minirezo');
	else if (in_array('1comite', $liste))
		$aut['statut'] = array('0minirezo', '1comite');
	else
		$aut['auteur'] = $liste;
	
	return $aut;
}

// fonction pour le pipeline, n'a rien a effectuer
function tickets_autoriser(){}

// Autorisation de creation ou modification des tickets
function autoriser_ticket_ecrire($faire, $type, $id, $qui, $opt){
	$aut = FALSE;

	$liste = definir_autorisations_tickets('ecrire');
	if ($liste['statut'])
		$aut = in_array($qui['statut'], $liste['statut']);
	else if ($liste['auteur'])
		$aut = in_array($qui['id_auteur'], $liste['auteur']);
	
	return $aut;
}

// Autorisation de notification des tickets
function autoriser_ticket_assigner($faire, $type, $id, $qui, $opt){
	$aut = FALSE;

	$liste = definir_autorisations_tickets('assigner');
	if ($liste['statut'])
		$aut = in_array($qui['statut'], $liste['statut']);
	else if ($liste['auteur'])
		$aut = in_array($qui['id_auteur'], $liste['auteur']);
	
	return $aut;
}

// Autorisation de notification des tickets
function autoriser_ticket_commenter($faire, $type, $id, $qui, $opt){
	$aut = FALSE;

	$liste = definir_autorisations_tickets('commenter');
	if ($liste['statut'])
		$aut = in_array($qui['statut'], $liste['statut']);
	else if ($liste['auteur'])
		$aut = in_array($qui['id_auteur'], $liste['auteur']);
	
	return $aut;
}
?>
