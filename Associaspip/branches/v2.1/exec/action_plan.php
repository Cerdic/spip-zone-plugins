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

	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');

function exec_action_plan(){

	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

		$id_plan=intval(_request('id'));
		$code=$_POST['code'];
		$intitule=$_POST['intitule'];
		$classe=$_POST['classe'];
		$reference=$_POST['reference'];
		$solde_anterieur=$_POST['solde_anterieur'];
		$date_anterieure=$_POST['date_anterieure'];
		$actif=$_POST['actif'];
		$commentaire=$_POST['commentaire'];

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;

		association_onglets();

		echo debut_gauche('',true);

		echo debut_boite_info(true);
		echo association_date_du_jour();
		echo fin_boite_info(true);
		echo association_retour();
		echo debut_droite('',true);

		debut_cadre_relief(  "", false, "", $titre = _T('asso:suppression_de_compte'));
		echo '<p><strong>' . _T('asso:vous_vous_appretez_a_effacer_le_compte').$id_plan.'</strong></p>';

		$res .= '<p style="float:right;"><input type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo" /></p>';
		echo redirige_action_post('supprimer_plans', $id_plan, 'plan_comptable', '', $res);
		fin_cadre_relief();

		echo fin_page_association();
	}
}
?>
