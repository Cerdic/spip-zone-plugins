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
    $CG_nom_table = "spip_compteurgraphique";
    include_spip('inc/CompteurGraphique_inclusions');
    
    //On r�cup�re le num�ro du compteur technique permettant la g�n�ration des compteurs
    $resultat1 = sql_select("decompte",$CG_nom_table,"statut = 10");
    $resultat1_tableau = sql_fetch($resultat1);
    $CGtechnique = $resultat1_tableau['decompte'];
    
    //Etude du cas o� un num�ro de compteur est d�fini dans le param�tre de la balise : statut = 7 : la donn�e statut est inutilis�e ici
        $requete2 = sql_select("longueur,habillage",$CG_nom_table,"statut = 7");
        $resultat2_tableau = sql_fetch($requete2);
	$CG_longueur = $resultat2_tableau['longueur'];
	$CG_habillage = $resultat2_tableau['habillage'];
	
        //On v�rifie que le compteur n'a pas �t� supprim� dans la base de donn�es
        if ($CG_longueur!=''){
		$CGstat_result = sql_query("SELECT SUM(visites) AS total_absolu FROM spip_visites");
		if ($CGrow = sql_fetch($CGstat_result)) {
			$CG_decompte = $CGrow['total_absolu'];
		}
            $CGtechnique++;
            $CGtechnique = $CGtechnique%100;
            sql_updateq($CG_nom_table,array("decompte" => $CGtechnique),"statut = 10");
            $CG_destruction = ($CGtechnique+50)%100;
            $CG_fichier = _DIR_IMG."CompteurGraphique/CompteurGraphique".$CG_destruction.".gif";
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