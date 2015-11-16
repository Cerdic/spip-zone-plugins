<?php
function formulaires_compteurgraphiquearticleeva_charger_dist($id_article){

    $CG_ida = $id_article;
    $CG_nom_table = "spip_compteurgraphique";
    include_spip('inc/CompteurGraphique_inclusions');
    
    //On récupère le numéro du compteur technique permettant la génération des compteurs
    $resultat1 = sql_select("decompte",$CG_nom_table,"statut = 10");
    $resultat1_tableau = sql_fetch($resultat1);
    $CGtechnique = $resultat1_tableau['decompte'];
    
    //Etude du cas où un numéro de compteur est défini dans le paramètre de la balise : statut = 7 : la donnée statut est inutilisée ici
    if ($num_compt!='') {
        $resultat2 = sql_select("decompte,longueur,habillage,statut",$CG_nom_table,"id_compteur = $num_compt");
        //On vérifie que le compteur n'a pas été supprimé dans la base de données
        if ($resultat2!=''){
            $resultat2_tableau = sql_fetch($resultat2);
            $CG_longueur = $resultat2_tableau['longueur'];
            $CG_habillage = $resultat2_tableau['habillage'];
            $CG_decompte = $resultat2_tableau['decompte'];
            $CG_statut = $resultat2_tableau['statut'];
            //On traite ici le cas de l'affichage du nombre total de visites du site
            if ($CG_statut==7) {
                	$CGstat_result = sql_query("SELECT SUM(visites) AS total_absolu FROM spip_visites");
                	if ($CGrow = sql_fetch($CGstat_result)) {
		                $CG_decompte = $CGrow['total_absolu'];
	                }
            }
            $CGtechnique++;
            $CGtechnique = $CGtechnique%100;
            sql_updateq($CG_nom_table,array("decompte" => $CGtechnique),"statut = 10");
            $CG_destruction = ($CGtechnique+50)%100;
            $CG_fichier = _DIR_IMG."CompteurGraphique/CompteurGraphique".$CG_destruction.".gif";
            if (file_exists($CG_fichier)) {unlink($CG_fichier);}
            if ($CG_statut!=7) {
                $CG_decompte++;
                sql_updateq($CG_nom_table,array("decompte" => $CG_decompte),"id_compteur = $num_compt");
            }
            $envoi_final = compteur_graphique_calcul_image($CG_longueur,$CG_decompte,$CG_habillage,$CGtechnique);
            return array('compteurgraphiquearticleeva'=>$envoi_final);
        }
        //Si suppression : retour d'une chaine vide
        else {
        return array('compteurgraphiquearticleeva'=>'');
        }
    }
    else {
        // Etudes des cas où on est dans un article et le numéro de compteur n'est pas défini
        if (isset($CG_ida)) {
        //On récupère les données concernant l'article en cours
            $resultat3 = sql_select("id_rubrique,visites","spip_articles","id_article = $CG_ida");
            $resultat3_tableau = sql_fetch($resultat3);
            $CG_idr = $resultat3_tableau['id_rubrique'];
            $CG_vis = $resultat3_tableau['visites'];
            
        //On récupère les stats du jour pour avoir le nombre total réel de visite
        global $aff_jours;
        if (!($aff_jours = intval($aff_jours))) {$aff_jours = 105;}
	    $result=sql_query("SELECT UNIX_TIMESTAMP(date) AS date_unix, visites FROM spip_visites_articles WHERE id_article = ".$CG_ida." AND date > DATE_SUB(NOW(),INTERVAL $aff_jours DAY) ORDER BY date");
	    $date_debut = '';
	    $log = array();
	    while ($row = sql_fetch($result)) {
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
            $resultat4 = sql_select("statut,decompte,longueur,habillage",$CG_nom_table,"id_article = $CG_ida");
            $resultat4_tableau = sql_fetch($resultat4);
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
                    sql_updateq($CG_nom_table,array("decompte" => $CGtechnique),"statut = 10");
                    $CG_destruction = ($CGtechnique+50)%100;
                    $CG_fichier = _DIR_IMG."CompteurGraphique/CompteurGraphique".$CG_destruction.".gif";
                    if (file_exists($CG_fichier)) {unlink($CG_fichier);}
                    $envoi_final = compteur_graphique_calcul_image($CG_longueur,$CG_vis+$visites_today,$CG_habillage,$CGtechnique);
                    return array('compteurgraphiquearticleeva'=>$envoi_final);
                    }
                        
                    //Second cas : statut = 2 ; on envoie des statistiques personnalisées gérées par le champ 'decompte' qui est alors incrémenté
                if ($CG_statut==2) {
                    $CGtechnique++;
                    $CGtechnique = $CGtechnique%100;
                    sql_updateq($CG_nom_table,array("decompte" => $CGtechnique),"statut = 10");
                    $CG_destruction = ($CGtechnique+50)%100;
                    $CG_fichier = _DIR_IMG."CompteurGraphique/CompteurGraphique".$CG_destruction.".gif";
                    if (file_exists($CG_fichier)) {unlink($CG_fichier);}
                    $CG_decompte++;
                    sql_updateq($CG_nom_table,array("decompte" => $CG_decompte),"id_article = $CG_ida");
                    $envoi_final = compteur_graphique_calcul_image($CG_longueur,$CG_decompte,$CG_habillage,$CGtechnique);
                    return array('compteurgraphiquearticleeva'=>$envoi_final);
                    }                        
                        
                    //Troisième cas : statut = 3 ; l'administrateur a désactivé le compteur de visite pour l'article, on renvoie une chaine vide
                if ($CG_statut==3) {
                        return array('compteurgraphiquearticleeva'=>'');
                }
            }
            //Sinon, on réalise un traitement pour la rubrique
            else {
                $resultat5 = sql_select("statut,decompte,longueur,habillage",$CG_nom_table,"id_rubrique = $CG_idr");
                $resultat5_tableau= sql_fetch($resultat5);
                $CG_statut = $resultat5_tableau['statut'];
                $CG_decompte = $resultat5_tableau['decompte'];
                $CG_longueur = $resultat5_tableau['longueur'];
                $CG_habillage = $resultat5_tableau['habillage'];
                if (isset($CG_statut)) {

                    // Premier cas : statut = 4 : tous les articles de la rubrique ont un compteur défini : on envoie les statistiques de SPIP
                    if ($CG_statut==4) { 
                        $CGtechnique++;
                        $CGtechnique = $CGtechnique%100;
                        sql_updateq($CG_nom_table,array("decompte" => $CGtechnique),"statut = 10");
                        $CG_destruction = ($CGtechnique+50)%100;
                        $CG_fichier = _DIR_IMG."CompteurGraphique/CompteurGraphique".$CG_destruction.".gif";
                        if (file_exists($CG_fichier)) {unlink($CG_fichier);}
                        $envoi_final = compteur_graphique_calcul_image($CG_longueur,$CG_vis+$visites_today,$CG_habillage,$CGtechnique);
                        return array('compteurgraphiquearticleeva'=>$envoi_final);
                    }
                    // Second cas : statut = 5 : l'administrateur a désactivé le compteur pour tous les articles de la rubrique : on envoie une chaine vide :
                    if ($CG_statut==5) {
                        return array('compteurgraphiquearticleeva'=>'');
                    }
                }
            
            //Enfin, s'il n'y a pas d'entrée article, ni rubrique, on cherche s'il y a un modèle de compteur défini pour tous les articles (statut = 6)
                else {
                    $resultat6= sql_select("longueur,habillage",$CG_nom_table,"statut = 6");
                    $resultat6_tableau = sql_fetch($resultat6);
                    $CG_longueur = $resultat6_tableau['longueur'];
                    $CG_habillage = $resultat6_tableau['habillage'];
                    if (isset($CG_longueur)) {
                        $CGtechnique++;
                        $CGtechnique = $CGtechnique%100;
                        sql_updateq($CG_nom_table,array("decompte" => $CGtechnique),"statut = 10");
                        $CG_destruction = ($CGtechnique+50)%100;
                        $CG_fichier = _DIR_IMG."CompteurGraphique/CompteurGraphique".$CG_destruction.".gif";
                        if (file_exists($CG_fichier)) {unlink($CG_fichier);}
                        $envoi_final = compteur_graphique_calcul_image($CG_longueur,$CG_vis+$visites_today,$CG_habillage,$CGtechnique);
                        return array('compteurgraphiquearticleeva'=>$envoi_final);
                    }
		    else {return array('compteurgraphiquearticleeva'=>'');}
                }
            }
        }
    //Si on n'est pas dans un article et que le numéro de compteur n'est pas défini dans le paramètre de la balise (erreur du webmestre), alors on renvoie une chaine vide
        else {
            return array('compteurgraphiquearticleeva'=>'');
        }
    }
}
?>