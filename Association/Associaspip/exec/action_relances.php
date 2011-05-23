<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip ('inc/presentation');
include_spip ('inc/navigation_modules');
include_spip ('inc/mail');
//include_spip ('inc/charsets');

function exec_action_relances(){
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		
		$url_retour=$_POST['url_retour'];
		
		//On recupere les donnees globales

		$sujet = _request('sujet');
		$message = _request('message') ;
		$id_tab=(isset($_POST["id"])) ? $_POST["id"]:array();
		$statut_tab=(isset($_POST["statut"])) ? $_POST["statut"]:array();
		$count=count ($id_tab);

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		association_onglets();
		echo debut_gauche("",true);
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);
		echo association_retour();
		echo debut_droite("",true);
		echo recuperer_fond("prive/editer/relance_adherents");
		echo fin_page_association(); 
	}
} 
?>
