<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('exec/inc_boites_infos');

function exec_jeux_voir(){
	global $jeux_caracteristiques;
	$commencer_page = charger_fonction('commencer_page', 'inc');
	$id_jeu = _request('id_jeu');
    
	include_spip('jeux_utils');

	$requete =    sql_fetsel('statut,contenu,id_jeu,type_jeu,titre_prive,date,type_resultat', 'spip_jeux', "id_jeu=$id_jeu");
	list($statut, $contenu, $id_jeu, $type_jeu, $titre_prive, $date, $type_resultat) =
		array($requete['statut'], $requete['contenu'], $requete['id_jeu'], $requete['type_jeu'], $requete['titre_prive'], $requete['date'], $requete['type_resultat']);
	$liste = jeux_liste_les_jeux($contenu);
	if(count($liste)==1)
		$configuration_defaut = jeux_configuration_generale($liste[0]);
	else {
		$configuration_defaut = array();
		foreach($liste as $jeu)
			if(count($t = jeux_configuration_generale($jeu)))
				$configuration_defaut[] = $jeux_caracteristiques['TYPES'][$jeu].'<ul><li>'.join('</li><li>', $t).'</li></ul>';
	}
	$configuration_interne = jeux_trouver_configuration_interne($contenu);
	// cas particulier des multi-jeux
	if(count($liste) && $liste[0]=='multi_jeux') {
		$configuration_interne = array($jeux_caracteristiques['TYPES']['multi_jeux'].'<ul><li>'.join('</li><li>', $configuration_interne).'</li></ul>');
		$textes = explode('['._JEUX_MULTI_JEUX.']', $contenu);
		unset($textes[0]);
		foreach($textes as $t) {
			$jeu = jeux_trouver_nom($t);
			if(count($t = jeux_trouver_configuration_interne($t)))
				$configuration_interne[] = $jeu.'<ul><li>'.join('</li><li>', $t).'</li></ul>';
		}
	} else $configuration_interne = jeux_trouver_configuration_interne($contenu);
	$titre_public = jeux_trouver_titre_public($contenu);
	if($titre_prive=='') $titre_prive = _T('jeux:sans_titre_prive');
	if($titre_public) {
		$titre_prive = _T('jeux:jeu_titre_prive_') . ' ' . $titre_prive;
		$titre_public = _T('jeux:jeu_titre_public_') . ' ' . $titre_public;
	}
	$contenu = $type_jeu==_T('jeux:jeu_vide')?_T('jeux:introuvable'):propre($contenu);
	$puce = puce_statut($statut);
	
	if(!$id_jeu){
		echo $commencer_page(_T("jeux:pas_de_jeu"));
		echo debut_gauche('',true);
		echo boite_infos_accueil();
		echo fin_gauche(), _T("jeux:pas_de_jeu"), fin_page();
		return;
	}
	
	echo $commencer_page(_T("jeux:jeu_numero", array('id'=>$id_jeu,'nom'=>$type_jeu)));
			
	echo debut_gauche('',true);
	echo boite_infos_jeu($id_jeu);
	echo boite_infos_accueil();

	debut_cadre_relief();
	echo "<strong>"._t("jeux:derniere_modif")."</strong><br />".affdate($date).' '.heures($date).":".minutes($date);
	fin_cadre_relief();

	echo creer_colonne_droite('',true);
	echo debut_droite('',true);
	debut_cadre_relief();

	// changement de statut
	if (autoriser('modifierstatut')){
		
		if (_request('statut_modif')){
			include_spip('base/jeux_modifier_statut');
			jeu_modifier_statut($id_jeu, $statut=_request('statut_modif'));
		}
		echo gros_titre($puce." "._T("jeux:jeu_numero", array('id'=>$id_jeu,'nom'=>$type_jeu)), '', '', false);

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
		echo "<span class='titrem'>"._T('jeux:cfg_type_resultat')
			."</span><ul><li>"._T("jeux:resultat2_$type_resultat").'</li></ul>';
		if(count($configuration_defaut))
			echo "<span class='titrem'>"._T('jeux:configuration_defaut')
				."</span><ul><li>".join('</li><li>', $configuration_defaut).'</li></ul>';
		if(count($configuration_interne))
			echo "<span class='titrem'>"._T('jeux:configuration_interne')
				."</span><ul><li>".join('</li><li>', $configuration_interne).'</li></ul>';
		fin_cadre_relief();
	}
	else
		echo gros_titre($puce." "._T("jeux:jeu_numero", array('id'=>$id_jeu,'nom'=>$type_jeu)), '', '', false);
	echo '<br />', $contenu;


	fin_cadre_relief();
	echo jeux_navigation_pagination();
	echo fin_gauche(), fin_page();
}


?>