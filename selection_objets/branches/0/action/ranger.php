<?php

function action_ranger_dist(){




	include_spip("inc/presentation");
	include_spip("inc/autoriser");
	include_spip("inc/puce_statut");
	
	define('_DIR_PB_REL', _DIR_RESTREINT ? '../' : '');

	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_SELECTION',(_DIR_PLUGINS.end($p)));

	$id_objet = _request("id_objet");
	$id_objet_dest = _request("id_objet_dest");
	$objet = _request("objet");
	$objet_dest = _request("objet_dest");	
	$lang = _request("langue");
	$action=_request('arg');

	
	
	
	if ($_GET["ajouter_selection"] > 0) {
		$ajouter = $_GET["ajouter_selection"];
		$id_rubrique = $_GET["id_rubrique"];
		
		
		if (!autoriser('modifier','rubrique', $id_rubrique)) die ("Interdit");
	
		$result = sql_select("id_article", "spip_articles", "id_article=$ajouter");
		
		$langue = sql_select("lang", "spip_articles", "id_article=$ajouter");
		
		while ($data = sql_fetch($langue)) {
			$lang = $data["lang"];
				}
	
		if ($row = sql_fetch($result)) {
			$result_test = sql_select("id_article", "spip_pb_selection", "id_rubrique=$id_rubrique AND id_article=$ajouter");
			if ($row_test = sql_fetch($result_test)) {
				echo "Cet article est déjà sélectionné.";
			} else {
				// Pas moyen de faire fonctionner le LIMIT 0,1 et l'ordre inverse avec sqlite
				$where = array( 
				"id_rubrique='$id_rubrique'",
				"lang='$lang'"			
				);
				
				$result_num = sql_select("ordre", "spip_pb_selection", $where, "ordre");
				$ordre = 0;
				while ($row_num = sql_fetch($result_num)) {
					$ordre = $row_num["ordre"];
				}
				$ordre ++;
				sql_insertq("spip_pb_selection", array('id_rubrique' => $id_rubrique, 'id_article'=>$ajouter, 'ordre'=>$ordre, 'lang'=>$lang));
				
			}
	
		} else {
			echo "Cet article n'existe pas.";
		}
	
	
	}
	
	if ($action=="supprimer_ordre") {
	
		include_spip('formulaires/bouton_article');

		if($objet=='rubriques'){
		
			$langues=explode(",",lire_config("langues_utilisees"));
		
			foreach ($langues as $key => $langue){
			
				$where=array(
					'id_objet='.$id_objet,
					'objet="'.$objet.'"',
					'lang="'.$langue.'"',	
					'id_objet_dest="'.$id_objet_dest.'"',
					'objet_dest="'.$objet_dest.'"',												  
					);
							
				sql_delete("spip_selection_objets",$where);
					
				// on vérifie l'ordre des objets déjà enregistrés et on corrige si besoin
				
				$where = array(
				'id_objet_dest='.$id_objet_dest,
				'objet_dest="'.$objet_dest.'"',
				'lang="'.$langue.'"',	
				);
				
				$ordre=verifier_ordre($where);	
				}
			}
		else{
		
		spip_log('eliminer 1','selection');
			$where=array(
				'id_objet='.$id_objet,
				'objet="'.$objet.'"',
				'lang="'.$lang.'"',	
				'id_objet_dest="'.$id_objet_dest.'"',
				'objet_dest="'.$objet_dest.'"',
				);
										
			sql_delete("spip_selection_objets",$where);
					
			// on vérifie l'ordre des objets déjà enregistrés et on corrige si besoin
				
			$where = array(
				'id_objet_dest='.$id_objet_dest,
				'objet_dest="'.$objet_dest.'"',
				'lang="'.$lang.'"',	
				);
				
			verifier_ordre($where);	
			}
	
	}
	
	if ($action=='remonter_ordre') {
	

	
		$where = array( 			
			'lang="'.$lang.'"',
			'objet_dest="'.$objet_dest.'"',
			'id_objet_dest="'.$id_objet_dest.'"',					
				);
		
		$result = sql_select("*", "spip_selection_objets", $where, "ordre");
		
		while ($row = sql_fetch($result)) {
			$id_objet_row = $row["id_objet"];
			$objet_row = $row["objet"];			
			$ordre_row = $row["ordre"];
			$lang_row = $row["lang"];		
			if ($id_objet  == $id_objet_row AND $objet_row == $objet) break;
			$ordre_new = $ordre_row;
			$id_objet_prec = $id_objet_row;
			$objet_prec = $objet_row;			
		
		}
		

		
		$where = array( 			
				"lang='$lang'",
				"objet_dest='$objet_dest'",
				"id_objet_dest='$id_objet_dest'",
				"id_objet='$id_objet'",	
				"objet='$objet'",		
				);
				

		spip_log('action '.$action.serialize($where).$ordre_new,'selecion_objet');
			
 		sql_updateq("spip_selection_objets", array("ordre" => $ordre_new), $where);
		
		$where = array( 			
				"lang='$lang'",
				"objet_dest='$objet_dest'",
				"id_objet_dest='$id_objet_dest'",
				"id_objet='$id_objet_prec'",	
				"objet='$objet_prec'",		
				);		
				
		spip_log('action '.$action.serialize($where).$ordre_row,'selecion_objet');	
		
 		sql_updateq("spip_selection_objets", array("ordre" => $ordre_row), $where);
		
	}
	
	if ($action=='descendre_ordre') {
	

	
		$where = array( 			
			'lang="'.$lang.'"',
			'objet_dest="'.$objet_dest.'"',
			'id_objet_dest="'.$id_objet_dest.'"',
			"id_objet='$id_objet'",	
			"objet='$objet'",						
			);
	

		$result = sql_select("*", "spip_selection_objets",$where, "ordre");
		
		if ($row = sql_fetch($result)) {

			$ordre = $row["ordre"];
			
			$where = array( 			
				'lang="'.$lang.'"',
				'objet_dest="'.$objet_dest.'"',
				'id_objet_dest="'.$id_objet_dest.'"',
				'ordre>"'.$ordre.'"',					
				);
			
			$result2 = sql_select("*", "spip_selection_objets",$where, "ordre LIMIT 0,1");
			
				if ($row2 = sql_fetch($result2)) {
					$ordre_suiv = $row2["ordre"];
					$id_objet_suiv = $row2["id_objet"];
					$objet_suiv = $row2["objet"];					
					
					$where = array( 			
						"lang='$lang'",
						"objet_dest='$objet_dest'",
						"id_objet_dest='$id_objet_dest'",
						"id_objet='$id_objet'",	
						"objet='$objet'",		
						);
					

					sql_updateq("spip_selection_objets", array("ordre" => $ordre_suiv),$where);

					
					$where = array( 			
						"lang='$lang'",
						"objet_dest='$objet_dest'",
						"id_objet_dest='$id_objet_dest'",
						"id_objet='$id_objet_suiv'",	
						"objet='$objet_suiv'",		
						);
					
					sql_updateq("spip_selection_objets", array("ordre" => $ordre),$where);

					}
		
			}

	
	}	

}

?>
