<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_edit_relances()
{
	if (!autoriser('relancer_membres', 'association')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		onglets_association('titre_onglet_membres', 'adherents');
		// notice
		echo _T('asso:aide_relances');
		// datation et raccourcis
		raccourcis_association('adherents');
		list($statut_interne, $critere) = association_passeparam_statut('interne', 'echu');
		$id_groupe = association_recuperer_entier('groupe');
		$num_relance = association_recuperer_entier('relance');
		debut_cadre_association('relance-24.png', 'tous_les_membres_a_relancer');
		// Filtres
		$filtre_relance = '<select name="relance" onchange="form.submit()">';
		$filtre_relance .= '<option value="" ';
		$filtre_relance .= (($num_relance==0 || $num_relance='')?' selected="selected"':'');
		$filtre_relance .= '>'. _T('asso:autre') .'</option>';
		$filtre_relance .= '<option value="1" ';
		$filtre_relance .= (($num_relance==1)?' selected="selected"':'');
		$filtre_relance .= '>'. _T('asso:relance') .'</option>';
		filtres_association(array(
			'groupe'=>$id_groupe,
			'statut'=>$statut_interne,
		), 'edit_relances', array(
			'relance'=>$filtre_relance,
		));
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
			.  relances_liste($critere, $groupe)
			. "</tbody>\n</table>\n";
		$res .= '<p class="boutons"><input type="submit" value="'. ( isset($action) ? _T('asso:bouton_'.$action) : _T('asso:bouton_envoyer') ) .'" /></p>';
		echo generer_form_ecrire('relance_adherents', $res, '', '');
		fin_page_association();
	}
}

/**
 * Liste des membres
 *
 * @param string $critere
 *   SQL de restriction selon statut
 * @param int $id_groupe
 *   Filtre groupe
 * @return string
 *   code HTML du tableau affichant la liste des membres en fonction des filtres
 *   actifs avec cases a cocher de selection
 */
function relances_liste($critere, $id_groupe=0)
{
	if ($id_groupe) {
		$critere .= " AND id_groupe=$id_groupe ";
		$jointure_groupe = ' LEFT JOIN spip_asso_groupes_liaisons a_g_l ON a_m.id_auteur=a_g_l.id_auteur ';
	} else {
		$jointure_groupe = '';
	}
	$query = sql_select(
		'id_auteur, sexe, nom_famille, prenom, statut_interne, validite', "spip_asso_membres AS a_m $jointure_groupe", $critere, '', 'nom_famille, prenom, validite' );
	$res = '';
	while ($data = sql_fetch($query)) {
		$res .= '<tr class="'.$GLOBALS['association_styles_des_statuts'][$data['statut_interne']].'" id="membre'.$data['id_auteur'].'">'
		.'<td class="integer"><label for="id'.$data['id_auteur'].'">'.$data['id_auteur'].'</label></td>'
		.'<td class="text"><label for="id'.$data['id_auteur'].'">'. association_formater_nom($data['sexe'], $data['prenom'], $data['nom_famille']) .'</label></td>'
		.'<td class="date"><label for="mbr'.$data['id_auteur'].'">'. association_formater_date($data['validite']) .'</label></td>'
		.'<td class="action">'. association_bouton_coch('id', $data['id_auteur'], '<input name="statut['.$data['id_auteur'].']" type="hidden" value="'.$data['statut_interne'].'" />')
		."</tr>\n";
	}
	return $res;
}

?>