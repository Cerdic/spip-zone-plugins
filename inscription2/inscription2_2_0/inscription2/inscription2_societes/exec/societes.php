<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_societes(){
	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	
	// L'icone de creation de societe
	$link=generer_url_ecrire('editer_societe', 'id_societe=new');
	$link=parametre_url($link,'retour',str_replace('&amp;', '&', self()));
	$icone = icone(_T("societe:ajouter_societe"), $link, "article-24.gif", "creer.gif");
	$contexte['icone'] = $icone;
	
	$commencer_page = charger_fonction('commencer_page','inc');
	echo $commencer_page(_T('societe:liste_societes'));
	
	echo gros_titre(_T('societe:liste_societes'),'',false);
	echo debut_gauche("societes",true);
	
	echo debut_boite_info(true);
	echo propre(_T('societe:info_page_liste'));	
	echo fin_boite_info(true);
	
	echo debut_droite("societes",true);
	echo recuperer_fond('prive/listes/societes',$contexte);

	echo fin_gauche(),fin_page();
}
?>