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

include_spip ('inc/navigation_modules');

function exec_action_activites()
{
	if (!autoriser('associer', 'activites')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		association_onglets(_T('asso:titre_onglet_activite'));
		// infos
		echo totauxinfos_intro('confirmation');
		// datation
		echo association_date_du_jour();
		echo fin_boite_info(true);
		echo association_retour();
		debut_cadre_association('activites.gif', 'activite_titre_inscriptions_activites');
		if (is_array($_REQUEST['delete'])) {
			$count = count($_REQUEST['delete']);
			echo '<p><strong>'._T('asso:activite_message_confirmation_supprimer',array('nombre' => $count, 'pluriel' => $count>1 ? 's' : '')).'</strong></<p>';
			$res = '';
			for ( $i=0 ; $i<$count ; $i++ ) {
				$id = $_REQUEST['delete'][$i];
				$res .= "<input type='hidden' name='drop[]' value='$id' checked='checked' />\n";
			}
			$res .= '<p class="boutons"><input type="submit" value="' . _T('asso:activite_bouton_confirmer') . '" class="fondo" /></p>';
			// count est du bruit de fond pour la secu
			echo generer_action_auteur('supprimer_activites', $count, $_REQUEST['url_retour'] ? $_REQUEST['url_retour'] : $_SERVER['HTTP_REFERER'], $res, " method='post'");
		}
		fin_page_association();
	}
}
?>
