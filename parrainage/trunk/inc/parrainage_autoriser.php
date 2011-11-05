<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Juste pour l'appel du pipeline
function parrainage_autoriser(){}

// Ajouter un filleul en général : il faut avoir un compte
function autoriser_ajouterfilleul_dist($faire, $quoi, $id, $qui, $options){
	if ($qui['id_auteur'] > 0)
		return true;
	else
		return false;
}

// Ajouter un filleul à un auteur précis : il faut être cet auteur ou bien être admin
function autoriser_auteur_ajouterfilleul_dist($faire, $quoi, $id, $qui, $options){
	if ($qui['id_auteur'] == $id or $qui['statut'] <= '0minirezo')
		return true;
	else
		return false;
}

?>
