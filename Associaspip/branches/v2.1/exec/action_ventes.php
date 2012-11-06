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

function exec_action_ventes() {
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'dons')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$delete_tab=(isset($_POST["delete"])) ? $_POST["delete"]:array();
		$count=count ($delete_tab);
			
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		association_onglets();
		echo debut_gauche("",true);
			
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);
			
		echo association_retour();
		
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  "", true, "", $titre = _T('asso:action_sur_les_ventes_associatives'));
		
		$res = '<div><strong>'
		  . _T('asso:vous_vous_appretez_a_effacer')
		  . " $count "
		  . (($count==1) ? _T('asso:vente') : _T('asso:ventes'))
		  . "</strong>\n";
			for ( $i=0 ; $i < $count ; $i++ ) {	
			$id = $delete_tab[$i];
			$res .= '<input type="hidden" name="drop[]" value="'.$id.'" checked="checked" />' . "\n";
		}
		$res .= '</div>'
		. '<div style="text-align:right"><input type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo" /></div>';	
			echo redirige_action_auteur('supprimer_ventes', $count, 'ventes', '', $res, '  method="post"');
			fin_cadre_relief();  
		echo fin_page_association();
	}
}
?>
