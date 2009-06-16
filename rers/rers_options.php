<?php

	function rers_ajouter_boutons($boutons_admin) {
		// si on est admin

		if ($GLOBALS['connect_statut'] == "0minirezo") {
		  // on voit le bouton comme  sous-menu de "naviguer"
			$boutons_admin['naviguer']->sousmenu['cfg&cfg=rers']= new Bouton("plugin-24.gif", _T('Configuration plugin RERS') );
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
		<li><a href='?exec=naviguer&id_rubrique=$rers_rub_vie'>rubrique VIE</a></li>
		<li><a href='?exec=mots_tous'>DOMAINE DE SAVOIRS (classement des offres et demandes par domaines de savoirs)</a></li>
		</ul>
     		<ul style='padding-left:1em'>
		<li><a href='?exec=articles_page'>tous vos articles</a></li>
		</ul>
     		<ul style='padding-left:1em'>
		<li><a href='?exec=rers_aide'>Guide d'utilisation du site</a></li>
		<li><a href='?exec=message_edit&new=oui&dest=$rers_auteur_webmestre'>une question ? (message privé au webmaster)</a></li>
		</ul>

     		<ul style='padding-left:1em'>
		<li>vos informations personnelles : cliquez en haut sur votre nom</li>
		</ul>
		</div></div>";
	return $flux;





}



?>
