<?php
/*
 * Plugin amis / gestion des amis
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

/**
 * traitement de #FORMULAIRE_RECHERCHE_AMI
 * rien a faire, si ce n'est garder le formulaire affiche dans l'espace prive
 * 
 * @return array
 */
function formulaires_recherche_ami_traiter_dist(){
	
	$editable = false;
	if (test_espace_prive()) {
		$editable = true;
	}
	return array($editable,'');
}

?>