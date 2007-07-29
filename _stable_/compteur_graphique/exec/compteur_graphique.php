<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function calcule_repertoire_max() {
    $i=0;
    $j=0;
    $cheminCG_rel="../lib/compteurgraphique_pack/";
    while ($j==0) {
        $i++;
        if (file_exists($cheminCG_rel.$i.'/0.gif') || file_exists($cheminCG_rel.$i.'/0.png')) {}
        else {$j=1;}
    }
    $i--;
    return $i;        
}

function exec_compteur_graphique(){

    include_spip("inc/presentation");
    $CG_nom_table = "ext_compteurgraphique";
    
    if (($_POST['drop_table_CG']) == 2) {
    spip_query('DROP TABLE ext_compteurgraphique');
    }

    if (isset($_POST['tous_habillage']) && is_numeric($_POST['tous_habillage']) && is_numeric($_POST['tous_chiffres'])) {
        $requete_nouveau_tous = "INSERT INTO ".$CG_nom_table." VALUES (NULL,NULL,NULL,NULL,6,".$_POST['tous_chiffres'].",".$_POST['tous_habillage'].")";
        $resultat_nouveau_tous = spip_query($requete_nouveau_tous);
    }
    
    if (isset($_POST['total_habillage']) && is_numeric($_POST['total_habillage']) && is_numeric($_POST['total_chiffres'])) {
        $requete_nouveau_total = "INSERT INTO ".$CG_nom_table." VALUES (NULL,NULL,NULL,NULL,7,".$_POST['total_chiffres'].",".$_POST['total_habillage'].")";
        $resultat_nouveau_total = spip_query($requete_nouveau_total);
    }
    
     if (isset($_POST['changement_habillage_total']) && is_numeric($_POST['changement_habillage_total'])) {
        $requete_changement_habillage_total = "UPDATE ".$CG_nom_table." SET habillage = ".$_POST['changement_habillage_total']." WHERE statut = 7";
        $resultat_changement_habillage_total = spip_query($requete_changement_habillage_total);
    }
    
    if (isset($_POST['changement_habillage_tous']) && is_numeric($_POST['changement_habillage_tous'])) {
        $requete_changement_habillage_tous = "UPDATE ".$CG_nom_table." SET habillage = ".$_POST['changement_habillage_tous']." WHERE statut = 6";
        $resultat_changement_habillage_tous = spip_query($requete_changement_habillage_tous);
    }
    
    if (isset($_POST['changement_chiffres_total']) && is_numeric($_POST['changement_chiffres_total'])) {
        $requete_changement_chiffres_tous = "UPDATE ".$CG_nom_table." SET longueur = ".$_POST['changement_chiffres_total']." WHERE statut = 7";
        $resultat_changement_chiffres_tous = spip_query($requete_changement_chiffres_tous);
    }
    
    if (isset($_POST['changement_chiffres_tous']) && is_numeric($_POST['changement_chiffres_tous'])) {
        $requete_changement_chiffres_tous = "UPDATE ".$CG_nom_table." SET longueur = ".$_POST['changement_chiffres_tous']." WHERE statut = 6";
        $resultat_changement_chiffres_tous = spip_query($requete_changement_chiffres_tous);
    }

    if (is_numeric($_POST['suppression_total']) && ($_POST['suppression_total']==1)) {
        $requete_suppr_tous = "DELETE FROM ".$CG_nom_table." WHERE statut = 7";
        $resultat_suppr_tous = spip_query($requete_suppr_tous);
    }
    
    if (is_numeric($_POST['suppression_tous']) && ($_POST['suppression_tous']==1)) {
        $requete_suppr_tous = "DELETE FROM ".$CG_nom_table." WHERE statut = 6";
        $resultat_suppr_tous = spip_query($requete_suppr_tous);
    }


    $icone = "../"._DIR_PLUGIN_COMPTEURGRAPHIQUE."/img_pack/CompteurGraphique.gif";
    $cheminCG_rel="../lib/compteurgraphique_pack/";

	debut_page(_T('compteurgraphique:CG_nom'));
    gros_titre(_T('compteurgraphique:CG_nom'));
    debut_gauche();
    debut_droite();
     if ( ($_POST['creer_table']) == 1) {
	include_spip("inc/CompteurGraphique_VerifBase");
	echo create_CompteurGraphiqueTable($CG_nom_table);
    }
    /* On teste la présence de la table dans la base de données, dans le cas contraire, on propose un formulaire de création */
    $verif_presence_table = spip_query("SELECT id_compteur FROM ".$CG_nom_table." WHERE statut=10");
if ($verif_presence_table == '') {
        debut_cadre_trait_couleur('', false, '', _T('compteurgraphique:CG_creer_table'));
            echo '<div style="text-align:center;">'._T('compteurgraphique:CG_creer_la_table').'<br />&nbsp;<br />';
            echo '<form method="POST" action="'.generer_url_ecrire("compteur_graphique").'">';
            echo '<input type="hidden" name="creer_table" value="1"><input type="submit" value="'._T('compteurgraphique:CG_creer').'"></form></div>';
        fin_cadre_trait_couleur();
	}
else {
    debut_cadre_trait_couleur($icone, false, '', _T('compteurgraphique:CG_exec_titre'));
    echo "<br />";

	if (($_POST['drop_table_CG']) == 1) {
	debut_cadre_enfonce('', false, '', _T('compteurgraphique:CG_suppr_table_confirm'));
        echo _T('compteurgraphique:CG_suppr_table_confirm_explic');
	echo '<form method="POST" action="'.generer_url_ecrire("compteur_graphique").'">';
	echo '<input type="hidden" name="drop_table_CG" value="2">';
	echo '<div style="text-align:center;"><br /><input type="submit" value="'._T('compteurgraphique:CG_confirmer').'"></div>';
	echo '</form>';
        fin_cadre_enfonce();}

        debut_cadre_sous_rub('', false, '',_T('compteurgraphique:CG_exec_tous_articles'));
        $requete1="SELECT longueur,habillage FROM ".$CG_nom_table." WHERE statut = 6";
        $resultat1= spip_query($requete1);
        $resultat1_tableau = spip_fetch_array($resultat1);       
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
        
        fin_cadre_sous_rub();
        echo "<br />";

	debut_cadre_enfonce('', false, '', _T('compteurgraphique:CG_def_compteur_tot'));
	
	$test_compteur_total = spip_query("SELECT habillage,longueur FROM ext_compteurgraphique WHERE statut = 7");
	$resultat_total = spip_fetch_array($test_compteur_total);       
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
        fin_cadre_enfonce();
	echo '<br />';
        
	
	
	if ($_POST['config_CG']=='oui') {
		spip_query ("DELETE FROM ".$CG_nom_table." WHERE statut = 9");
	}
	if ($_POST['config_CG']=='non') {
		spip_query ("DELETE FROM ".$CG_nom_table." WHERE statut = 9");
		spip_query ("INSERT INTO ".$CG_nom_table." VALUES (NULL,NULL,NULL,NULL,9,NULL,NULL)");
	}
	$test_config = spip_query("SELECT id_compteur FROM ".$CG_nom_table." WHERE statut = 9");
	$tab_config = spip_fetch_array($test_config);
	$res_config = $tab_config['id_compteur'];
	
        debut_cadre_enfonce('', false, '', _T('compteurgraphique:CG_config_CG'));
        echo _T('compteurgraphique:CG_config_CG_explic');
	echo '<form method="POST" action="'.generer_url_ecrire("compteur_graphique").'"><div style="text-align:center;">';
	echo _T('compteurgraphique:CG_oui').'<input type="radio" name="config_CG" value="oui" ';
	if (!isset($res_config)) {echo 'checked';}
	echo '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo _T('compteurgraphique:CG_non').'<input type="radio" name="config_CG" value="non" ';
	if (isset($res_config)) {echo 'checked';}
	echo '></div><hr />';
	
	if ($_POST['genere_CG_miniatures']=='gif') {
		spip_query ("DELETE FROM ".$CG_nom_table." WHERE statut = 11");
	}
	if ($_POST['genere_CG_miniatures']=='png') {
		spip_query ("DELETE FROM ".$CG_nom_table." WHERE statut = 11");
		spip_query ("INSERT INTO ".$CG_nom_table." VALUES (NULL,NULL,NULL,NULL,11,NULL,NULL)");
	}
	$test1 = spip_query("SELECT id_compteur FROM ".$CG_nom_table." WHERE statut = 11");
	$tab1 = spip_fetch_array($test1);
	$res1 = $tab1['id_compteur'];
	if (isset($res1)) {$t1GIF=''; $t1PNG='checked';} else {$t1GIF='checked'; $t1PNG='';}
	echo _T('compteurgraphique:CG_config_genere');
	echo '<br /><div style="text-align:center;">GIF <input type="radio" name="genere_CG_miniatures" value="gif" ';
	echo $t1GIF.' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo 'PNG <input type="radio" name="genere_CG_miniatures" value="png" ';
	echo $t1PNG.'></div><br /><hr />';
	
	if ($_POST['genere_CG']=='gif') {
		spip_query ("DELETE FROM ".$CG_nom_table." WHERE statut = 12");
	}
	if ($_POST['genere_CG']=='png') {
		spip_query ("DELETE FROM ".$CG_nom_table." WHERE statut = 12");
		spip_query ("INSERT INTO ".$CG_nom_table." VALUES (NULL,NULL,NULL,NULL,12,NULL,NULL)");
	}
	$test2 = spip_query("SELECT id_compteur FROM ".$CG_nom_table." WHERE statut = 12");
	$tab2 = spip_fetch_array($test2);
	$res2 = $tab2['id_compteur'];
	if (isset($res2)) {$t2GIF=''; $t2PNG='checked';} else {$t2GIF='checked'; $t2PNG='';}
	echo _T('compteurgraphique:CG_config_genere_final');
	echo '<br /><div style="text-align:center;">GIF <input type="radio" name="genere_CG" value="gif" ';
	echo $t2GIF.' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo 'PNG <input type="radio" name="genere_CG" value="png" ';
	echo $t2PNG.' ></div><br /><hr />';
	
	if ($_POST['transparent_CG']=='oui') {
		spip_query ("DELETE FROM ".$CG_nom_table." WHERE statut = 13");
	}
	if ($_POST['transparent_CG']=='non') {
		spip_query ("DELETE FROM ".$CG_nom_table." WHERE statut = 13");
		spip_query ("INSERT INTO ".$CG_nom_table." VALUES (NULL,NULL,NULL,NULL,13,NULL,NULL)");
	}
	$test3 = spip_query("SELECT id_compteur FROM ".$CG_nom_table." WHERE statut = 13");
	$tab3 = spip_fetch_array($test3);
	$res3 = $tab3['id_compteur'];
	if (isset($res3)) {$t3OUI=''; $t2NON='checked';} else {$t2OUI='checked'; $t2NON='';}
	echo _T('compteurgraphique:CG_config_transparence');
	echo '<br /><div style="text-align:center;">'._T('compteurgraphique:CG_oui').' <input type="radio" name="transparent_CG" value="oui" ';
	echo $t2OUI.' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo _T('compteurgraphique:CG_non').'<input type="radio" name="transparent_CG" value="non" ';
	echo $t2NON.'></div><br />';
	
	
	echo '<div style="text-align:center;"><input type="submit" value="'._T('compteurgraphique:CG_valider').'"></div>';
	echo '</form>';
        fin_cadre_enfonce();
	echo '<br />';
	
        debut_cadre_enfonce('', false, '', _T('compteurgraphique:CG_drop_table'));
        echo _T('compteurgraphique:CG_drop_table_explication');
	echo '<form method="POST" action="'.generer_url_ecrire("compteur_graphique").'">';
	echo '<input type="hidden" name="drop_table_CG" value="1">';
	echo '<div style="text-align:center;"><br /><input type="submit" value="'._T('compteurgraphique:CG_suppr').'"></div>';
	echo '</form>';
        fin_cadre_enfonce();
        
    fin_cadre_trait_couleur();
}
    echo fin_gauche(), fin_page();
}
?>