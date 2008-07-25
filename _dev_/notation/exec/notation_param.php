<?php
/**
* Plugin Notation
* par JEM (jean-marc.viglino@ign.fr) / b_b
* 
* Copyright (c) 2008
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Affichage de l'aide
*  
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/vieilles_defs');
include_spip('inc/presentation');
include_spip('inc/notation_menu');
include_spip('inc/config');
include_spip('inc/notation_util');

function exec_notation_param(){
	// Modifications demandees
 	if (isset($_POST['modifier'])){
		if (intval($_POST['ponderation']) != notation_get_ponderation()) $recalc = true;
	    ecrire_config('notation/ponderation',$_POST['ponderation']);
	    ecrire_config('notation/acces',$_POST['acces']);
	    ecrire_config('notation/change_note',$_POST['change_note']);
	    ecrire_config('notation/nombre',$_POST['nombre']);
 	}

	// les parametres
	$ponderation = notation_get_ponderation();
	$acces = notation_get_acces();
	$nb_note = notation_get_nb_notes();
	$change_note = lire_config('notation/change_note');
	
	// Recalculer les notes
	if ($recalc){
		$res = sql_select(
			"spip_notations_objets.id_article,spip_notations_objets.id_forum,spip_notations_objets.note,spip_notations_objets.nombre_votes",
			"spip_notations_objets",
			""
			);
		while ($row =spip_fetch_array($res)){
			$note_ponderee = notation_ponderee($row['note'],$row['nombre_votes']);
			sql_update("spip_notations_objets", array("note_ponderee" => $note_ponderee), "id_article=" . sql_quote($row['id_article']) . " AND id_forum=" .sql_quote($row['id_forum']));
		}
	}
	// Afficher les menus
	ecrire_menu("param");

 	global $connect_statut, $connect_toutes_rubriques, $couleur_foncee, $couleur_claire;
	if ($GLOBALS['connect_statut'] == "0minirezo" AND $connect_toutes_rubriques){
		
		echo generer_url_post_ecrire("notation_param").'<div>';
		
		/* Ponderation */
		debut_cadre_trait_couleur("../"._DIR_PLUGIN_NOTATION."img_pack/poids.gif", false, "", _T('notation:ponderation'));
		debut_cadre_relief();
		echo("<span class='verdana2'>"._T("notation:info_ponderation")."</span>");
		fin_cadre_relief();
		echo "<table><tr><td>";
		echo _T('notation:valeur_ponderation');
		echo " : <input type='text' name='ponderation' class='fondl' value=\"$ponderation\" size='8' />";
		echo "<input type='submit' name='modifier' class='fondo' style='margin-left:1em;' value='"._T('bouton_valider')."' />";
		echo "</td></tr></table>";
		echo "<p class='verdana2'>"._T('notation:exemple',array('ponderation' => $ponderation))."</p>";
		echo "<table class='spip' cellspacing='1' style='border:1px solid ".$couleur_foncee."; text-align:center;'><tr style='background-color:".$couleur_claire.";'>";
		echo "<td>"._T("notation:nbvotes")."</td><td>1</td><td>10</td><td>25</td><td>50</td><td>100</td><td>150</td><td>200</td>";
		echo "</tr><tr>";
		echo "<td>"._T("notation:note")."</td><td>5</td><td>5</td><td>5</td><td>5</td><td>5</td><td>5</td><td>5</td>";
		echo "</tr><tr style='background-color:".$couleur_claire.";'>";
		echo "<td>"._T("notation:note_pond")."</td><td>".notation_ponderee(5,1)."</td><td>".notation_ponderee(5,10)."</td><td>".notation_ponderee(5,25).
		"</td><td>".notation_ponderee(5,50)."</td><td>".notation_ponderee(5,100)."</td><td>".notation_ponderee(5,150)."</td><td>".notation_ponderee(5,200)."</td>";
		echo "</tr></table>";
		fin_cadre_trait_couleur();
		
		/* Acessibilite */
		debut_cadre_trait_couleur("redacteurs-24.gif", false, "", _T('notation:acces'));
		echo '<table style="width:100%"><tr><td colspan="2">';
		echo _T("notation:info_acces");
		echo '</td></tr><tr><td style="width:50%">';
		echo afficher_choix('acces', $acces,
		  array( 'adm' => _T('notation:item_adm'),
			 'aut' => _T('notation:item_aut'),
			 'ide' => _T('notation:item_ide'),
			 'all' => _T('notation:item_all')
			 ), "<br/>");
		echo '<p>';
		echo _T("notation:change_note_label");
		echo '</p>';
		echo "<input type='checkbox' name='change_note'";
		if($change_note){
			echo " checked='checked'";
		}
		echo " class='fondl' />";
		echo _T('notation:change_note_texte');
		echo "</td><td><input type='submit' style='margin-left:0em' name='modifier' class='fondo' value='"._T('bouton_valider')."' />";
		
		echo '</td></tr><tr><td colspan="2">';
		debut_cadre_relief('',false,'',_T("notation:titre_ip"));
		echo("<span class='verdana2'>"._T("notation:info_ip")."</span>");
		fin_cadre_relief();
		
		echo "</td></tr></table>";
		fin_cadre_trait_couleur();
		
		/* Nombre d'etoiles */
		debut_cadre_trait_couleur("../"._DIR_PLUGIN_NOTATION."img_pack/note.gif", false, "", _T('notation:nb_etoiles'));
		debut_cadre_relief();
		echo("<span class='verdana2'>"._T("notation:info_etoiles")."</span>");
		fin_cadre_relief();
		echo _T('notation:valeur_nb_etoiles');
		echo "&nbsp;<input type='text' name='nombre' class='fondl' value=\"$nb_note\" size='8' />";
		echo "<input type='submit' name='modifier' class='fondo' style='margin-left:1em;' value='"._T('bouton_valider')."' />";
		
		fin_cadre_trait_couleur();
		
		/* Fin */
		echo '</div></form>';
	}else{
		echo "<br/><br/>".gros_titre(_T('avis_non_acces_page'));
	}
	echo fin_gauche(), fin_page();
	
}
?>