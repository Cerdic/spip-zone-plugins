<?php

$t=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_COMPTEURGRAPHIQUE',(_DIR_PLUGINS.end($t)));


function balise_COMPTEURGRAPHIQUE($p) {
    $numero_compteur_graphique = valeur_numerique($p->param[0][1][0]->texte);
    return calculer_balise_dynamique($p,'COMPTEURGRAPHIQUE',array($numero_compteur_graphique));
}

function balise_COMPTEURGRAPHIQUE_stat($args,$filtres) {
    return array($args[1]);
}

function balise_COMPTEURGRAPHIQUE_dyn($num_compt) {
    $CG_ida = $GLOBALS['id_article'];
    $CG_nom_table = "ext_compteurgraphique";
    include_spip('inc/CompteurGraphique_inclusions');
    
    //On récupère le numéro du compteur technique permettant la génération des compteurs
    $requete1 = "SELECT decompte FROM ".$CG_nom_table." WHERE statut = 10";
    $resultat1 = spip_query($requete1);
    $resultat1_tableau = spip_fetch_array($resultat1);
    $CGtechnique = $resultat1_tableau['decompte'];
    
    //Etude du cas où un numéro de compteur est défini dans le paramètre de la balise : statut = 7 : la donnée statut est inutilisée ici
    if ($num_compt!='') {
        $requete2 = "SELECT decompte,longueur,habillage,statut FROM ".$CG_nom_table." WHERE id_compteur =".$num_compt;
        $resultat2 = spip_query($requete2);
        //On vérifie que le compteur n'a pas été supprimé dans la base de données
        if ($resultat2!=''){
            $resultat2_tableau = spip_fetch_array($resultat2);
            $CG_longueur = $resultat2_tableau['longueur'];
            $CG_habillage = $resultat2_tableau['habillage'];
            $CG_decompte = $resultat2_tableau['decompte'];
            $CG_statut = $resultat2_tableau['statut'];
            //On traite ici le cas de l'affichage du nombre total de visites du site
            if ($CG_statut==7) {
                	$CGstat_result = spip_query("SELECT SUM(visites) AS total_absolu FROM spip_visites");
                	if ($CGrow = spip_fetch_array($CGstat_result)) {
		                $CG_decompte = $CGrow['total_absolu'];
	                }
            }
            $CGtechnique++;
            $CGtechnique = $CGtechnique%100;
            $requete_incrementation_technique = "UPDATE ".$CG_nom_table." SET decompte = ".$CGtechnique." WHERE statut = 10";
            spip_query($requete_incrementation_technique);
            $CG_destruction = ($CGtechnique+50)%100;
            $CG_fichier = "IMG/CompteurGraphique/CompteurGraphique".$CG_destruction.".gif";
            if (file_exists($CG_fichier)) {unlink($CG_fichier);}
            if ($CG_statut!=7) {
                $CG_decompte++;
                $requete_incrementation_decompte = "UPDATE ".$CG_nom_table." SET decompte=".$CG_decompte." WHERE id_compteur = ".$num_compt;
                spip_query($requete_incrementation_decompte);
            }
            $envoi_final = compteur_graphique_calcul_image($CG_longueur,$CG_decompte,$CG_habillage,$CGtechnique);
            return array('formulaires/compteurgraphique',0,array('CG'=>$envoi_final));
        }
        //Si suppression : retour d'une chaine vide
        else {
        return array('formulaires/compteurgraphique',0,array('CG'=>''));
        }
    }
    else {
        // Etudes des cas où on est dans un article et le numéro de compteur n'est pas défini
        if (isset($CG_ida)) {
        //On récupère les données concernant l'article en cours
            $requete3 = "SELECT id_rubrique,visites FROM spip_articles WHERE id_article =".$CG_ida;
            $resultat3 = spip_query($requete3);
            $resultat3_tableau = spip_fetch_array($resultat3);
            $CG_idr = $resultat3_tableau['id_rubrique'];
            $CG_vis = $resultat3_tableau['visites'];
            
        //On récupère les stats du jour pour avoir le nombre total réel de visite
        global $aff_jours;
        if (!($aff_jours = intval($aff_jours))) {$aff_jours = 105;}
	    $result=spip_query("SELECT UNIX_TIMESTAMP(date) AS date_unix, visites FROM spip_visites_articles WHERE id_article = ".$CG_ida." AND date > DATE_SUB(NOW(),INTERVAL $aff_jours DAY) ORDER BY date");
	    $date_debut = '';
	    $log = array();
	    while ($row = spip_fetch_array($result)) {
		    $date = $row['date_unix'];
		    if (!$date_debut) $date_debut = $date;
		    $log[$date] = $row['visites'];
	    }
	    if (count($log)>0) {
		    // les visites du jour
		    $date_today = max(array_keys($log));
		    $visites_today = $log[$date_today];
		    // sauf s'il n'y en a pas :
		    if (time()-$date_today>3600*24) {
			    $date_today = time();
			    $visites_today=0;
            }
		}
            
        //On récupère les données de la table des compteurs pour vérifier si l'identifiant de l'article y est défini 
            $requete4 = "SELECT statut,decompte,longueur,habillage FROM ".$CG_nom_table." WHERE id_article =".$CG_ida;
            $resultat4 = spip_query($requete4);
            $resultat4_tableau = spip_fetch_array($resultat4);
            $CG_statut = $resultat4_tableau['statut'];
            $CG_decompte = $resultat4_tableau['decompte'];
            $CG_longueur = $resultat4_tableau['longueur'];
            $CG_habillage = $resultat4_tableau['habillage'];
            //S'il y a une entrée pour l'identifiant de l'article, on fait le traitement pour l'article
            if (isset($CG_statut)) {
            
                    //Premier cas : statut = 1 ; on utilise le compteur de l'article et les statistiques de SPIP
                if ($CG_statut==1) {
                    $CGtechnique++;
                    $CGtechnique = $CGtechnique%100;
                    $requete_incrementation_technique = "UPDATE ".$CG_nom_table." SET decompte = ".$CGtechnique." WHERE statut = 10";
                    spip_query($requete_incrementation_technique);
                    $CG_destruction = ($CGtechnique+50)%100;
                    $CG_fichier = "IMG/CompteurGraphique/CompteurGraphique".$CG_destruction.".gif";
                    if (file_exists($CG_fichier)) {unlink($CG_fichier);}
                    $envoi_final = compteur_graphique_calcul_image($CG_longueur,$CG_vis+$visites_today,$CG_habillage,$CGtechnique);
                    return array('formulaires/compteurgraphique',0,array('CG'=>$envoi_final));
                    }
                        
                    //Second cas : statut = 2 ; on envoie des statistiques personnalisées gérées par le champ 'decompte' qui est alors incrémenté
                if ($CG_statut==2) {
                    $CGtechnique++;
                    $CGtechnique = $CGtechnique%100;
                    $requete_incrementation_technique = "UPDATE ".$CG_nom_table." SET decompte = ".$CGtechnique." WHERE statut = 10";
                    spip_query($requete_incrementation_technique);
                    $CG_destruction = ($CGtechnique+50)%100;
                    $CG_fichier = "IMG/CompteurGraphique/CompteurGraphique".$CG_destruction.".gif";
                    if (file_exists($CG_fichier)) {unlink($CG_fichier);}
                    $CG_decompte++;
                    $requete_incrementation_decompte = "UPDATE ".$CG_nom_table." SET decompte = ".$CG_decompte." WHERE id_article = ".$CG_ida;
                    spip_query($requete_incrementation_decompte);
                    $envoi_final = compteur_graphique_calcul_image($CG_longueur,$CG_decompte,$CG_habillage,$CGtechnique);
                    return array('formulaires/compteurgraphique',0,array('CG'=>$envoi_final));
                    }                        
                        
                    //Troisième cas : statut = 3 ; l'administrateur a désactivé le compteur de visite pour l'article, on renvoie une chaine vide
                if ($CG_statut==3) {
                        return array('formulaires/compteurgraphique',0,array('CG'=>''));
                }
            }
            //Sinon, on réalise un traitement pour la rubrique
            else {
                $requete5 = "SELECT statut,decompte,longueur,habillage FROM ".$CG_nom_table." WHERE id_rubrique = ".$CG_idr;
                $resultat5 = spip_query($requete5);
                $resultat5_tableau= spip_fetch_array($resultat5);
                $CG_statut = $resultat5_tableau['statut'];
                $CG_decompte = $resultat5_tableau['decompte'];
                $CG_longueur = $resultat5_tableau['longueur'];
                $CG_habillage = $resultat5_tableau['habillage'];
                if (isset($CG_statut)) {

                    // Premier cas : statut = 4 : tous les articles de la rubrique ont un compteur défini : on envoie les statistiques de SPIP
                    if ($CG_statut==4) { 
                        $CGtechnique++;
                        $CGtechnique = $CGtechnique%100;
                        $requete_incrementation_technique = "UPDATE ".$CG_nom_table." SET decompte = ".$CGtechnique." WHERE statut = 10";
                        spip_query($requete_incrementation_technique);
                        $CG_destruction = ($CGtechnique+50)%100;
                        $CG_fichier = "IMG/CompteurGraphique/CompteurGraphique".$CG_destruction.".gif";
                        if (file_exists($CG_fichier)) {unlink($CG_fichier);}
                        $envoi_final = compteur_graphique_calcul_image($CG_longueur,$CG_vis+$visites_today,$CG_habillage,$CGtechnique);
                        return array('formulaires/compteurgraphique',0,array('CG'=>$envoi_final));
                    }
                    // Second cas : statut = 5 : l'administrateur a désactivé le compteur pour tous les articles de la rubrique : on envoie une chaine vide :
                    if ($CG_statut==5) {
                        return array('formulaires/compteurgraphique',0,array('CG'=>''));
                    }
                }
            
            //Enfin, s'il n'y a pas d'entrée article, ni rubrique, on cherche s'il y a un modèle de compteur défini pour tous les articles (statut = 6)
                else {
                    $requete6 = "SELECT longueur,habillage FROM ".$CG_nom_table." WHERE statut = 6";
                    $resultat6= spip_query($requete6);
                    $resultat6_tableau = spip_fetch_array($resultat6);
                    $CG_longueur = $resultat6_tableau['longueur'];
                    $CG_habillage = $resultat6_tableau['habillage'];
                    if (isset($CG_longueur)) {
                        $CGtechnique++;
                        $CGtechnique = $CGtechnique%100;
                        $requete_incrementation_technique = "UPDATE ".$CG_nom_table." SET decompte = ".$CGtechnique." WHERE statut = 10";
                        spip_query($requete_incrementation_technique);
                        $CG_destruction = ($CGtechnique+50)%100;
                        $CG_fichier = "IMG/CompteurGraphique/CompteurGraphique".$CG_destruction.".gif";
                        if (file_exists($CG_fichier)) {unlink($CG_fichier);}
                        $envoi_final = compteur_graphique_calcul_image($CG_longueur,$CG_vis+$visites_today,$CG_habillage,$CGtechnique);
                        return array('formulaires/compteurgraphique',0,array('CG'=>$envoi_final));
                    }
                }
            }
        }
    //Si on n'est pas dans un article et que le numéro de compteur n'est pas défini dans le paramètre de la balise (erreur du webmestre), alors on renvoie une chaine vide
        else {
            return array('formulaires/compteurgraphique',0,array('CG'=>''));
        }
    }
}
?>