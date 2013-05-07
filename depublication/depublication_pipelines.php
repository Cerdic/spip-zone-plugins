<?php
/******************************************************************************************
 * Dépublication permet de dépublier un article à une date donnée.						  *
 * Copyright (C) 2005-2010 Nouveaux Territoires support<at>nouveauxterritoires.fr		  *
 * http://www.nouveauxterritoires.fr							    					  *
 *                                                                                        *
 * Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes *
 * de la Licence Publique Générale GNU publiée par la Free Software Foundation            *
 * (version 3).                                                                           *
 *                                                                                        *
 * Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       *
 * ni explicite ni implicite, y compris les garanties de commercialisation ou             *
 * d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  *
 * pour plus de détails.                                                                  *
 *                                                                                        *
 * Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    *
 * en même temps que ce programme ; si ce n'est pas le cas,								  * 
 * regardez http://www.gnu.org/licenses/ 												  *
 * ou écrivez à la	 																	  *
 * Free Software Foundation,                                                              *
 * Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   *
 ******************************************************************************************/


function depublication_execution($flux) {
	global $connect_statut;
	
	//determine la page demandée 
	switch ($flux['args']['exec']) {
		//la page articles est demandée
		case "articles" :
			//charge les fonctions necessaire
			include_once('inc/depublication_articles.php');
			$id_article = $flux['args']['id_article'];
			//recupere le complement d'affichage
			$flux['data'] .= inc_depublication_articles_dist($id_article,$flag, $connect_statut);
			break;
			
		case "auteur_infos" :
			//charge les fonctions necessaire
			include_once('inc/depublication_auteurs.php');
			$id_auteur = $flux['args']['id_auteur'];
			//recupere le complement d'affichage
			$flux['data'] .= inc_depublication_auteurs_dist($id_auteur,$flag ,$connect_statut);
			break;
			
		default :
			break;
	}
	
	//retourne l'affichage complet
	return $flux;
}
function depublication_taches_generales_cron($taches) {

	$taches['depublication'] = 600; // par exemple toutes les 10 minutes, ne pas descendre en dessous de 30 secondes !
	spip_log('execution tache cron dépublication','depublication');
	return $taches;
}


function depublication_header_prive($flux) {
	if (preg_match('"^depublication_.*$"',_request('exec'))) {

		
		$paramcss = array (
'couleur_claire' => $GLOBALS["couleur_claire"],

							'couleur_foncee' => $GLOBALS["couleur_foncee"],

							'couleur_lien' => $GLOBALS["couleur_lien"],

							'couleur_lien_off' => $GLOBALS["couleur_lien_off"]

							);
							
							
		$flux .= '<link rel="stylesheet" type="text/css" href="'.generer_url_public('depublication', $paramcss) .'" />';
		
		// Insertion des librairies js
		$flux .='<script src="'.url_absolue(find_in_path('lib/DataTables-1.7.4/media/js/jquery.dataTables.js')).'" type="text/javascript"></script>'."\n";
		
		// Inclusion des styles propres a dataTables
		$flux .='<link rel="stylesheet" href="'.url_absolue(find_in_path('lib/DataTables-1.7.4/media/css/demo_table.css')).'" type="text/css" media="all" />';
		
	}
	return $flux;
}

?>