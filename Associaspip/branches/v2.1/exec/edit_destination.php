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

function exec_edit_destination(){

	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

		$url_asso = generer_url_ecrire('association');
		$url_destination = generer_url_ecrire('destination_comptable');
		$url_action_destination=generer_url_ecrire('action_destination');

		$id_destination= intval(_request('id'));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:edition_destination')) ;
		association_onglets();
		echo debut_gauche("",true);
		echo debut_boite_info(true);
		echo association_date_du_jour();
		echo fin_boite_info(true);
		echo association_retour();
		echo debut_droite("",true);

		debut_cadre_relief(  "", false, "", $titre = _T('asso:edition_destination'));

		$data = !$id_destination ? '' : sql_fetsel("*", "spip_asso_destination", "id_destination=$id_destination");
		if ($data) {
			$intitule=$data['intitule'];
			$commentaire=$data["commentaire"];
			$action = 'modifier';
		} else {
			$intitule=$commentaire='';
			$action = 'ajouter';
		}

		$res = '<label for="intitule"><strong>' . _T('asso:intitule') . '&nbsp;;</strong></label>'
		. '<input name="intitule" type="text" value="'
		. $intitule
		. '" id="intitule" class="formo" />'
		. '<label for="commentaire"><strong>' . _T('asso:commentaires') . '&nbsp;:</strong></label>'
		. '<textarea name="commentaire" id="commentaire" class="formo" rows="4" cols="80">'
		. $commentaire
		. "</textarea>\n"
		. '<div style="float:right;">'
		. '<input type="submit" value="'
		. _T('asso:bouton_envoyer')
		. '" class="fondo" /></div>';

		echo redirige_action_post($action . '_destinations' , $id_destination, 'destination_comptable', "", "<div>$res</div>");

		fin_cadre_relief();
		echo fin_gauche(), fin_page();
	}
}
?>
