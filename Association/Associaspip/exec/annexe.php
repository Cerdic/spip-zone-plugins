<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  Ecrit par Marcel BOLLA en 09/2011                                      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/* TODO : ce module n'est pas implemente ! Il existe juste pour ne pas generer
 * une erreur lors de son activation dans les raccourcis de compte
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
	
function exec_annexe(){
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} 
	else {
		$plan = sql_countsel('spip_asso_plan');

		if (!($annee = _request('annee')))		{
			$annee = date('Y');		
		}
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(propre(_T('asso:titre_gestion_pour_association')), "", _DIR_PLUGIN_ASSOCIATION_ICONES.'finances.jpg','rien.gif');
		association_onglets(_T('asso:titre_onglet_comptes'));		

		echo debut_gauche("",true);		
		
		echo debut_boite_info(true);
		echo association_date_du_jour();

		echo fin_boite_info(true);

		$url_compte_resultat = generer_url_ecrire('compte_resultat', "annee=$annee");
		$url_bilan = generer_url_ecrire('bilan', "annee=$annee");
		$res = association_icone(_T('asso:cpte_resultat_titre_general') . " $annee",  $url_compte_resultat, 'finances.jpg')
		. association_icone(_T('asso:bilan') . " $annee",  $url_bilan, 'finances.jpg');
		
		echo bloc_des_raccourcis($res);

		echo debut_droite("",true);
		
		debut_cadre_relief(_DIR_PLUGIN_ASSOCIATION_ICONES."finances.jpg", false, "", '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .propre( _T('asso:annexe_titre_general'). ' - ' . $annee));
		
		echo _T('asso:non_implemente');

		fin_cadre_relief();  

		echo fin_page_association();
	}
}


?>
