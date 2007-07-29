<?php

$t=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_COMPTEURGRAPHIQUE',(_DIR_PLUGINS.end($t)));


function balise_COMPTEURGRAPHIQUETOTAL($p) {
    $numero_compteur_graphique = valeur_numerique($p->param[0][1][0]->texte);
    return calculer_balise_dynamique($p,'COMPTEURGRAPHIQUETOTAL',array($numero_compteur_graphique));
}

function balise_COMPTEURGRAPHIQUETOTAL_stat($args,$filtres) {
    return array($args[1]);
}

function balise_COMPTEURGRAPHIQUETOTAL_dyn($num_compt) {
    $CG_ida = $GLOBALS['id_article'];
    $CG_nom_table = "ext_compteurgraphique";
    include_spip('inc/CompteurGraphique_inclusions');
    
    //On récupère le numéro du compteur technique permettant la génération des compteurs
    $requete1 = "SELECT decompte FROM ".$CG_nom_table." WHERE statut = 10";
    $resultat1 = spip_query($requete1);
    $resultat1_tableau = spip_fetch_array($resultat1);
    $CGtechnique = $resultat1_tableau['decompte'];
    
    //Etude du cas où un numéro de compteur est défini dans le paramètre de la balise : statut = 7 : la donnée statut est inutilisée ici
        $requete2 = spip_query("SELECT longueur,habillage FROM ".$CG_nom_table." WHERE statut = 7");
        $resultat2_tableau = spip_fetch_array($requete2);
	$CG_longueur = $resultat2_tableau['longueur'];
	$CG_habillage = $resultat2_tableau['habillage'];
	
        //On vérifie que le compteur n'a pas été supprimé dans la base de données
        if ($CG_longueur!=''){
		$CGstat_result = spip_query("SELECT SUM(visites) AS total_absolu FROM spip_visites");
		if ($CGrow = spip_fetch_array($CGstat_result)) {
			$CG_decompte = $CGrow['total_absolu'];
		}
            $CGtechnique++;
            $CGtechnique = $CGtechnique%100;
            $requete_incrementation_technique = "UPDATE ".$CG_nom_table." SET decompte = ".$CGtechnique." WHERE statut = 10";
            spip_query($requete_incrementation_technique);
            $CG_destruction = ($CGtechnique+50)%100;
            $CG_fichier = "IMG/CompteurGraphique/CompteurGraphique".$CG_destruction.".gif";
            if (file_exists($CG_fichier)) {unlink($CG_fichier);}
            $envoi_final = compteur_graphique_calcul_image($CG_longueur,$CG_decompte,$CG_habillage,$CGtechnique);
            return array('formulaires/compteurgraphiquetotal',0,array('CG'=>$envoi_final));
        }
        //Si suppression : retour d'une chaine vide
        else {
        return array('formulaires/compteurgraphiquetotal',0,array('CG'=>''));
        }
}
?>