<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_echoppe(){
	include_spip('inc/commencer_page');
	if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page(_T('echoppe:echoppe'), "redacteurs", "echoppe");	
	}else{
		echo inc_commencer_page_dist(_T('echoppe:echoppe'), "redacteurs", "echoppe");
	}
	
	echo debut_gauche();
	
	
		echo debut_boite_info();
			echo (_T('echoppe:descriptif_echoppe'));
			/* A coder :
	
			1) Verif des droits
			2) Y a t il des categorie de creer ?
				-> non : on propose d'en creer une. Obligatoire.
				-> oui : on liste les categorie "racine".
			
			*/
		echo fin_boite_info();
		
		$raccourcis = icone_horizontale(_T('echoppe:nouvelle_categorie'), generer_url_ecrire("echoppe_categorie","new=oui"), _DIR_PLUGIN_ECHOPPE."images/categorie-24.png","creer.gif", false);
		echo bloc_des_raccourcis($raccourcis);
		
		
		/* A coder :
		1) Affichage des boutons de creation des outils
		*/
	
	echo creer_colonne_droite();
	echo debut_droite(_T('echoppe:echoppe'));
	echo gros_titre(_T("echoppe:echoppe"));
	
	/* A coder :
	
	1) Verif des droits
		-> OK : goto point 2
		-> NoOK : affichage d'une boite "Desole poulette, t'as pas les droits."
	2) Y a t il des categorie de creer ?
		-> non : on propose d'en creer une. Obligatoire.
		-> oui : on liste les categorie "racine".
	
	*/
	
	
	echo fin_gauche();
	echo fin_page();
}

?>
