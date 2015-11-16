<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip("inc/presentation");

function calcule_repertoire_max() {
    $i=0;
    $j=0;
    $cheminCG_rel=_DIR_PLUGIN_COMPTEURGRAPHIQUE."img_pack/";
    while ($j==0) {
        $i++;
        if (file_exists($cheminCG_rel.$i.'/0.gif') || file_exists($cheminCG_rel.$i.'/0.png')) {}
        else {$j=1;}
    }
    $i--;
    return $i;        
}

function exec_compteur_graphique(){

 

    $CG_nom_table = "spip_compteurgraphique";
    
    if (($_POST['drop_table_CG']) == 2) {
    sql_query('DROP TABLE spip_compteurgraphique');
    }

    if (isset($_POST['tous_habillage']) && is_numeric($_POST['tous_habillage']) && is_numeric($_POST['tous_chiffres'])) {
        $resultat_nouveau_tous = sql_insertq($CG_nom_table,array("statut" => 6,"longueur" => $_POST['tous_chiffres'],"habillage" => $_POST['tous_habillage']));
    }
    
    if (isset($_POST['total_habillage']) && is_numeric($_POST['total_habillage']) && is_numeric($_POST['total_chiffres'])) {
        $resultat_nouveau_total = sql_insertq($CG_nom_table,array("statut" => 7,"longueur" => $_POST['total_chiffres'],"habillage" => $_POST['total_habillage']));
    }
    
     if (isset($_POST['changement_habillage_total']) && is_numeric($_POST['changement_habillage_total'])) {
        $resultat_changement_habillage_total = sql_updateq($CG_nom_table,array("habillage" => $_POST['changement_habillage_total']),"statut = 7");
    }
    
    if (isset($_POST['changement_habillage_tous']) && is_numeric($_POST['changement_habillage_tous'])) {
        $resultat_changement_habillage_tous = sql_updateq($CG_nom_table,array("habillage" => $_POST['changement_habillage_tous']),"statut = 6");
    }
    
    if (isset($_POST['changement_chiffres_total']) && is_numeric($_POST['changement_chiffres_total'])) {
        $resultat_changement_chiffres_tous = sql_updateq($CG_nom_table,array("longueur" => $_POST['changement_chiffres_total']),"statut = 7");
    }
    
    if (isset($_POST['changement_chiffres_tous']) && is_numeric($_POST['changement_chiffres_tous'])) {
        $resultat_changement_chiffres_tous = sql_updateq($CG_nom_table,array("longueur" => $_POST['changement_chiffres_tous']),"statut = 6");
    }

    if (is_numeric($_POST['suppression_total']) && ($_POST['suppression_total']==1)) {
        $resultat_suppr_tous = sql_delete($CG_nom_table,"statut = 7");
    }
    
    if (is_numeric($_POST['suppression_tous']) && ($_POST['suppression_tous']==1)) {
        $resultat_suppr_tous = sql_delete($CG_nom_table,"statut = 6");
    }


    $icone = "../"._DIR_PLUGIN_COMPTEURGRAPHIQUE."img_pack/CompteurGraphique.gif";
    $cheminCG_rel=_DIR_PLUGIN_COMPTEURGRAPHIQUE."img_pack/";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('compteurgraphique:CG_nom') , '', '', '');
	echo gros_titre(_T('compteurgraphique:CG_nom'),'',false);
	echo debut_gauche("",true);
	echo debut_droite("",true);
	
	if ($GLOBALS['connect_statut'] != "0minirezo" OR !$GLOBALS["connect_toutes_rubriques"]) {
	echo _T('avis_non_acces_page');
	echo fin_gauche(), fin_page();
	exit;
    }	
    
     if ( ($_POST['creer_table']) == 1) {
	include_spip("inc/CompteurGraphique_VerifBase");
	echo create_CompteurGraphiqueTable($CG_nom_table);
    }
    /* On teste la présence de la table dans la base de données, dans le cas contraire, on propose un formulaire de création */
    $verif_presence_table = sql_select("id_compteur",$CG_nom_table,"statut=10");
if ($verif_presence_table == '') {
        echo debut_cadre_trait_couleur('', true, '', _T('compteurgraphique:CG_creer_table'));
            echo '<div style="text-align:center;">'._T('compteurgraphique:CG_creer_la_table').'<br />&nbsp;<br />';
            echo '<form method="POST" action="'.generer_url_ecrire("compteur_graphique").'">';
            echo '<input type="hidden" name="creer_table" value="1"><input type="submit" value="'._T('compteurgraphique:CG_creer').'"></form></div>';
        echo fin_cadre_trait_couleur(true);
	}
else {
    echo debut_cadre_trait_couleur($icone, true, '', _T('compteurgraphique:CG_exec_titre'));
    echo "<br />";

	if (($_POST['drop_table_CG']) == 1) {
	echo debut_cadre_enfonce('', true, '', _T('compteurgraphique:CG_suppr_table_confirm'));
        echo _T('compteurgraphique:CG_suppr_table_confirm_explic');
	echo '<form method="POST" action="'.generer_url_ecrire("compteur_graphique").'">';
	echo '<input type="hidden" name="drop_table_CG" value="2">';
	echo '<div style="text-align:center;"><br /><input type="submit" value="'._T('compteurgraphique:CG_confirmer').'"></div>';
	echo '</form>';
        echo fin_cadre_enfonce(true);}

        echo debut_cadre_sous_rub('', true, '',_T('compteurgraphique:CG_exec_tous_articles'));
        $resultat1= sql_select("longueur,habillage",$CG_nom_table,"statut = 6");
        $resultat1_tableau = sql_fetch($resultat1);
        $CG_tous_longueur = $resultat1_tableau['longueur'];
        $CG_tous_habillage = $resultat1_tableau['habillage'];
        if (isset($CG_tous_habillage)) {
            echo _T('compteurgraphique:CG_exec_modele_tous');
            if ($CG_tous_longueur==0) {echo _T('compteurgraphique:CG_gestion_chiffres_automatique');}
            else {
                echo $CG_tous_longueur." ";
                if ($CG_tous_longueur==1) {echo _T('compteurgraphique:CG_affiche_chiffre');}
                else {echo _T('compteurgraphique:CG_affiche_chiffres');}
                echo _T('compteurgraphique:CG_exec_modele_compteur');
            }
            echo _T('compteurgraphique:CG_habillage_choisi');
            for ($m=1;$m<=5;$m++){
                echo '<img src="'.$cheminCG_rel.$CG_tous_habillage.'/'.$m.'.gif">';
            }
            echo "</div><hr />";
            echo bouton_block_depliable(_T('compteurgraphique:CG_modif_habillage'),false,'');
            echo debut_block_depliable(false);
            echo '<form method="POST" action="'.generer_url_ecrire("compteur_graphique").'">';
            $limiteCG = calcule_repertoire_max();
            $nbre_cellules=10;
            echo '<table border cellpadding="4" align="center">';
            for ($k=1;$k<=$limiteCG;$k++) {
                if (($k%$nbre_cellules)==1){echo '<tr>';}
                echo '<td align="center">';
                if (file_exists($cheminCG_rel.$k.'/8.gif')) {
                    echo '<div style="text-align:center;"><img src="'.$cheminCG_rel.$k.'/8.gif"></div><br /><input type="radio" name="changement_habillage_tous" value='.$k;
                    if ($k==$CG_tous_habillage) {echo ' checked';}
                echo ' >';
                }
                echo '</td>';
                if (($k%$nbre_cellules)==0){echo '</tr>';}
            }
            if (($limiteCG%$nbre_cellules)!=0) {echo '</tr>';}
            echo '</table><br /><div style="text-align:center;"><input type="submit" value="'._T('compteurgraphique:CG_modif_habillage').'"></div></form>';
            echo fin_block();
            echo "<br />";

            echo bouton_block_depliable(_T('compteurgraphique:CG_modif_nombre_chiffres'),false,'');
            echo debut_block_depliable(false);?>            
            <div style="text-align:center;"><form method="POST" action="<?php echo generer_url_ecrire("compteur_graphique");?>">
            <select name="changement_chiffres_tous">
                <option value="0" selected><?php echo _T('compteurgraphique:CG_chiffre_auto');?></option>
                <?php for ($n=1;$n<=10;$n++) {
                    echo '<option value="'.$n.'">'.$n._T('compteurgraphique:CG_chiffre');
                    if ($n!=1){echo _T('compteurgraphique:CG_pluriel');}
                    echo '</option>';
                } ?>
            </select><br /><input type="submit" value="<?php echo _T('compteurgraphique:CG_modif'); ?>"></form></div> 
            <?php echo fin_block();
            echo "<br />";

            echo bouton_block_depliable(_T('compteurgraphique:CG_suppr_modele_compteur'),false,'');
            echo debut_block_depliable(false);
            echo '<div style="text-align:center;"><form method="POST" action="'.generer_url_ecrire("compteur_graphique").'">';
            echo '<input type="hidden" name="suppression_tous" value="1"><input type="submit" value="'._T('compteurgraphique:CG_suppr').'"></form></div>';
            echo fin_block();
        }
        else {
            echo _T('compteurgraphique:CG_exec_aucun_modele_compteur');
            echo bouton_block_depliable(_T('compteurgraphique:CG_exec_creer_modele_compteur'),false,'');
            echo debut_block_depliable(false);?>
            <form method="POST" action="<?php echo generer_url_ecrire("compteur_graphique");?>">
            <?php    $limiteCG = calcule_repertoire_max();
            $nbre_cellules=10;
            echo _T('compteurgraphique:CG_choix_habillage');
            echo '<table border cellpadding="4" align="center">';
            for ($k=1;$k<=$limiteCG;$k++) {
                if (($k%$nbre_cellules)==1){echo '<tr>';}
                echo '<td align="center">';
                if (file_exists($cheminCG_rel.$k.'/8.gif')) {
                    echo '<div style="text-align:center;"><img src="'.$cheminCG_rel.$k.'/8.gif"></div><br /><input type="radio" name="tous_habillage" value='.$k;
                    if ($k==1) {echo ' checked';}
                    echo ' >';
                }
                echo '</td>';
                if (($k%$nbre_cellules)==0){echo '</tr>';}
            }
            if (($limiteCG%$nbre_cellules)!=0) {echo '</tr>';}
            echo '</table><hr />&nbsp;<br />';
            echo _T('compteurgraphique:CG_choix_nombre_chiffres');?>
            <select name="tous_chiffres">
                <option value="0" selected><?php echo _T('compteurgraphique:CG_chiffre_auto');?></option>
                <?php for ($n=1;$n<=10;$n++) {
                    echo '<option value="'.$n.'">'.$n._T('compteurgraphique:CG_chiffre');
                    if ($n!=1){echo _T('compteurgraphique:CG_pluriel');}
                    echo '</option>';
                } ?>
            </select><br />&nbsp;<br /><input type="submit" value="<?php echo _T('compteurgraphique:CG_creer');?>"></div></form>
        
            <?php echo fin_block();
        }
        
        echo fin_cadre_sous_rub(true);
        echo "<br />";

	echo debut_cadre_enfonce('', true, '', _T('compteurgraphique:CG_def_compteur_tot'));
	
	$test_compteur_total = sql_select("habillage,longueur",$CG_nom_table,"statut = 7");
	$resultat_total = sql_fetch($test_compteur_total);       
        $CG_total_longueur = $resultat_total['longueur'];
        $CG_total_habillage = $resultat_total['habillage'];
	if ($CG_total_habillage=='') {	
        echo _T('compteurgraphique:CG_aucun_compteur_total').'<br />&nbsp;<br />';
	echo bouton_block_depliable(_T('compteurgraphique:CG_exec_creer_modele_compteur_total'),false,'');
	echo debut_block_depliable(false);?>
            <form method="POST" action="<?php echo generer_url_ecrire("compteur_graphique");?>">
            <?php    $limiteCG = calcule_repertoire_max();
            $nbre_cellules=10;
            echo _T('compteurgraphique:CG_choix_habillage');
            echo '<table border cellpadding="4" align="center">';
            for ($k=1;$k<=$limiteCG;$k++) {
                if (($k%$nbre_cellules)==1){echo '<tr>';}
                echo '<td align="center">';
                if (file_exists($cheminCG_rel.$k.'/8.gif')) {
                    echo '<div style="text-align:center;"><img src="'.$cheminCG_rel.$k.'/8.gif"></div><br /><input type="radio" name="total_habillage" value='.$k;
                    if ($k==1) {echo ' checked';}
                    echo ' >';
                }
                echo '</td>';
                if (($k%$nbre_cellules)==0){echo '</tr>';}
            }
            if (($limiteCG%$nbre_cellules)!=0) {echo '</tr>';}
            echo '</table><hr />&nbsp;<br />';
            echo _T('compteurgraphique:CG_choix_nombre_chiffres');?>
            <select name="total_chiffres">
                <option value="0" selected><?php echo _T('compteurgraphique:CG_chiffre_auto');?></option>
                <?php for ($n=1;$n<=10;$n++) {
                    echo '<option value="'.$n.'">'.$n._T('compteurgraphique:CG_chiffre');
                    if ($n!=1){echo _T('compteurgraphique:CG_pluriel');}
                    echo '</option>';
                } ?>
            </select><br />&nbsp;<br /><input type="submit" value="<?php echo _T('compteurgraphique:CG_creer');?>"></div></form>
	
	 <?php } else {
		echo _T('compteurgraphique:CG_exec_modele_total');
            if ($CG_total_longueur==0) {echo _T('compteurgraphique:CG_gestion_chiffres_automatique');}
            else {
                echo $CG_total_longueur." ";
                if ($CG_total_longueur==1) {echo _T('compteurgraphique:CG_affiche_chiffre');}
                else {echo _T('compteurgraphique:CG_affiche_chiffres');}
                echo _T('compteurgraphique:CG_exec_modele_compteur');
            }
            echo _T('compteurgraphique:CG_habillage_choisi');
            for ($m=1;$m<=5;$m++){
                echo '<img src="'.$cheminCG_rel.$CG_total_habillage.'/'.$m.'.gif">';
            }
            echo "<hr />";
            echo bouton_block_depliable(_T('compteurgraphique:CG_modif_habillage'),false,'');
            echo debut_block_depliable(false);
            echo '<form method="POST" action="'.generer_url_ecrire("compteur_graphique").'">';
            $limiteCG = calcule_repertoire_max();
            $nbre_cellules=10;
            echo '<table border cellpadding="4" align="center">';
            for ($k=1;$k<=$limiteCG;$k++) {
                if (($k%$nbre_cellules)==1){echo '<tr>';}
                echo '<td align="center">';
                if (file_exists($cheminCG_rel.$k.'/8.gif')) {
                    echo '<div style="text-align:center;"><img src="'.$cheminCG_rel.$k.'/8.gif"></div><br /><input type="radio" name="changement_habillage_total" value='.$k;
                    if ($k==$CG_total_habillage) {echo ' checked';}
                echo ' >';
                }
                echo '</td>';
                if (($k%$nbre_cellules)==0){echo '</tr>';}
            }
            if (($limiteCG%$nbre_cellules)!=0) {echo '</tr>';}
            echo '</table><br /><div style="text-align:center;"><input type="submit" value="'._T('compteurgraphique:CG_modif_habillage').'"></div></form>';
            echo fin_block();
            echo "<br />";

            echo bouton_block_depliable(_T('compteurgraphique:CG_modif_nombre_chiffres'),false,'');
            echo debut_block_depliable(false);?>            
            <div style="text-align:center;"><form method="POST" action="<?php echo generer_url_ecrire("compteur_graphique");?>">
            <select name="changement_chiffres_total">
                <option value="0" selected><?php echo _T('compteurgraphique:CG_chiffre_auto');?></option>
                <?php for ($n=1;$n<=10;$n++) {
                    echo '<option value="'.$n.'">'.$n._T('compteurgraphique:CG_chiffre');
                    if ($n!=1){echo _T('compteurgraphique:CG_pluriel');}
                    echo '</option>';
                } ?>
            </select><br /><input type="submit" value="<?php echo _T('compteurgraphique:CG_modif'); ?>"></form></div> 
            <?php echo fin_block().'<br />';
	
	echo bouton_block_depliable(_T('compteurgraphique:CG_suppr_modele_compteur'),false,'');
	echo debut_block_depliable(false);
	echo '<div style="text-align:center;"><form method="POST" action="'.generer_url_ecrire("compteur_graphique").'">';
	echo '<input type="hidden" name="suppression_total" value="1"><input type="submit" value="'._T('compteurgraphique:CG_suppr').'"></form></div>';
	echo fin_block();
	echo "<br />";
	}
	
	echo fin_block();
        echo fin_cadre_enfonce(true);
	echo '<br />';
        
	
	
	if ($_POST['config_CG']=='oui') {
		sql_delete($CG_nom_table,"statut = 9");
	}
	if ($_POST['config_CG']=='non') {
		sql_delete($CG_nom_table,"statut = 9");
		sql_insertq($CG_nom_table,array("statut" => 9));
	}
	$test_config = sql_select("id_compteur",$CG_nom_table,"statut = 9");
	$tab_config = sql_fetch($test_config);
	$res_config = $tab_config['id_compteur'];
	
        echo debut_cadre_enfonce('', true, '', _T('compteurgraphique:CG_config_CG'));
        echo _T('compteurgraphique:CG_config_CG_explic');
	echo '<form method="POST" action="'.generer_url_ecrire("compteur_graphique").'"><div style="text-align:center;">';
	echo _T('compteurgraphique:CG_oui').'<input type="radio" name="config_CG" value="oui" ';
	if (!isset($res_config)) {echo 'checked';}
	echo '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo _T('compteurgraphique:CG_non').'<input type="radio" name="config_CG" value="non" ';
	if (isset($res_config)) {echo 'checked';}
	echo '></div><hr />';
	
	if ($_POST['genere_CG_miniatures']=='gif') {
		sql_delete($CG_nom_table,"statut = 11");
	}
	if ($_POST['genere_CG_miniatures']=='png') {
		sql_delete($CG_nom_table,"statut = 11");
		sql_insertq($CG_nom_table,array("statut" => 11));
	}
	$test1 = sql_select("id_compteur",$CG_nom_table,"statut = 11");
	$tab1 = sql_fetch($test1);
	$res1 = $tab1['id_compteur'];
	if (isset($res1)) {$t1GIF=''; $t1PNG='checked';} else {$t1GIF='checked'; $t1PNG='';}
	echo _T('compteurgraphique:CG_config_genere');
	echo '<br /><div style="text-align:center;">GIF <input type="radio" name="genere_CG_miniatures" value="gif" ';
	echo $t1GIF.' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo 'PNG <input type="radio" name="genere_CG_miniatures" value="png" ';
	echo $t1PNG.'></div><br /><hr />';
	
	if ($_POST['genere_CG']=='gif') {
		sql_delete($CG_nom_table,"statut = 12");
	}
	if ($_POST['genere_CG']=='png') {
		sql_delete($CG_nom_table,"statut = 12");
		sql_insertq($CG_nom_table,array("statut" => 12));
	}
	$test2 = sql_select("id_compteur",$CG_nom_table,"statut = 12");
	$tab2 = sql_fetch($test2);
	$res2 = $tab2['id_compteur'];
	if (isset($res2)) {$t2GIF=''; $t2PNG='checked';} else {$t2GIF='checked'; $t2PNG='';}
	echo _T('compteurgraphique:CG_config_genere_final');
	echo '<br /><div style="text-align:center;">GIF <input type="radio" name="genere_CG" value="gif" ';
	echo $t2GIF.' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo 'PNG <input type="radio" name="genere_CG" value="png" ';
	echo $t2PNG.' ></div><br /><hr />';
	
	if ($_POST['transparent_CG']=='oui') {
		sql_delete($CG_nom_table,"statut = 13");
	}
	if ($_POST['transparent_CG']=='non') {
		sql_delete($CG_nom_table,"statut = 13");
		sql_insertq($CG_nom_table,array("statut" => 13));
	}
	$test3 = sql_select("id_compteur",$CG_nom_table,"statut = 13");
	$tab3 = sql_fetch($test3);
	$res3 = $tab3['id_compteur'];
	if (isset($res3)) {$t3OUI=''; $t2NON='checked';} else {$t2OUI='checked'; $t2NON='';}
	echo _T('compteurgraphique:CG_config_transparence');
	echo '<br /><div style="text-align:center;">'._T('compteurgraphique:CG_oui').' <input type="radio" name="transparent_CG" value="oui" ';
	echo $t2OUI.' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo _T('compteurgraphique:CG_non').'<input type="radio" name="transparent_CG" value="non" ';
	echo $t2NON.'></div><br />';
	
	
	echo '<div style="text-align:center;"><input type="submit" value="'._T('compteurgraphique:CG_valider').'"></div>';
	echo '</form>';
        echo fin_cadre_enfonce(true);
	echo '<br />';
	
        echo debut_cadre_enfonce('', true, '', _T('compteurgraphique:CG_drop_table'));
        echo _T('compteurgraphique:CG_drop_table_explication');
	echo '<form method="POST" action="'.generer_url_ecrire("compteur_graphique").'">';
	echo '<input type="hidden" name="drop_table_CG" value="1">';
	echo '<div style="text-align:center;"><br /><input type="submit" value="'._T('compteurgraphique:CG_suppr').'"></div>';
	echo '</form>';
        echo fin_cadre_enfonce(true);
        
    echo fin_cadre_trait_couleur(true);
}
    echo fin_gauche(), fin_page();
}
?>