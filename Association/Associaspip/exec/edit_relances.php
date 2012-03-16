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


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip ('inc/navigation_modules');

function exec_edit_relances()
{
	association_onglets(_T('asso:titre_onglet_membres'));
	// notice
	echo _T('asso:aide_relances'); //!\ il faut en rajouter
	// datation
	echo association_date_du_jour();
	echo fin_boite_info(true);
	$res = association_icone('bouton_retour',  generer_url_ecrire('adherents'), 'retour-24.png');
	echo bloc_des_raccourcis($res);
	debut_cadre_association('ico_panier.png', 'tous_les_membres_a_relancer');
	$statut_interne = _request('statut_interne');
	if (!$statut_interne)
		$statut_interne = 'echu';
	$corps = '';
	foreach ($GLOBALS['association_liste_des_statuts'] as $var) {
		$corps .= '<option value="'.$var.'"';
		if ($statut_interne==$var) {
			$corps .= ' selected="selected"';
		}
		$corps .= '> '. _T('asso:adherent_entete_statut_'.$var) .'</option>';
	}
	if ($corps) {
		$corps = '<div><select name ="statut_interne" onchange="form.submit()">' . $corps . '</select></div>';
		echo generer_form_ecrire('edit_relances', $corps, 'method="get"', '');
	}
	$corps = relances_while($statut_interne);
	if ($corps) {
		$res = '<div class="formulaire_spip formulaire_edit_relance"><form>'
			. '<ul>'
			. '<li class="editer_sujet">'
			. '<label for="sujet">'. _T('asso:sujet') . '</label>'
			. '<input name="sujet" type="text" value="'.stripslashes(_T('asso:titre_relance')).'" id="sujet" class="text" />'
			. "</li>\n"
			. '<li class="editer_message">'
			. '<label for="message">'. _T('asso:message') . '</label>'
			. '<textarea name="message" rows="15" id="message">'.stripslashes(_T('asso:message_relance')).'</textarea>'
			. "</li>\n"
			. "</ul>\n"
			. "<table width='100%' class='asso_tablo' id='asso_tablo_ressources'>\n"
			. "<thead>\n<tr>"
			. '<th>'. _T('asso:entete_id') .'</th>'
			. '<th>' . _T('asso:entete_nom') .'</th>'
			. '<th>' . _T('asso:adherent_libelle_validite') .'</th>'
			. '<th>' . _T('asso:envoi') .'</th>'
			. "</tr>\n</thead><tbody>"
			.  $corps
			. "</tbody>\n</table>\n";
		$bouton = '<p class="boutons">'. isset($action) ? _T('asso:bouton_'.$action) : _T('asso:bouton_envoyer') .'</p>';
		echo generer_form_ecrire('action_relances', $res, '', $bouton);
	}
	fin_page_association();
}

function relances_while($statut_interne)
{
	$query = sql_select('id_auteur, sexe, nom_famille, prenom, statut_interne, validite', 'spip_asso_membres', " statut_interne like '$statut_interne' AND statut_interne <> 'sorti'", '', 'nom_famille');
	$res = '';
	$tr_class = 'pair';
	while ($data = sql_fetch($query)) {
		$res .= '<tr class="'.$tr_class.' '.$GLOBALS['association_styles_des_statuts'][$data['statut_interne']].'">'
		.'<td class="integer">'. $data['id_auteur'] .'</td>'
		.'<td class="text">'. association_calculer_nom_membre($data['sexe'], $data['prenom'], $data['nom_famille']) .'</td>'
		.'<td class="date">'. association_datefr($data['validite']) .'</td>'
		.'<td class="action"><input name="id[]" type="checkbox" value="'.$data['id_auteur'].'" checked="checked" /><input name="statut['.$data['id_auteur'].']" type="hidden" value="'.$data['statut_interne'].'" /></td>'
		."</tr>\n";
		$tr_class = ($tr_class=='pair')?'impair':'pair';
	}
	return $res;
}

?>