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

function exec_edit_labels(){
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'adherents')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
		$url_asso = generer_url_ecrire('association');
		$url_edit_relances = generer_url_ecrire('edit_relances');		
		$indexation = $GLOBALS['association_metas']['indexation'];
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);	
		
		echo association_retour();
		
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  "", false, "", $titre = _T('asso:toutes_les_etiquettes_a_generer'));
		
		$statut_interne = _request('statut_interne');
		if (!$statut_interne) $statut_interne= "ok";



		$corps = '';
		foreach ($GLOBALS['association_liste_des_statuts2'] as $var) {
			$corps .= '<option value="'.$var.'"';
			if ($statut_interne==$var) {$corps .= ' selected="selected"';}
			$corps .= '> '._T('asso:adherent_entete_statut_'.$var)
			  ."</option>\n";
		}

		if ($corps) {
			$corps = '<div><select name ="statut_interne" class="fondl" onchange="form.submit()">' . $corps . '</select></div>';
			echo generer_form_ecrire('edit_labels', $corps, 'method="get"', '');
		}

		$corps = labels_adherents($indexation, $statut_interne);

		if ($corps) {
			$corps = "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n"
			."<tr style='background-color: #DBE1C5;'>\n"
			.'<td><strong>'
			. (($indexation=="id_asso")
			 ? _T('asso:adherent_libelle_id_asso')
			 : _T('asso:adherent_libelle_id_auteur'))
			.'</strong></td>'
			.'<th>' . _T('asso:nom') . '</th>'
			.'<th>' . _T('asso:adresse') . '</th>'
			.'<th>' . _T('asso:env') . '</th>'
			.'</tr>'
			. $corps
			.'</table>';

			echo generer_form_ecrire('action_labels', $corps, '', _T('asso:Etiquettes'));
		}
		fin_cadre_relief();  
		echo fin_page_association();
	}
}

function labels_adherents($indexation, $statut_interne)
{
	$query = sql_select("*",_ASSOCIATION_AUTEURS_ELARGIS, "statut_interne like '$statut_interne'", '', "nom_famille, sexe DESC" );
	// originale semblait contenir une vieillerie:
	//  spip_auteurs_elargis INNER JOIN spip_asso_adherents ON spip_auteurs_elargis.id_auteur=spip_asso_adherents.id_auteur 

	$res = '';
	if ($indexation !=="id_asso") $indexation = 'id_auteur'; // superflu ?
	while ($data = sql_fetch($query))  {
		$sexe=$data['sexe'];
		$id = $data[$indexation];
		switch($data['statut_interne']) {
			case "echu": $class= "impair"; break;
			case "ok": $class="valide"; break;
			case "relance": $class="pair"; break;
			case "prospect": $class="prospect"; break;	   
		}
			
		$res .= '<tr> ';
		$res .= '<td style="text-align:right;vertical-align:top;" class="'.$class. ' border1">';
		$res .= $id;
		$res .= '</td>';
		$res .= '<td style="vertical-align:top;" class="'.$class. ' border1">';
		if ($sexe=='H'){ $res .= 'M.'; }
		elseif ($sexe=='F'){ $res .= 'Mme'; }
		else { $res .= '&nbsp;'; }
		$res .= ' '.$data['prenom'].' '.$data["nom_famille"].'</td>';
		$res .= '<td style="vertical-align:top;" class="'.$class. ' border1">'.$data['adresse'].'<br />'.$data['code_postal'].' '.$data['ville'].'</td>';
		$res .= '<td style="text-align:center;vertical-align:top;" class="'.$class. ' border1">';
		$res .= '<input name="label[]" type="checkbox" value="'.$id.'" checked="checked" />';
		$res .= '</td>';
		$res .= "</tr>\n";
	}
	return $res;
}
?>
