<?php

function calcule_repertoire_max() {
$cheminCG_rel="../lib/compteurgraphique_pack/";
$icone = "../"._DIR_PLUGIN_COMPTEURGRAPHIQUE."/img_pack/CompteurGraphique.gif";
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

function CompteurGraphique_ArticleGauche ($exec) {
$verif_presence_table = spip_query("SELECT id_compteur FROM ext_compteurgraphique WHERE statut=10");
if ($verif_presence_table == '') {return;}
$retour ='';
$CG_nom_table = "ext_compteurgraphique";
$cheminCG_rel="../lib/compteurgraphique_pack/";
$icone = "../"._DIR_PLUGIN_COMPTEURGRAPHIQUE."/img_pack/CompteurGraphique.gif";
if ((isset($_GET['id_article'])) AND is_numeric($_GET['id_article'])) {$id_article=$_GET['id_article'];}
if (isset($id_article)) {

if (isset($_POST['choix_habillage']) && isset($_POST['valid_decompte']) && isset($_POST['changement_chiffres2']) && is_numeric($_POST['changement_chiffres2'])) {
    if ($_POST['valid_decompte']==2) {$choix_decompte="decompte=".$_POST['choix_decompte'].","; $statut=2;} else {$statut=1;}
    $requete_reactivation = "UPDATE ".$CG_nom_table." SET ".$choix_decompte."statut=".$statut.",longueur=".$_POST['changement_chiffres2'].",habillage=".$_POST['choix_habillage']." WHERE id_article = ".$id_article;
    $resultat_reactivation = spip_query($requete_reactivation);
}

if (isset($_POST['changement_habillage']) && is_numeric($_POST['changement_habillage'])) {
    $requete_changement_habillage = "UPDATE ".$CG_nom_table." SET habillage = ".$_POST['changement_habillage']." WHERE id_article = ".$id_article;
    $resultat_changement_habillage = spip_query($requete_changement_habillage);
}

if (isset($_POST['modif_decompte_decompte']) && is_numeric($_POST['modif_decompte_decompte'])) {
    $requete_changement_decompte = "UPDATE ".$CG_nom_table." SET decompte = ".$_POST['modif_decompte_decompte']." WHERE id_article = ".$id_article;
    $resultat_changement_decompte = spip_query($requete_changement_decompte);
}

if (isset($_POST['transition_decompte']) && is_numeric($_POST['transition_decompte'])) {
    $requete_transition_decompte = "UPDATE ".$CG_nom_table." SET decompte=".$_POST['transition_decompte'].",statut=2 WHERE id_article = ".$id_article;
    $resultat_transition_decompte = spip_query($requete_transition_decompte);
}

if (isset($_POST['changement_chiffres']) && is_numeric($_POST['changement_chiffres'])) {
    $requete_changement_chiffres = "UPDATE ".$CG_nom_table." SET longueur = ".$_POST['changement_chiffres']." WHERE id_article = ".$id_article;
    $resultat_changement_chiffres = spip_query($requete_changement_chiffres);
}

if (isset($_POST['suppression_compteur']) && ($_POST['suppression_compteur']==1)) {
    $requete_suppr_compteur = "DELETE FROM ".$CG_nom_table." WHERE id_article=".$id_article;
    $resultat_suppr_compteur=spip_query($requete_suppr_compteur);
}
    
if (isset($_POST['interdiction_compteur']) && ($_POST['interdiction_compteur']==1)) {
    $requete_interdiction_compteur = "UPDATE ".$CG_nom_table." SET statut = 3 WHERE id_article = ".$id_article;
    $resultat_interdiction_compteur = spip_query($requete_interdiction_compteur);
}

if (isset($_POST['interdiction_compteur']) && ($_POST['interdiction_compteur']==2)) {
    $requete_interdiction_compteur = "INSERT INTO ".$CG_nom_table." VALUES (NULL,NULL,".$id_article.",NULL,3,NULL,NULL)";
    $resultat_interdiction_compteur = spip_query($requete_interdiction_compteur);
}

if (isset($_POST['transition_visites']) && ($_POST['transition_visites']==1)) {
    $requete_transition_visites = "UPDATE ".$CG_nom_table." SET statut = 1 WHERE id_article = ".$id_article;
    $resultat_transition_visites = spip_query($requete_transition_visites);
}

if (isset($_POST['nouveau_chiffres']) && is_numeric($_POST['nouveau_decompte']) && is_numeric($_POST['nouveau_chiffres']) && is_numeric($_POST['nouveau_habillage'])) {
    if ($_POST['nouveau_decompte']==1) {$CG_dec="NULL";} else {$CG_dec=$_POST['choix_decompte'];}
    $requete_nouveau_compteur = "INSERT INTO ".$CG_nom_table." VALUES (NULL,".$CG_dec.",".$id_article.",NULL,".$_POST['nouveau_decompte'].",".$_POST['nouveau_chiffres'].",".$_POST['nouveau_habillage'].")";
    $resultat_nouveau_compteur = spip_query($requete_nouveau_compteur);
}

//Récupération d'une éventuelle entrée pour cet article dans la table du compteur
$requete1 = "SELECT id_compteur,decompte,statut,longueur,habillage FROM ".$CG_nom_table." WHERE id_article = ".$id_article;
$resultat1 = spip_query($requete1);
$resultat1_tableau = spip_fetch_array($resultat1);
$CG_id_compteur = $resultat1_tableau['id_compteur'];
$CG_statut = $resultat1_tableau['statut'];
$CG_decompte = $resultat1_tableau['decompte'];
$CG_longueur = $resultat1_tableau['longueur'];
$CG_habillage = $resultat1_tableau['habillage'];

//On récupère les données concernant l'article en cours
$requete3 = "SELECT id_rubrique,visites FROM spip_articles WHERE id_article =".$id_article;
$resultat3 = spip_query($requete3);
$resultat3_tableau = spip_fetch_array($resultat3);
$CG_idr = $resultat3_tableau['id_rubrique'];
$CG_vis = $resultat3_tableau['visites'];

$retour .= debut_cadre_relief($icone,true,'', _T('compteurgraphique:CG_article_cet_article'));

if (isset($CG_statut)) {

    //On traite le cas d'un compteur défini pour l'article avec utilisation des statistiques de SPIP
    if ($CG_statut==1) {
        $retour .= _T('compteurgraphique:CG_article_modele_choisi');
        $retour .= "<div style='text-align:center;'>"._T('compteurgraphique:CG_habillage_choisi');
        for ($m=1;$m<=5;$m++){
            $retour .= '<img src="'.$cheminCG_rel.$CG_habillage.'/'.$m.'.gif">';
        }
        $retour .= "<br />&nbsp;<br />";
        if ($CG_longueur==0) {$retour .= _T('compteurgraphique:CG_gestion_chiffres_automatique');}
            else {
                $retour .= $CG_longueur." ";
                if ($CG_longueur==1) {$retour .= _T('compteurgraphique:CG_article_chiffre_affiche');}
                else {$retour .= _T('compteurgraphique:CG_article_chiffres_affiches');}
                $retour .= _T('compteurgraphique:CG_article_modele_chiffre');
            }
        $retour .= "</div>";
        
        $retour .= bouton_block_depliable(_T('compteurgraphique:CG_modif_habillage'),false,'');
        $retour .= debut_block_depliable(false);
        $retour .= "<br /><div style='text-align:center;'>";
        $retour .= '<form method="POST" action="'.generer_url_ecrire($exec,"id_article=$id_article").'">';
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
	$retour .= generer_url_ecrire($exec,"id_article=$id_article");
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

        $retour .= bouton_block_depliable(_T('compteurgraphique:CG_article_choix_decompte'),false,'');
        $retour .= debut_block_depliable(false);
        $retour .= _T('compteurgraphique:CG_article_decompte_perso');
	$retour .= '<div style="text-align:center;"><form method="POST" action="';
	$retour .= generer_url_ecrire($exec,"id_article=$id_article");
	$retour .= '"><input type="text" name="transition_decompte" value="0"><input type="submit" value="';
	$retour .= _T('compteurgraphique:CG_valider');
	$retour .= '"></form></div>';
        $retour .= fin_block();
        $retour .= "<br />&nbsp;<br />";

        $retour .= bouton_block_depliable(_T('compteurgraphique:CG_article_interdire_compteur'),false,'');
        $retour .= debut_block_depliable(false);
	$retour .= '<br /><center><form method="POST" action="';
	$retour .= generer_url_ecrire($exec,"id_article=$id_article");
	$retour .= '"><input type="hidden" name="interdiction_compteur" value="1"><input type="submit" value="';
	$retour .= _T('compteurgraphique:CG_interdire');
	$retour .= '"></form></center>';
        $retour .= fin_block();
        $retour .= "<br />&nbsp;<br />";

        $retour .= bouton_block_depliable(_T('compteurgraphique:CG_article_suppr_compteur'),false,'');
        $retour .= debut_block_depliable(false);
	$retour .= '<br /><div style="text-align:center;"><form method="POST" action="';
	$retour .= generer_url_ecrire($exec,"id_article=$id_article");
	$retour .= '"><input type="hidden" name="suppression_compteur" value="1"><input type="submit" value="';
	$retour .= _T('compteurgraphique:CG_suppr');
	$retour .= '"></form></div>';
        $retour .= fin_block();
    }
    
    //On traite le cas d'un compteur défini pour l'article avec utilisation d'un décompte personnalisé
    elseif ($CG_statut==2) {
        $retour .= _T('compteurgraphique:CG_article_modele_choisi');
        $retour .= '<div style="text-align:center;">'._T('compteurgraphique:CG_article_decompte_est').'<div style="font-weight:bold">'.$CG_decompte.'</div><br />';
        $retour .= _T('compteurgraphique:CG_habillage_choisi');
        for ($m=1;$m<=5;$m++){
            $retour .= '<img src="'.$cheminCG_rel.$CG_habillage.'/'.$m.'.gif">';
        }
        $retour .= "<br />&nbsp;<br />";
        if ($CG_longueur==0) {$retour .= _T('compteurgraphique:CG_gestion_chiffres_automatique');}
            else {
                $retour .= $CG_longueur." ";
                if ($CG_longueur==1) {$retour .= _T('compteurgraphique:CG_article_chiffre_affiche');}
                else {$retour .= _T('compteurgraphique:CG_article_chiffres_affiches');}
                $retour .= _T('compteurgraphique:CG_article_modele_chiffre');
            }
        $retour .= "</div>";
        
        $retour .= bouton_block_depliable(_T('compteurgraphique:CG_modif_habillage'),false,'');
        $retour .= debut_block_depliable(false);
        $retour .= "<br /><div style='text-align:center;'>";
	$retour .= '<form method="POST" action="';
	$retour .= generer_url_ecrire($exec,"id_article=$id_article").'">';
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
	
	$retour .= bouton_block_depliable(_T('compteurgraphique:CG_modif_decompte'),false,'');
        $retour .= debut_block_depliable(false);
	$retour .= '<br /><div style="text-align:center;"><form method="POST" action="';
	$retour .= generer_url_ecrire($exec,"id_article=$id_article");
	$retour .= '"><input type="text" name="modif_decompte_decompte" value="'.$CG_decompte.'">';
	$retour .= '<br />&nbsp;<br /><input type="submit" value="';
	$retour .= _T('compteurgraphique:CG_modif');
	$retour .= '"></form></div>';
        $retour .= fin_block();
	$retour .= "<br />&nbsp;<br />";
        
        $retour .= bouton_block_depliable(_T('compteurgraphique:CG_modif_nombre_chiffres'),false,'');
        $retour .= debut_block_depliable(false);
	$retour .= '<br /><div style="text-align:center;"><form method="POST" action="';
	$retour .= generer_url_ecrire($exec,"id_article=$id_article");
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

        $retour .= bouton_block_depliable(_T('compteurgraphique:CG_article_utilise_stat'),false,'');
        $retour .= debut_block_depliable(false);
	$retour .= '<br /><div style="text-align:center;"><form method="POST" action="';
	$retour .= generer_url_ecrire($exec,"id_article=$id_article");
	$retour .= '"><input type="hidden" name="transition_visites" value="1"><input type="submit" value="';
	$retour .= _T('compteurgraphique:CG_modif');
	$retour .= '"></form></div>';
        $retour .= fin_block();
        $retour .= "<br />&nbsp;<br />";

        $retour .= bouton_block_depliable(_T('compteurgraphique:CG_article_interdire_compteur'),false,'');
        $retour .= debut_block_depliable(false);
	$retour .= '<br /><div style="text-align:center;"><form method="POST" action="';
	$retour .= generer_url_ecrire($exec,"id_article=$id_article");
	$retour .= '"><input type="hidden" name="interdiction_compteur" value="1"><input type="submit" value="';
	$retour .= _T('compteurgraphique:CG_interdire');
	$retour .= '"></form></div>';
        $retour .= fin_block();
        $retour .= "<br />&nbsp;<br />";
        
        $retour .= bouton_block_depliable(_T('compteurgraphique:CG_article_suppr_compteur'),false,'');
        $retour .= debut_block_depliable(false);
	$retour .= '<br /><center><form method="POST" action="';
	$retour .= generer_url_ecrire($exec,"id_article=$id_article");
	$retour .= '"><input type="hidden" name="suppression_compteur" value="1"><input type="submit" value="';
	$retour .= _T('compteurgraphique:CG_suppr');
	$retour .= '"></form></center>';
        $retour .= fin_block();
    }
    
    //On traite le cas d'un compteur dont l'affichage est interdit pour l'article
    elseif ($CG_statut==3) {
        $retour .= _T('compteurgraphique:CG_article_compteur_desactive');
        $retour .= bouton_block_depliable(_T('compteurgraphique:CG_article_reactive_compteur'),false,'');
        $retour .= debut_block_depliable(false);
        $retour .= "<br /><div style='text-align:center;'>";
	$retour .= '<form method="POST" action="';
	$retour .= generer_url_ecrire($exec,"id_article=$id_article").'">';
        $limiteCG = calcule_repertoire_max();
        $nbre_cellules=5;
        $retour .= '<table border cellpadding="4" align="center">';
        for ($k=1;$k<=$limiteCG;$k++) {
            if (($k%$nbre_cellules)==1){$retour .= '<tr>';}
            $retour .= '<td align="center">';
            if (file_exists($cheminCG_rel.$k.'/8.gif')) {
                $retour .= '<div style="text-align:center;"><img src="'.$cheminCG_rel.$k.'/8.gif"></div><br /><input type="radio" name="choix_habillage" value='.$k;
                if ($k==1) {$retour .= ' checked';}
                $retour .= ' >';
            }
            $retour .= '</td>';
            if (($k%$nbre_cellules)==0){$retour .= '</tr>';}
        }
        if (($limiteCG%$nbre_cellules)!=0) {$retour .= '</tr>';}
        $retour .= '</table><hr />&nbsp;<br /></div>';
        $retour .= _T('compteurgraphique:CG_article_utilise_decompte');
	$retour .= '<input type="radio" name="valid_decompte" value=1 checked>';
	$retour .= _T('compteurgraphique:CG_article_utilise_stat_spip');
	$retour .= '<br />&nbsp;<br /><input type="radio" name="valid_decompte" value=2>';
        $retour .= _T('compteurgraphique:CG_article_utilise_decompte_perso');
	$retour .= '<div style="text-align:center;"><input type="text" name="choix_decompte" value="0"><hr />&nbsp;<br />';
	$retour .= _T('compteurgraphique:CG_choix_nombre_chiffres');
	$retour .= '<select name="changement_chiffres2"><option value="0" selected>';
	$retour .= _T('compteurgraphique:CG_chiffre_auto');
	$retour .= '</option>';
	        for ($n=1;$n<=10;$n++) {
                    $retour .= '<option value="'.$n.'">'.$n._T('compteurgraphique:CG_chiffre');
                    if ($n!=1){$retour .= _T('compteurgraphique:CG_pluriel');}
                    $retour .= '</option>';
                }
	$retour .= '</select><br />&nbsp;<br /><input type="submit" value="';
	$retour .= _T('compteurgraphique:CG_chiffre_auto');
	$retour .= '"></div></form>';
        $retour .= fin_block();
        $retour .= "<br />&nbsp;<br />";

        $retour .= bouton_block_depliable(_T('compteurgraphique:CG_article_suppr_actif_desactive'),false,'');
        $retour .= debut_block_depliable(false);
        $retour .= "<br /><div style='text-align:center;'>";
	$retour .= '<form method="POST" action="';
	$retour .= generer_url_ecrire($exec,"id_article=$id_article").'">';
	$retour .= '<input type="hidden" name="suppression_compteur" value="1"><input type="submit" value="';
	$retour .= _T('compteurgraphique:CG_suppr');
	$retour .= '"></form></div>';
        $retour .= fin_block();
        $retour .= "<br />";
    }
}

else {
    $requete_rub = "SELECT statut,longueur,habillage FROM ".$CG_nom_table." WHERE id_rubrique = ".$CG_idr;
    $resultat_rub = spip_query($requete_rub);
    $resultat_rub_tableau = spip_fetch_array($resultat_rub);
    $CG_rub_statut = $resultat_rub_tableau['statut'];
    $CG_rub_longueur = $resultat_rub_tableau['longueur'];
    $CG_rub_habillage = $resultat_rub_tableau['habillage'];
    
    $requete_tous = "SELECT habillage FROM ".$CG_nom_table." WHERE statut = 6";
    $resultat_tous = spip_query($requete_tous);
    $resultat_tous_tableau = spip_fetch_array($resultat_tous);
    $CG_tous_habillage = $resultat_tous_tableau['habillage'];
    
    if ($CG_rub_statut==4){
        $retour .= _T('compteurgraphique:CG_article_aucun_compteur_cree_rubrique')."<div style='text-align:center;'>";
        for ($m=1;$m<=5;$m++){
            $retour .= '<img src="'.$cheminCG_rel.$CG_rub_habillage.'/'.$m.'.gif">';
        }
        $retour .= "</div><hr />&nbsp;<br />";
    }
    elseif ($CG_rub_statut==5){
        $retour .= _T('compteurgraphique:CG_article_aucun_compteur_cree_desactive');
    }
    elseif (isset($CG_tous_habillage)){
        $retour .= _T('compteurgraphique:CG_article_aucun_compteur_cree_site').'<div style="text-align:center;">';
        for ($m=1;$m<=5;$m++){
            $retour .= '<img src="'.$cheminCG_rel.$CG_tous_habillage.'/'.$m.'.gif">';
        }
        $retour .= "</div><hr />&nbsp;<br />";
    }
    else {
        $retour .= _T('compteurgraphique:CG_article_aucun_compteur_cree');
    }

    $retour .= bouton_block_depliable(_T('compteurgraphique:CG_article_creer_compteur'),false,'');
    $retour .= debut_block_depliable(false);
    $retour .= '<br /><div style="text-align:center;">';
    $retour .= '<form method="POST" action="';
    $retour .= generer_url_ecrire($exec,"id_article=$id_article").'">';
    $limiteCG = calcule_repertoire_max();
    $nbre_cellules=5;
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
    $retour .= '</table><hr />&nbsp;<br /></div>';
    $retour .= _T('compteurgraphique:CG_article_utilise_decompte');
    $retour .= '<input type="radio" name="nouveau_decompte" value=1 checked>';
    $retour .= _T('compteurgraphique:CG_article_utilise_stat_spip');
    $retour .= '<br />&nbsp;<br /><input type="radio" name="nouveau_decompte" value=2>';
    $retour .= _T('compteurgraphique:CG_article_utilise_decompte_perso');
    $retour .= '<div style="text-align:center;"><input type="text" name="choix_decompte" value="0"><hr />&nbsp;<br />';
    $retour .= _T('compteurgraphique:CG_choix_nombre_chiffres');
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
	$retour .= '"></div></form>';
    $retour .= fin_block()."<br />&nbsp;<br />";
    
    $retour .= bouton_block_depliable(_T('compteurgraphique:CG_article_interdire_compteur'),false,'');
    $retour .= debut_block_depliable(false);
    $retour .= '<br /><center><form method="POST" action="';
    $retour .= generer_url_ecrire($exec,"id_article=$id_article");
    $retour .= '"><input type="hidden" name="interdiction_compteur" value="2"><input type="submit" value="';
    $retour .= _T('compteurgraphique:CG_interdire');
    $retour .= '"></form></center>';
    $retour .= fin_block()."<br />&nbsp;<br />";
}}
$retour .= fin_cadre_relief(true);
return $retour;
}
?>