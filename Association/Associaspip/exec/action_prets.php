<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010 Emmanuel Saint-James                     (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_action_prets(){
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'activites')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		$id_pret=intval($_REQUEST['id_pret']);
		$id_ressource=$_REQUEST['id_ressource']; // text !
		$id_emprunteur=$_POST['id_emprunteur']; // text !
		$date_sortie=$_POST['date_sortie'];
		$duree=$_POST['duree'];
		$date_retour=$_POST['date_retour'];
		$commentaire_sortie=$_POST['commentaire_sortie'];
		$commentaire_retour=$_POST['commentaire_retour'];
		$statut=$_POST['statut'];
		$montant=$_POST['montant'];
		$journal=$_POST['journal'];
		$imputation=$GLOBALS['association_metas']['pc_prets'];
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:prets_titre_suppression_prets')) ;
		association_onglets();
			
		echo debut_gauche("",true);
		echo debut_boite_info(true);
		echo association_date_du_jour();	
			
		$data = sql_fetsel("*", "spip_asso_ressources", "id_ressource=" . _q($id_ressource) ) ;
		$statut=$data['statut'];
		echo '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'._T('asso:ressources_num').'<br />';
		echo '<span class="spip_xx-large">'.$data['id_ressource'].'</span></div>';
		echo '<p>'._T('asso:ressources_libelle_code').': '.$data['code'].'<br />';
		echo $data['intitule'];
		echo '</p>';
		echo fin_boite_info(true);
		echo association_retour();
		echo debut_droite("",true);
		echo debut_cadre_relief(  "", true, "", $titre = _T('asso:prets_titre_suppression_prets'));
		echo '<p><strong>'._T('asso:prets_danger_suppression',array('id_pret' => $id_pret)).'</strong></p>';
			
		$res = '<div style="float:right;"><input type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo" /></div>';

		echo redirige_action_post('supprimer_prets', "$id_pret-$id_ressource", 'prets', '', $res);
			
		echo fin_cadre_relief(true);
		echo fin_page_association();
	}
}
?>
