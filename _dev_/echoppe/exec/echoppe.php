<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_echoppe(){
		
	
	
	echo debut_page(_T('echoppe:echoppe'), "redacteurs", "echoppe");
	echo debut_gauche();
		echo debut_boite_info();
			/* A coder :
	
			1) Verif des droits
			2) Y a t il des catégorie de créer ?
				-> non : on propose d'en créer une. Obligatoire.
				-> oui : on liste les categorie "racine".
			
			*/
		echo fin_boite_info();
		/* A coder :
		1) Affichage des boutons de création des outils
		*/
	
	echo creer_colonne_droite();
	echo debut_droite(_T('echoppe:echoppe'));
	echo gros_titre(_T("echoppe:echoppe"));
	
	/* A coder :
	
	1) Verif des droits
		-> OK : goto point 2
		-> NoOK : affichage d'une boite "Désolé poulette, t'as pas les droits."
	2) Y a t il des catégorie de créer ?
		-> non : on propose d'en créer une. Obligatoire.
		-> oui : on liste les categorie "racine".
	
	*/
	
	
	echo fin_gauche();
	echo fin_page();
}

?>
