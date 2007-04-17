<?php

/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

	include_spip('base/forms');
	//$GLOBALS['exceptions_des_tables']['forms_donnees']['id_mot']=array('spip_forms_donnees_champs', 'valeur', 'forms_index_exception');
	$GLOBALS['exceptions_des_jointures']['forms_donnees']['id_mot'] = array('spip_forms_donnees_champs', 'valeur', 'forms_calculer_critere_externe');
	function forms_calculer_critere_externe(&$boucle, $joints, $col, $desc, $eg, $checkarrivee)
	{
		if ($checkarrivee!='spip_forms_donnees_champs' || $col!='valeur')
			erreur_squelette(_T('zbug_info_erreur_squelette'),
					_T('zbug_boucle') .
					" $idb " .
					_T('zbug_critere_inconnu', 
					    array('critere' => $col)));
		else {
			$id_table = $boucle->id_table;
			$boucle->modificateur['crit_id_mot']=array();
			$boucle->modificateur['crit_id_mot']['select'][] =  "donnees_champs.valeur AS id_mot";
			$boucle->modificateur['crit_id_mot']['from']["donnees_champs"] =  "spip_forms_donnees_champs";
			$boucle->modificateur['crit_id_mot']['from']["champs"] =  "spip_forms_champs";
			$boucle->modificateur['crit_id_mot']['where'][]= array("'='", "'$id_table.id_form'", "'champs.id_form'");
			$boucle->modificateur['crit_id_mot']['where'][]= array("'='", "'champs.type'", "'\"mot\"'");
			$boucle->modificateur['crit_id_mot']['where'][]= array("'='", "'donnees_champs.champ'", "'champs.champ'");
			$boucle->modificateur['crit_id_mot']['where'][]= array("'='", "'donnees_champs.id_donnee'", "'$id_table.id_donnee'");
			$boucle->modificateur['crit_id_mot']['group'][] = $id_table . '.id_donnee'; 
	
			$t = "donnees_champs";
			return $t;
		}
	}

	function forms_index_exception(&$boucle, $desc, $nom_champ, &$excep)
	{
		global $tables_des_serveurs_sql;
		list($e, $x) = $excep;	#PHP4 affecte de gauche a droite
		$excep = $x;		#PHP5 de droite a gauche !
		if ($e!='spip_forms_donnees_champs' || $x!='valeur') return NULL; // on ne traite ici qu'un cas particulier
		
		//$boucle->from["mots"] =  "spip_mots";
		$boucle->from["donnees_champs"] =  "spip_forms_donnees_champs";
		$boucle->from["champs"] =  "spip_forms_champs";
		$boucle->where[]= array("'='", "'$id_table.id_form'", "'champs.id_form'");
		$boucle->where[]= array("'='", "'champs.type'", "'\"mot\"'");
		$boucle->where[]= array("'='", "'donnees_champs.champ'", "'champs.champ'");
		$boucle->where[]= array("'='", "'donnees_champs.id_donnee'", "'$id_table.id_donnee'");
		$boucle->group[] = $boucle->id_table . '.id_donnee'; 

		$t = "'donnees_champs.valeur'";
		return $t;
	}

	// {filtre}
	function critere_filtre_dist($idb, &$boucles, $crit) {
		$not = $crit->not;
		$boucle = &$boucles[$idb];
		if ($not)
			erreur_squelette(_T('zbug_info_erreur_squelette'), $crit->op);
		$boucle->modificateur['crit_filtre'] = !isset($crit->param[0]) ? "'ET'" : calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
		//$boucle->where[]= array("'='", "'$boucle->id_table." . "id_parent'", 0);
	}
	
	function boucle_DONNEES_dist($id_boucle, &$boucles){
		if (function_exists($f='boucle_FORMS_DONNEES') OR function_exists($f='boucle_FORMS_DONNEES_dist'))
			return $f($id_boucle, &$boucles);
	}
	//
	// <BOUCLE(FORMS_DONNEES)>
	//
	function boucle_FORMS_DONNEES_dist($id_boucle, &$boucles) {
		$boucle = &$boucles[$id_boucle];
		$id_table = $boucle->id_table;
		$boucle->from[$id_table] =  "spip_forms_donnees";

		if (!isset($boucle->modificateur['tout']) && !$boucle->tout)
			$boucle->where[]= array("'='", "'$id_table.confirmation'", "'\"valide\"'");
		if (!$boucle->statut && !isset($boucle->modificateur['tout']) && !$boucle->tout)
			$boucle->where[]= array("'='", "'$id_table.statut'", "'\"publie\"'");
			
		if (isset($boucle->modificateur['crit_filtre'])){
			// construire la requete pour construire une condtion sur les champs passes en $_GET
			$filtre = new Boucle;
			$filtre->id_table = 'forms_champs';
			$filtre->sql_serveur = $boucle->sql_serveur;
			$filtre->from['forms_champs']='spip_forms_champs';
			$filtre->select[]='champ';
			foreach($boucle->where as $cond){
				if ($cond[1] == "'$id_table.id_form'"){
					$cond[1] = "'forms_champs.id_form'";
					$filtre->where[] = $cond;
				}
			}
			//$filtre->where[] = array("'='","'forms_champs.type'","'\"mot\"'");
			$reqfiltre = calculer_requete_sql($filtre);
			
			$boucle->hash .= <<<code
	$reqfiltre
	\$filtre = "";
	\$res = 0;
	while (\$row = @spip_abstract_fetch(\$result,"")){
		if ((\$r = _request(\$row['champ']))!==NULL){
			if (is_array(\$r)){
				\$r = array_diff(\$r,array('')); // enlever les valeurs vides
				\$res += count(\$r);
				if (strlen(implode("",\$r))) 
					\$filtre .= " OR (dc.champ="._q(\$row['champ'])." AND dc.valeur IN (".implode(',',array_map('_q',\$r))."))";
			}
			elseif (strlen(\$r)){
				\$res++;
				\$filtre .= " OR (dc.champ="._q(\$row['champ'])." AND dc.valeur="._q(\$r).")";
			}
		}
	}
	if (strlen(\$filtre)) \$filtre = '('.substr(\$filtre,4).')';
	else \$filtre="1=1";
code;
			$boucle->select[] = 'COUNT('.$boucle->id_table . '.id_donnee) AS res';
			$boucle->where[] = '$filtre';
			$boucle->from["dc"] =  "spip_forms_donnees_champs";
			$boucle->where[] =  array("'='", "'dc.id_donnee'", "'$id_table.id_donnee'");
			$boucle->having[] =  '('.$boucle->modificateur['crit_filtre'].'!="OU")?"res=$res":"1=1"';
			$boucle->group[] = $boucle->id_table . '.id_donnee'; 
		}
		
		if (isset($boucle->modificateur['crit_id_mot'])){
      $init = ($init = $boucles[$id_boucle]->doublons) ? ("\n\t$init = array();") : '';
      $hash = $boucle->hash;
      $boucle->hash = "";
      $boucles[$id_boucle]->doublons = false;
			// calculer la requete sans prise en compte du critere id_mot
			// car il n'est pas certain que la table possede un champ mot cle
      $corps = calculer_boucle_nonrec($id_boucle, $boucles);
      // attention, ne calculer la requete que maintenant
      // car la fonction precedente appelle index_pile qui influe dessus
      $req =	calculer_requete_sql($boucles[$id_boucle]);
			
			// construire la requete pour rechercher un champ de type mot
			$verif = new Boucle;
			$verif->id_table = 'forms_champs';
			$verif->sql_serveur = $boucle->sql_serveur;
			$verif->from['forms_champs']='spip_forms_champs';
			$verif->select[]='champ';
			foreach($boucle->where as $cond){
				if ($cond[1] == "'$id_table.id_form'"){
					$cond[1] = "'forms_champs.id_form'";
					$verif->where[] = $cond;
				}
			}
			$verif->where[] = array("'='","'forms_champs.type'","'\"mot\"'");
			$reqverif = calculer_requete_sql($verif);
			$boucle->hash = "$hash $reqverif \n$init

	if (spip_abstract_count(\$result,'".$verif->sql_serveur."')==0){
	$req
	} else";
			
			// recoller les conditions du critere id_mot dans la boucle
			foreach($boucle->modificateur['crit_id_mot']['select'] as $cond)		$boucle->select[]=$cond;
			foreach($boucle->modificateur['crit_id_mot']['from'] as $key=>$cond)			$boucle->from[$key]=$cond;
			foreach($boucle->modificateur['crit_id_mot']['where'] as $cond)			$boucle->where[]=$cond;
			foreach($boucle->modificateur['crit_id_mot']['group'] as $cond)			$boucle->group[]=$cond;
		}
	
		return calculer_boucle($id_boucle, $boucles); 
	}

	function boucle_TABLE_CHAMPS_dist($id_boucle, &$boucles){
		if (function_exists($f='boucle_FORMS_CHAMPS') OR function_exists($f='boucle_FORMS_CHAMPS_dist'))
			return $f($id_boucle, &$boucles);
	}
	//
	// <BOUCLE(FORMS_CHAMPS)>
	//
	function boucle_FORMS_CHAMPS_dist($id_boucle, &$boucles) {
		$boucle = &$boucles[$id_boucle];
		$id_table = $boucle->id_table;
		$boucle->from[$id_table] =  "spip_forms_champs";
	
		if (!isset($boucle->modificateur['tout']) && !$boucle->tout){
			$boucle->where[]= array("'='", "'$id_table.public'", "'\"oui\"'");
		}
	
		return calculer_boucle($id_boucle, $boucles); 
	}
	
	function boucle_DONNEE_CHAMPS_dist($id_boucle, &$boucles){
		if (function_exists($f='boucle_FORMS_DONNEES_CHAMPS') OR function_exists($f='boucle_FORMS_DONNEES_CHAMPS_dist'))
			return $f($id_boucle, &$boucles);
	}
	//
	// <BOUCLE(FORMS_DONNEES_CHAMPS)>
	//
	function boucle_FORMS_DONNEES_CHAMPS_dist($id_boucle, &$boucles) {
		$boucle = &$boucles[$id_boucle];
		$id_table = $boucle->id_table;
		$boucle->from[$id_table] =  "spip_forms_donnees_champs";
	
		if (!isset($boucle->modificateur['tout']) && !$boucle->tout){
			$boucle->from["champs"] =  "spip_forms_champs";
			$boucle->from["donnees"] =  "spip_forms_donnees";
			$boucle->where[]= array("'='", "'$id_table.id_donnee'", "'donnees.id_donnee'");
			$boucle->where[]= array("'='", "'$id_table.champ'", "'champs.champ'");
			$boucle->where[]= array("'='", "'donnees.id_form'", "'champs.id_form'");
			$boucle->where[]= array("'='", "'champs.public'", "'\"oui\"'");
			$boucle->group[] = $boucle->id_table . '.champ'; // ?  
		}
		if (!$boucle->statut && !isset($boucle->modificateur['tout']) && !$boucle->tout)
			$boucle->where[]= array("'='", "'donnees.statut'", "'\"publie\"'");

		return calculer_boucle($id_boucle, $boucles); 
	}
	
?>
