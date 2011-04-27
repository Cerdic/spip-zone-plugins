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
	
function exec_edit_relances(){
		
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;

	$url_edit_labels = generer_url_ecrire('edit_labels');
	$url_retour = $_SERVER["HTTP_REFERER"];		

	association_onglets();
		
	echo debut_gauche("",true);
		
	echo debut_boite_info(true);
	echo association_date_du_jour();	
	echo fin_boite_info(true);
		
	$res=association_icone(_T('asso:bouton_impression'),  $url_edit_labels, "print-24.png");
			
	$res.=association_icone(_T('asso:bouton_retour'),  $url_retour, "retour-24.png");	
	echo bloc_des_raccourcis($res);
		
	echo debut_droite("",true);
		
	debut_cadre_relief(  "", false, "", $titre = _T('asso:tous_les_membres_a_relancer'));
		
	$statut_interne = _request('statut_interne');
	if (!$statut_interne) $statut_interne= "echu";

	$corps = '';
	foreach ($GLOBALS['association_liste_des_statuts'] as $var) {
			$corps .= '<option value="'.$var.'"';
			if ($statut_interne==$var) {$corps .= ' selected="selected"';}
			$corps .= '> '._T('asso:adherent_entete_statut_'.$var)."</option>\n";
	}

	if ($corps) {
		$corps = '<div><select name ="statut_interne" class="fondl" onchange="form.submit()">' . $corps . '</select></div>';
		echo generer_form_ecrire('edit_relances', $corps, 'method="get"', '');
	}
		
	$corps = relances_while($statut_interne);

	if ($corps) {

		$corps = '<fieldset>'
			. '<legend>Message de relance</legend>'
			. '<label for="sujet"><strong>'._T('asso:Sujet')
			. " :</strong></label>\n"
			. '<input name="sujet" type="text" value="'.stripslashes(_T('asso:titre_relance')).'" id="sujet" class="formo" />'
			. '<label for="message"><strong>'._T('asso:Message')
			. " :</strong></label>\n"
			. '<textarea name="message" rows="15" id="message" class="formo">'.stripslashes(_T('asso:message_relance'))."</textarea>\n"
			. '</fieldset>'
			. "\n<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n"
			. "<tr style='background-color: #DBE1C5;'>\n"
			. '<td><strong>'
			. _T('asso:adherent_libelle_id_auteur')
			. "</strong></td>\n"
			. '<th>' . _T('asso:nom') . "</th>\n"
			. '<th>' . _T('asso:telephone') . "</th>\n"
			. '<th>' . _T('asso:portable') . "</th>\n"
			. '<th>' . _T('asso:validite') . "</th>\n"
			. '<th>' . _T('asso:envoi') . "</th>\n"
			. '</tr>'
			.  $corps 
			. '</table>';

		$bouton = isset($action) ? _T('asso:bouton_'.$action) : _T('asso:bouton_envoyer');

		echo generer_form_ecrire('action_relances', $corps, '', $bouton);
	}
	fin_cadre_relief();  
	echo fin_page_association(); 
}

function relances_while($statut_interne)
{
	/* Cette requete recupere tous les membres qui ont un email dans la table spip_auteurs, a reprendre lors de l'interfacage avec Coordonnees car les emails peuvent alors etre uniquement dans spip_emails
 et ils peuvent etre plusieurs, il faudrait peut etre laisser la possibilite de choisir ou prendre la/les adresses email qui sont de toute facon recuperes dans action/modifier_relances.php, le JOIN sur la
table spip_auteurs permet d'afficher uniquement les membres qui ont un email dans cette table */
   $query = sql_select("a.id_auteur, a.nom_famille, a.prenom, a.telephone, a.mobile, a.statut_interne, a.validite", "spip_asso_membres a LEFT JOIN spip_auteurs b ON a.id_auteur=b.id_auteur", " b.email <> ''  AND a.statut_interne like '$statut_interne' AND a.statut_interne <> 'sorti'", '', "a.nom_famille" );

	$res = '';
	while ($data = sql_fetch($query)) {
		$id_auteur=$data['id_auteur'];
		$email=$data["email"];
		$class = $GLOBALS['association_styles_des_statuts'][$data['statut_interne']] . " border1";
		$res .= "\n<tr>"
		.'<td class="$class" style="text-align:right">'
		. $data['id']
		."</td>\n"
		.'<td class="$class">'.$data["nom_famille"].' '.$data['prenom']."</td>\n"
		.'<td class="$class">'.$data['telephone']."</td>\n"
		.'<td class="$class">'.$data['mobile']."</td>\n"
		.'<td class="$class">'.association_datefr($data['validite'])."</td>\n"
		.'<td class="$class" style="text-align:center;">'
		.'<input name="id[]" type="checkbox" value="'.$id_auteur.'" checked="checked" />'
		.'<input name="statut['.$id_auteur.']" type="hidden" value="'.$statut_interne.'" />'
		."</td>\n"
		.'</tr>';
	}
	return $res;
}
?>
