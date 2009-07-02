<?php

	function rers_ajouter_boutons($boutons_admin) {
		// si on est admin

		if ($GLOBALS['connect_statut'] == "0minirezo") {
		  // on voit le bouton comme  sous-menu de "naviguer"
			$boutons_admin['configuration']->sousmenu['cfg&cfg=rers']= new Bouton("plugin-24.gif", _T('Plugin RERS') );
			$boutons_admin['forum']->sousmenu['controle_forum&type=interne']= new Bouton("suivi-forum-24.gif", _T('Suivi des forums privés') );
		}
		return $boutons_admin;
	}
	


function rers_affiche_aide($flux) {
$rers_rub_offres = lire_config('rers/rers_rub_offres');
$rers_rub_demandes = lire_config('rers/rers_rub_demandes');
$rers_rub_vie = lire_config('rers/rers_rub_vie');
$rers_auteur_webmestre = lire_config('rers/rers_auteur_webmestre');

$jcc = $GLOBALS['id_auteur'];


	$flux['data'] .= "<div class='cadre cadre-info verdana1'><div class='cadre_padding'>
		<b>Raccourcis RERS : $jcc</b>
     		<ul style='padding-left:1em'>
		<li><a href='?exec=naviguer&id_rubrique=$rers_rub_offres'>rubrique OFFRES</a></li>
		<li><a href='?exec=naviguer&id_rubrique=$rers_rub_demandes'>rubrique DEMANDES</a></li>
		<li><a href='?exec=naviguer&id_rubrique=$rers_rub_vie'>rubrique VIE DU RERS</a></li>
		<li><a href='?exec=mots_tous'>DOMAINE DE SAVOIRS (classement des offres et demandes par domaines de savoirs)</a></li>
		</ul>
     		<ul style='padding-left:1em'>
		<li><a href='?exec=articles_page'>tous vos articles</a></li>
		</ul>
     		<ul style='padding-left:1em'>
		<li><a href='?exec=rers_aide'>Aide</a></li>
		
		</ul>

     		<ul style='padding-left:1em'>
		<li>vos informations personnelles : cliquez en haut sur votre nom</li>
		</ul>
		</div></div>";
	return $flux;





}



?>
