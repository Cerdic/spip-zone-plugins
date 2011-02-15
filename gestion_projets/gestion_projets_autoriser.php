<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function gestion_projets_autoriser(){}

// declarations d'autorisations
function autoriser_projet_editer_dist($faire, $type, $id, $id_auteur, $opt='') {


	$statut= $GLOBALS['visiteur_session']['statut'];	
	
	$projet= sql_fetsel('*','spip_projets','id_projet='.sql_quote($id));

	if($statut =='0minirezo' or $projet['id_chef_projet']==$qui) $retour=true;
	
	return $retour;

}

?>