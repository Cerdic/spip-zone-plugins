<?php

function icones_admin_base_ajouter_boutons($boutons_admin){
		  $boutons_admin['accueil']= new Bouton(
			'../'._DIR_PLUGINS.'_amelioration_admin_/icones_admin/base/img_pack/asuivre.png', 'icone_a_suivre');
		  $boutons_admin['naviguer']= new Bouton(
			'../'._DIR_PLUGINS.'_amelioration_admin_/icones_admin/base/img_pack/naviguer.png', 'icone_edition_site');
		  $boutons_admin['forum']= new Bouton(
			'../'._DIR_PLUGINS.'_amelioration_admin_/icones_admin/base/img_pack/forum.png', 'titre_forum');
		  $boutons_admin['auteurs']= new Bouton(
			'../'._DIR_PLUGINS.'_amelioration_admin_/icones_admin/base/img_pack/redacteurs-48.png', 'icone_auteurs');
		  $boutons_admin['statistiques_visites']= new Bouton(
			'../'._DIR_PLUGINS.'_amelioration_admin_/icones_admin/base/img_pack/statistiques-48.png', 'icone_statistiques_visites');
		  $boutons_admin['configuration']= new Bouton(
		  '../'._DIR_PLUGINS.'_amelioration_admin_/icones_admin/base/img_pack/administration-48.png', 'icone_configuration_site');
		  $boutons_admin['aide_index']= new Bouton(
		  '../'._DIR_PLUGINS.'_amelioration_admin_/icones_admin/base/img_pack/aide-48.png', 'icone_aide_ligne');
		  $boutons_admin['visiter']= new Bouton(
		  '../'._DIR_PLUGINS.'_amelioration_admin_/icones_admin/base/img_pack/visiter-48.png', 'icone_visiter_site');
		return $boutons_admin;
}


?>