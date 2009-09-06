<?php

	function rers_ajouter_boutons($boutons_admin) {
		// si on est admin
		$rers_rub_extraction = lire_config('rers/rers_rub_extraction');



		if ($GLOBALS['connect_statut'] == "0minirezo") {
		  // on voit le bouton comme  sous-menu de "naviguer"
			$boutons_admin['configuration']->sousmenu['cfg&cfg=rers']= new Bouton("plugin-24.gif", _T('Plugin RERS') );
			$boutons_admin['forum']->sousmenu['controle_forum&type=interne']= new Bouton("suivi-forum-24.gif", _T('Suivi des forums privés') );



		   if ($rers_rub_extraction) {
			$boutons_admin['naviguer']->sousmenu['naviguer&id_rubrique='.$rers_rub_extraction]= 
			  new Bouton("breve-24.gif", "Rubrique Extractions" );
		   }



		}
		return $boutons_admin;
	}
	


	function rers_affiche_aide($flux) {
	$rers_rub_offres = lire_config('rers/rers_rub_offres');
	$rers_rub_demandes = lire_config('rers/rers_rub_demandes');
	$rers_rub_vie = lire_config('rers/rers_rub_vie');
	$rers_auteur_webmestre = lire_config('rers/rers_auteur_webmestre');
	global $connect_id_auteur;

	$flux['data'] .= "<div class='cadre cadre-info verdana1'><div class='cadre_padding'>"
		. "<b>Raccourcis RERS&nbsp: </b>"
     		. "<ul style='padding-left:1em'>"
		. "<li><a href='?exec=naviguer&id_rubrique=$rers_rub_offres'>Rubrique OFFRES</a></li>"
		. "<li><a href='?exec=naviguer&id_rubrique=$rers_rub_demandes'>Rubrique DEMANDES</a></li>"
		. "<li><a href='?exec=naviguer&id_rubrique=$rers_rub_vie'>Rubrique VIE DU RERS</a></li>"
		. "<li><a href='?exec=mots_tous'>Recherche par domaine de savoir</a></li>"
		. "<li><a href='?exec=articles_page'>Tous vos articles</a></li>"
		. "<li><a href='"
		. generer_url_ecrire("auteur_infos","id_auteur=$connect_id_auteur")
		. "'>"
		. "Modifier vos informations personnelles"
		. "</a></li>" 
		. "<li><a href='"
		.find_in_path('aide_rers/guide_redacteur.pdf')
		."'>Guide du rédacteur (PDF)</a></li>"
		. "<li><a href='/?auteur$rers_auteur_webmestre'>Question au webmestre</a></li>"
		. "<li>Message privé à un adhérent&nbsp;: cliquer sur l'icône <img src='/prive/images/m_envoi.gif'/> à côté de son nom </li>"
		. "</ul>"
		. "</div></div>";
	return $flux;





}



?>
