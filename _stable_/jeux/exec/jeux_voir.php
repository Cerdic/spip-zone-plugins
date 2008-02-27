<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('exec/inc_boites_infos');

function exec_jeux_voir(){
	$id_jeu = _request('id_jeu');
	
	$requete = spip_fetch_array(spip_query("SELECT statut,contenu,id_jeu,type_jeu,titre_prive,date FROM spip_jeux WHERE id_jeu =".$id_jeu));
	list($statut,$contenu, $id_jeu, $type_jeu, $titre_prive, $date) =
		array($requete['statut'],$requete['contenu'], $requete['id_jeu'], $requete['type_jeu'], $requete['titre_prive'], $requete['date']);
	include_spip('jeux_utils');
	$titre_public = jeux_trouver_titre_public($contenu);
	if($titre_prive=='') $titre_prive = _T('jeux:sans_titre_prive');
	if($titre_public) {
		$titre_prive = _T('jeux:jeu_titre_prive_') . ' ' . $titre_prive;
		$titre_public = _T('jeux:jeu_titre_public_') . ' ' . $titre_public;
	}
	$contenu = $type_jeu==_T('jeux:jeu_vide')?_T('jeux:introuvable'):propre($contenu);
	
	if(!$id_jeu){
		jeux_debut_page(_T("jeux:pas_de_jeu"));
		gros_titre(_T("jeux:pas_de_jeu"), '', false);
		fin_page();
		return;
	}
	

	
	jeux_debut_page(_T("jeux:jeu_numero",array('id'=>$id_jeu,'nom'=>$type_jeu)));
			
	jeux_compat_boite('debut_gauche');
	
	boite_infos_jeu($id_jeu, $type_jeu);
	boite_infos_accueil();
	
	debut_cadre_relief();
	echo "<strong>"._t("jeux:derniere_modif")."</strong><br />".affdate($date).' '.heures($date).":".minutes($date);
	fin_cadre_relief();
	
	jeux_compat_boite('creer_colonne_droite');
	jeux_compat_boite('debut_droite');
	debut_cadre_relief();
	
	
	// changement de statut
	if (autoriser('modifierstatut')){
		
		if (_request('statut_modif')){
			include_spip('base/jeux_modifier_statut');
			jeu_modifier_statut($id_jeu,_request('statut_modif'));
			$statut=_request('statut_modif');
		}
		gros_titre(_T("jeux:jeu_numero", array('id'=>$id_jeu,'nom'=>$type_jeu)),puce_statut($statut, " style='vertical-align: center'"), '', false);
		echo propre("<div style='font-weight:bold'>$titre_prive</div>");
		if($titre_public) echo propre("<div style='font-weight:bold'>$titre_public</div>");

		debut_cadre_relief();
		echo "<form method='post' name='statut_edit'>\n";
		echo "<label><span class='titrem'>"._T('jeux:statut_jeu').'</span>&nbsp;<select name="statut_modif">';
		echo '<option value="publie">'._T('texte_statut_publie').'</option>';
		echo '<option value="poubelle"';	
		echo ($statut=='poubelle')?'selected="selected"':'';
		echo '>'._T('texte_statut_poubelle').'</option>';
		echo '<option value="refuse"';	
		echo ($statut=='refuse')?'selected="selected"':'';
		echo '>'._T('texte_statut_refuse').'</option>';
		echo "</select></label>&nbsp;<input type='submit' name='valider' value='"._T('bouton_valider')."' class='fondo' />";
		echo "</form>";
		fin_cadre_relief();
	}
	else
		gros_titre(_T("jeux:jeu_numero", array('id'=>$id_jeu,'nom'=>$type_jeu)),puce_statut($statut, " style='vertical-align: center'"), '', false);
	echo '<br />', $contenu;

	fin_cadre_relief();
	echo jeux_navigation_pagination();
	echo fin_gauche(), fin_page();
}


?>