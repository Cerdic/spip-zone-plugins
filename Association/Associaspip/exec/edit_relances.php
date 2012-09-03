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
	if (!autoriser('editer_membres', 'association')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		onglets_association('titre_onglet_membres');
		// notice
		echo _T('asso:aide_relances');
		// datation et raccourcis
		raccourcis_association('adherents');
		$statut_interne = _request('statut_interne');
		if (!$statut_interne)
			$statut_interne = 'echu';
		$id_groupe = intval(_request('groupe'));
		debut_cadre_association('relance-24.png', 'tous_les_membres_a_relancer');
		// Filtres
		filtres_association(array(
			'groupe'=>$id_groupe,
			'statut'=>$statut_interne,
		), 'edit_relances');
		// MAILING
		$res = '<div class="formulaire_spip formulaire_editer_relances"><form>'
			// message (objet/titre et corps)
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
			// destinataires (liste des resultats de filtrage, a affiner en decochant les membres a exclure)
			. "<table width='100%' class='asso_tablo' id='asso_tablo_relances'>\n"
			. '<caption>'. _T('asso:membres') .'</caption>'
			. "<thead>\n<tr>"
			. '<th>'. _T('asso:entete_id') .'</th>'
			. '<th>' . _T('asso:entete_nom') .'</th>'
			. '<th>' . _T('asso:adherent_libelle_validite') .'</th>' // comme il s'agit initialement de faire des relances, cette information est rajoutee
			. '<th>' . _T('asso:envoi') .'</th>'
			. "</tr>\n</thead><tbody>"
			.  relances_while($statut_interne, $groupe)
			. "</tbody>\n</table>\n";
		$res .= '<p class="boutons"><input type="submit" value="'. ( isset($action) ? _T('asso:bouton_'.$action) : _T('asso:bouton_envoyer') ) .'" /></p>';
		echo generer_form_ecrire('relance_adherents', $res, '', '');
		fin_page_association();
	}
}

function relances_while($statut_interne, $id_groupe=0)
{
	$query = sql_select(
		'id_auteur, sexe, nom_famille, prenom, statut_interne, validite', // select
		'spip_asso_membres AS a_m'. ($id_groupe?' LEFT JOIN spip_asso_groupes_liaisons a_g_l ON a_m.id_auteur=a_g_l.id_auteur ':''), // from
		" statut_interne LIKE '$statut_interne' AND statut_interne <> 'sorti'". ($id_groupe?' AND id_groupe='.intval($id_groupe):''), //where
		'', // limit
		'nom_famille, prenom, validite' // order by
	);
	$res = '';
	while ($data = sql_fetch($query)) {
		$res .= '<tr class="'.$GLOBALS['association_styles_des_statuts'][$data['statut_interne']].'" id="'.$data['id_auteur'].'">'
		.'<td class="integer"><label for="mbr'.$data['id_auteur'].'">'.$data['id_auteur'].'</label></td>'
		.'<td class="text"><label for="mbr'.$data['id_auteur'].'">'. association_calculer_nom_membre($data['sexe'], $data['prenom'], $data['nom_famille']) .'</label></td>'
		.'<td class="date"><label for="mbr'.$data['id_auteur'].'">'. association_formater_date($data['validite']) .'</label></td>'
		.'<td class="action"><input name="id[]" type="checkbox" id="mbr'.$data['id_auteur'].'" value="'.$data['id_auteur'].'" checked="checked" /><input name="statut['.$data['id_auteur'].']" type="hidden" value="'.$data['statut_interne'].'" /></td>'
		."</tr>\n";
	}
	return $res;
}

?>