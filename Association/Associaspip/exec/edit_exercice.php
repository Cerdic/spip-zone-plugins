<?php

/* * *************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  ajouté en 11/2011 par Marcel BOLLA ... à partir de edit_categorie.php      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
  \************************************************************************** */


if (!defined("_ECRIRE_INC_VERSION"))
	return;

include_spip('inc/presentation');
include_spip('inc/navigation_modules');

function exec_edit_exercice() {

	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	}
	else {
		$id = intval(_request('id'));

		$data = !$id ? '' : sql_fetsel('*', 'spip_asso_exercices', "id_exercice=$id");
		if ($data) {
			$intitule = $data['intitule'];
			$commentaire = $data['commentaire'];
			$debut = $data['debut'];
			$fin = $data['fin'];
			$action = 'modifier';
		}
		else {
			$intitule = $commentaire = $debut = $fin = '';
			$action = 'ajouter';
		}

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:exercice_budgetaire_edition_titre'));
		association_onglets();

		echo debut_gauche('', true);

		echo debut_boite_info(true);
		echo association_date_du_jour();
		echo fin_boite_info(true);

		echo association_retour();

		echo debut_droite('', true);

		echo debut_cadre_relief('', false, '', _T('asso:exercice_budgetaire_titre'));

		$res = '<label for="intitule"><strong>' . _T('asso:exercice_intitule'). '</strong></label>'
			. '<input name="intitule" type="text" value="'.$intitule .'" id="intitule" class="formo" />'
			. '<label for="commentaire"><strong>' . _T('asso:exercice_commentaire').'</strong></label>'
			. '<input name="commentaire" type="text" value="'.$commentaire.'" id="commentaire" class="formo" />'
			. '<label for="debut"><strong>' . _T('asso:exercice_debut_aaaa').'</strong></label>'
			. '<input name="debut" type="text" value="'.$debut . '" id="debut" class="formo" />'
			. '<label for="fin"><strong>' . _T('asso:exercice_fin_aaaa'). '</strong></label>'
			. '<input name="fin" type="text" value="'. $fin . '" id="fin" class="formo" />'
			. '<p class="boutons">'
			. '<input name="submit" type="submit" value="'. _T('asso:bouton_envoyer'). '" class="fondo" /></p>';

		echo redirige_action_post($action . '_exercice', $id, 'exercices', '', "<div>$res</div>");
		echo fin_cadre_relief(true);
		echo fin_page_association();
	}
}

?>