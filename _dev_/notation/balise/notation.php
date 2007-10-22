<?php
/**
* Plugin Notation v.0.1
* par JEM (jean-marc.viglino@ign.fr)
* 
* Copyright (c) 2007
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Definition des balises (recuperer id_article courant dans le formulaire)
*  
**/

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite
global $auteur_session;

function balise_NOTATION ($p) {
	return calculer_balise_dynamique($p,'NOTATION', array('id_article'));
}

function balise_NOTATION_stat($args, $filtres) {
	return $args;
}

function balise_NOTATION_dyn($id_article=0, $auteur_session=array()) {
   return array(
       'formulaires/notation',
       0,
       array(
         'id_article'=>$id_article
       )
   );
}

?>