<?php
/*
 * Plugin amis / gestion des amis
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

/**
 * Chargement par defaut des valeurs de saisie du #FORMULAIRE_RECHERCHE_AMI
 * la fonction recoit en entree les arguments de la balise dans le squelette
 * renvoyer la liste des champs en cle, et les valeurs par defaut a la saisie
 * les valeurs seront automatiquement surchargees par _request() en cas de second tour de saisie
 * renvoyer false pour ne pas autoriser la saisie
 * dans id renvoyer la cle primaire de l'objet traite si necessaire (sera mise a new sinon)
 * 
 * @return array
 */
function formulaires_recherche_ami_charger_dist(){
	
	$valeurs = array('recherche_ami'=>null);
	return $valeurs;
}

/**
 * traitement de #FORMULAIRE_RECHERCHE_AMI
 * rien a faire, si ce n'est garder le formulaire affiche dans l'espace prive
 * 
 * @return array
 */
function formulaires_recherche_ami_traiter_dist(){
	
	$editable = true;

	return array($editable,'');
}

?>