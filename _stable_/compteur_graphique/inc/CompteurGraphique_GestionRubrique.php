<?php
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
function CompteurGraphique_rubriquedroite($exec) {
$verif_presence_table = spip_query("SELECT id_compteur FROM ext_compteurgraphique WHERE statut=10");
if ($verif_presence_table == '') {return;}
$retour = '';
$CG_nom_table = "ext_compteurgraphique";
$cheminCG = "../"._DIR_PLUGIN_COMPTEURGRAPHIQUE."/img_pack/";
$icone = $cheminCG."CompteurGraphique.gif";
$cheminCG_rel="../lib/compteurgraphique_pack/";
include_spip("inc/presentation");
if ( isset($_GET['id_rubrique']) AND is_numeric($_GET['id_rubrique']) ) {$id_rubrique=$_GET['id_rubrique'];}
    if ((is_numeric($_POST['numero_suppression_compteur']) && isset($_POST['numero_suppression_compteur'])) xor (isset($_POST['suppression_compteur']) && is_numeric($_POST['suppression_compteur']))) {
        $requete_suppr_compteur = "DELETE FROM ".$CG_nom_table." WHERE id_rubrique=".$id_rubrique;
        $resultat_suppr_compteur=spip_query($requete_suppr_compteur);
    }
    
    if (isset($_POST['nouveau_habillage']) && is_numeric($_POST['nouveau_habillage']) && isset($_POST['nouveau_chiffres']) && is_numeric($_POST['nouveau_chiffres'])) {
        $requete_cree_compteur = "INSERT INTO ".$CG_nom_table." VALUES (NULL,NULL,NULL,".$id_rubrique.",4,".$_POST['nouveau_chiffres'].",".$_POST['nouveau_habillage'].")";
        $resultat_cree_compteur = spip_query($requete_cree_compteur);
    }
    
    if (isset($_POST['changement_habillage']) && is_numeric($_POST['changement_habillage'])) {
        $requete_changement_habillage = "UPDATE ".$CG_nom_table." SET habillage = ".$_POST['changement_habillage']." WHERE id_rubrique = ".$id_rubrique;
        $resultat_changement_habillage = spip_query($requete_changement_habillage);
    }
    
    if (isset($_POST['changement_chiffres']) && is_numeric($_POST['changement_chiffres'])) {
        $requete_changement_chiffres = "UPDATE ".$CG_nom_table." SET longueur = ".$_POST['changement_chiffres']." WHERE id_rubrique = ".$id_rubrique;
        $resultat_changement_chiffres = spip_query($requete_changement_chiffres);
    }
    
    if (isset($_POST['interdiction_compteur']) && ($_POST['interdiction_compteur']==1)) {
        $requete_interdiction_compteur = "UPDATE ".$CG_nom_table." SET statut = 5 WHERE id_rubrique = ".$id_rubrique;
        $resultat_interdiction_compteur = spip_query($requete_interdiction_compteur);
    }
    
    if (isset($_POST['interdiction_compteur']) && ($_POST['interdiction_compteur']==2)) {
        $requete_interdiction_compteur = "INSERT INTO ".$CG_nom_table." VALUES (NULL,NULL,NULL,".$id_rubrique.",5,NULL,NULL)";
        $resultat_interdiction_compteur = spip_query($requete_interdiction_compteur);
    }
    
    $retour .= debut_cadre_relief($icone, true, "", "Le compteur de visites d&eacute;fini pour les articles de cette rubrique");    $requete1 = "SELECT id_compteur,statut,longueur,habillage FROM ".$CG_nom_table." WHERE id_rubrique = ".$id_rubrique;
    $resultat1 = spip_query($requete1);
    $resultat1_tableau = spip_fetch_array($resultat1);
    $CG_id_compteur = $resultat1_tableau['id_compteur'];
    $CG_statut = $resultat1_tableau['statut'];
    $CG_longueur = $resultat1_tableau['longueur'];
    $CG_habillage = $resultat1_tableau['habillage'];
    

    if ($CG_statut==4) {
    $retour .= _T('compteurgraphique:CG_rubrique_modele_cree');
    $retour .= _T('compteurgraphique:CG_habillage_choisi');
    for ($m=1;$m<=5;$m++){
        $retour .= '<img src="'.$cheminCG_rel.$CG_habillage.'/'.$m.'.gif">';
    }
    $retour .= "<br />&nbsp;<br />";
    if ($CG_longueur==0) {$retour .= _T('compteurgraphique:CG_gestion_chiffres_automatique');}
            else {
                $retour .= $CG_longueur." ";
                if ($CG_longueur==1) {$retour .= _T('compteurgraphique:CG_affiche_chiffre');}
                else {$retour .= _T('compteurgraphique:CG_affiche_chiffres');}
                $retour .= _T('compteurgraphique:CG_rubrique_modele_compteur');
            }
    $retour .= "</div>";
    $retour .= bouton_block_depliable(_T('compteurgraphique:CG_modif_habillage'),false,'');
    $retour .= debut_block_depliable(false);
    $retour .= "<br /><div style='text-align:center;'>";
    $retour .= '<form method="POST" action="';
    $retour .= generer_url_ecrire($exec,"id_rubrique=$id_rubrique");
    $retour .= '">';
	$limiteCG = calcule_repertoire_max();
        $nbre_cellules=5;
        $retour .= '<table border cellpadding="4" align="center">';
        for ($k=1;$k<=$limiteCG;$k++) {
            if (($k%$nbre_cellules)==1){$retour .= '<tr>';}
            $retour .= '<td align="center">';
            if (file_exists($cheminCG_rel.$k.'/8.gif')) {
                $retour .= '<div style="text-align:center;"><img src="'.$cheminCG_rel.$k.'/8.gif"></div><br /><input type="radio" name="changement_habillage" value='.$k;
                if ($k==1) {$retour .= ' checked';}
                $retour .= ' >';
            }
            $retour .= '</td>';
            if (($k%$nbre_cellules)==0){$retour .= '</tr>';}
        }
        if (($limiteCG%$nbre_cellules)!=0) {$retour .= '</tr>';}
        $retour .= '</table><br /><input type="submit" value="'._T('compteurgraphique:CG_modif').'"></form></div>';
    $retour .= fin_block();
    $retour .= "<br />&nbsp;<br />";
    
    $retour .= bouton_block_depliable(_T('compteurgraphique:CG_modif_nombre_chiffres'),false,'');
    $retour .= debut_block_depliable(false);
	$retour .= '<br /><div style="text-align:center;"><form method="POST" action="';
	$retour .= generer_url_ecrire($exec,"id_rubrique=$id_rubrique");
	$retour .= '"><select name="changement_chiffres"><option value="0" selected>';
	$retour .= _T('compteurgraphique:CG_chiffre_auto');
	$retour .= '</option>';
                for ($n=1;$n<=10;$n++) {
                    $retour .= '<option value="'.$n.'">'.$n._T('compteurgraphique:CG_chiffre');
                    if ($n!=1){$retour .= _T('compteurgraphique:CG_pluriel');}
                    $retour .= '</option>';
                }
	$retour .= '</select><br />&nbsp;<br /><input type="submit" value="';
	$retour .= _T('compteurgraphique:CG_modif');
	$retour .= '"></form></div>';
	$retour .= fin_block();
    $retour .= "<br />";
    
    $retour .= bouton_block_depliable(_T('compteurgraphique:CG_suppr_modele_compteur'),false,'');
    $retour .= debut_block_depliable(false);
    $retour .= '<br /><div style="text-align:center;"><form method="POST" action="';
    $retour .= generer_url_ecrire($exec,"id_rubrique=$id_rubrique");
    $retour .= '"><input type="hidden" name="suppression_compteur" value="1"><input type="submit" value="';
    $retour .= _T('compteurgraphique:CG_suppr');
	$retour .= '"></form></div>';
    $retour .= fin_block();
    $retour .= "<br />&nbsp;<br />";
    
    $retour .= bouton_block_depliable(_T('compteurgraphique:CG_rubrique_interdire'),false,'');
    $retour .= debut_block_depliable(false);
    $retour .= '<br /><div style="text-align:center;"><form method="POST" action="';
    $retour .= generer_url_ecrire($exec,"id_rubrique=$id_rubrique");
    $retour .= '"><input type="hidden" name="interdiction_compteur" value="1"><input type="submit" value="';
    $retour .= _T('compteurgraphique:CG_interdire');
	$retour .= '"></form></div>';
	$retour .= fin_block();
    }
    
    elseif ($CG_statut==5) {
        $retour .= _T('compteurgraphique:CG_rubrique_desactive');
	$retour .= '<form method="POST" action="';
	$retour .= generer_url_ecrire($exec,"id_rubrique=$id_rubrique");
	$retour .= '" ><input type="hidden" name="numero_suppression_compteur" value="';
	$retour .= $CG_id_compteur;
	$retour .= '">';
        $retour .= _T('compteurgraphique:CG_rubrique_stop_desactiv');
	$retour .= '<div style="text-align:center;"><input type="submit" value="';
	$retour .= _T('compteurgraphique:CG_annuler');
	$retour .= '"></div></form>';
    }
    else {
        $requete_tous = "SELECT habillage FROM ".$CG_nom_table." WHERE statut = 6";
        $resultat_tous = spip_query($requete_tous);
        $resultat_tous_tableau = spip_fetch_array($resultat_tous);
        $CG_tous_habillage = $resultat_tous_tableau['habillage'];
        $retour .= _T('compteurgraphique:CG_rubrique_aucun_modele_compteur');
        if (isset($CG_tous_habillage)) {
            $retour .= _T('compteurgraphique:CG_rubrique_modele_compteur_cree');
            for ($m=1;$m<=5;$m++){
                $retour .= '<img src="'.$cheminCG_rel.$CG_tous_habillage.'/'.$m.'.gif">';
            }
	    $retour .= '</div>';
        }
        $retour .= "&nbsp;<br /><hr />";
        $retour .= bouton_block_depliable(_T('compteurgraphique:CG_rubrique_creer_compteur'),false,'');
        $retour .= debut_block_depliable(false);
        $retour .= _T('compteurgraphique:CG_rubrique_stat_compteur');
	$retour .= "&nbsp;<br /><div style='text-align:center;'>Choix de l'habillage :<br /><form method='POST' action='";
	$retour .= generer_url_ecrire($exec,"id_rubrique=$id_rubrique");
	$retour .= "'>";
        $limiteCG = calcule_repertoire_max();
        $nbre_cellules = 5;
        $retour .= '<table border cellpadding="4" align="center">';
        for ($k=1;$k<=$limiteCG;$k++) {
            if (($k%$nbre_cellules)==1){$retour .= '<tr>';}
            $retour .= '<td align="center">';
            if (file_exists($cheminCG_rel.$k.'/8.gif')) {
                $retour .= '<div style="text-align:center;"><img src="'.$cheminCG_rel.$k.'/8.gif"></div><br /><input type="radio" name="nouveau_habillage" value='.$k;
                if ($k==1) {$retour .= ' checked';}
                $retour .= ' >';
            }
            $retour .= '</td>';
            if (($k%$nbre_cellules)==0){$retour .= '</tr>';}
        }
        if (($limiteCG%$nbre_cellules)!=0) {$retour .= '</tr>';}
        $retour .= '</table>';
        $retour .= "<br />"._T('compteurgraphique:CG_choix_nombre_chiffres');
	$retour .= '<select name="nouveau_chiffres"><option value="0" selected>';
	$retour .= _T('compteurgraphique:CG_chiffre_auto');
	$retour .= '</option>';
        for ($n=1;$n<=10;$n++) {
            $retour .= '<option value="'.$n.'">'.$n._T('compteurgraphique:CG_chiffre');
            if ($n!=1){$retour .= _T('compteurgraphique:CG_pluriel');}
            $retour .= '</option>';
        }
	$retour .= '</select><br />&nbsp;<br /><input type="submit" value="';
	$retour .= _T('compteurgraphique:CG_creer');
	$retour .= '"></form></div><br />';
        $retour .= fin_block();
        $retour .= "<br />&nbsp;<br />";
        if (isset($CG_tous_habillage)) {
            $retour .= bouton_block_depliable(_T('compteurgraphique:CG_rubrique_interdire'),false,'');
            $retour .= debut_block_depliable(false);
	    $retour .= '<br /><div style="text-align:center;"><form method="POST" action="';
	    $retour .= generer_url_ecrire($exec,"id_rubrique=$id_rubrique");
	    $retour .= '"><input type="hidden" name="interdiction_compteur" value="2"><input type="submit" value="';
	    $retour .= _T('compteurgraphique:CG_interdire');
	    $retour .= '"></form></div>';
            $retour .= fin_block();
        }
    }
    $retour .= fin_cadre_relief(true);
	return $retour;
}
?>