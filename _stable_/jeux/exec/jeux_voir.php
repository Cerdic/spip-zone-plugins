<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('exec/inc_boites_infos');

function exec_jeux_voir(){
	$id_jeu = _request('id_jeu');

	include_spip('jeux_utils');

//	TODO :
//	include_spip('public/assembler');
//	echo recuperer_fond('fonds/jeux_voir',array('id_jeu'=>$id_jeu));

	$requete = jeux_fetsel('statut,contenu,id_jeu,type_jeu,titre_prive,date,type_resultat', 'spip_jeux', "id_jeu=$id_jeu");
	list($statut, $contenu, $id_jeu, $type_jeu, $titre_prive, $date, $type_resultat) =
		array($requete['statut'],$requete['contenu'], $requete['id_jeu'], $requete['type_jeu'], $requete['titre_prive'], $requete['date'], $requete['type_resultat']);

	$configuration_interne = jeux_trouver_configuration_interne($contenu);
	$titre_public = jeux_trouver_titre_public($contenu);
	if($titre_prive=='') $titre_prive = _T('jeux:sans_titre_prive');
	if($titre_public) {
		$titre_prive = _T('jeux:jeu_titre_prive_') . ' ' . $titre_prive;
		$titre_public = _T('jeux:jeu_titre_public_') . ' ' . $titre_public;
	}
	$contenu = $type_jeu==_T('jeux:jeu_vide')?_T('jeux:introuvable'):propre($contenu);
	
	if(!$id_jeu){
		jeux_debut_page(_T("jeux:pas_de_jeu"));
		jeux_compat_boite('debut_gauche');
		echo boite_infos_accueil();
		echo fin_gauche(), _T("jeux:pas_de_jeu"), fin_page();
		return;
	}
	
	jeux_debut_page(_T("jeux:jeu_numero",array('id'=>$id_jeu,'nom'=>$type_jeu)));
			
	jeux_compat_boite('debut_gauche');
	echo boite_infos_jeu($id_jeu);
	echo boite_infos_accueil();

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
			jeu_modifier_statut($id_jeu, $statut=_request('statut_modif'));
		}
		echo gros_titre(_T("jeux:jeu_numero", array('id'=>$id_jeu,'nom'=>$type_jeu)), 'puce-'.puce_statut($statut).'.gif', '', false);

		debut_cadre_relief();
		echo "<span class='titrem'>"._T('jeux:titres_jeu')
			.'</span>'.propre('<ul><li>'.$titre_prive.($titre_public?"</li><li>$titre_public":'').'</li></ul>');
		echo "<form method='post' name='statut_edit'>\n";
		echo "<span class='titrem'>"._T('jeux:statut_jeu')."</span><ul><li>$type_jeu</li><li><select name='statut_modif'>";
		echo '<option value="publie">'._T('texte_statut_publie').'</option>';
		echo '<option value="poubelle"'.($statut=='poubelle'?' selected="selected"':'').'>'._T('texte_statut_poubelle').'</option>';
		echo '<option value="refuse"'.($statut=='refuse'?' selected="selected"':'').'>'._T('texte_statut_refuse').'</option>';
		echo "</select>&nbsp;<input type='submit' name='valider' value='"._T('bouton_valider')."' class='fondo' /></li></ul>\n";
		echo "</form>";
		echo "<span class='titrem'>"._T('jeux:type_resultat')
			."</span><ul><li>"._T("jeux:resultat2_$type_resultat")."</li></ul>";
		if(count($configuration_interne))
			echo "<span class='titrem'>"._T('jeux:configuration_interne')
				."</span><ul><li>".join('</li><li>', $configuration_interne)."</li></ul>";
		fin_cadre_relief();
	}
	else
		echo gros_titre(_T("jeux:jeu_numero", array('id'=>$id_jeu,'nom'=>$type_jeu)), 'puce-'.puce_statut($statut).'.gif', '', false);
	echo '<br />', $contenu;

//echo 'compacter (auteur=1) : NOT IN ',recuperer_fond('fonds/jeux_compacter', array('id_auteur'=>1));
//echo "<br>compacter (jeu=$id_jeu) : NOT IN ",recuperer_fond('fonds/jeux_compacter', array('id_jeu'=>$id_jeu));
//echo '<br>compacter (tout) : NOT IN ',recuperer_fond('fonds/jeux_compacter');

	fin_cadre_relief();
	echo jeux_navigation_pagination();
	echo fin_gauche(), fin_page();
}


?>