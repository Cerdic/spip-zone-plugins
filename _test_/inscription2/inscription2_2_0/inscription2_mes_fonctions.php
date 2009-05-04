<?php
include_spip('base/abstract_sql');

// Filtres
function n_to_br($texte){
	$texte = str_replace("\n", "<br />", $texte);
	return $texte;
}

function id_pays_to_pays($id_pays){
	if($id_pays != 0){
		$pays = sql_getfetsel('nom', 'spip_geo_pays', 'id_pays ='.$id_pays) ;
		return typo($pays);
	}
	else return;
}

function inscription2_recuperer_champs($champs,$id_auteur){
	if($champs == 'login'){
		$champs = 'spip_auteurs.login';
	}
	if($champs == 'pays'){
		spip_log('champs = pays');
		$resultat = sql_getfetsel("b.nom","spip_auteurs_elargis a LEFT JOIN spip_geo_pays b on a.pays = b.id_pays","a.id_auteur=$id_auteur");
		return propre($resultat);
	}
	if($champs == 'pays_pro'){
		spip_log('champs = pays_pro');
		$resultat = sql_getfetsel("b.nom","spip_auteurs_elargis a LEFT JOIN spip_geo_pays b on a.pays_pro = b.id_pays","a.id_auteur=$id_auteur");
		return propre($resultat);
	}
	$resultat = sql_getfetsel($champs,"spip_auteurs_elargis LEFT JOIN spip_auteurs USING(id_auteur)","spip_auteurs_elargis.id_auteur=$id_auteur");
	return propre($resultat);
}

function i2_recherche($quoi=NULL,$ou=NULL,$table=NULL){
	if(isset($quoi) && isset($ou)){
		$quoi = texte_script(trim($quoi));
		global $tables_principales;

		if(isset($tables_principales[table_objet_sql($table)]['field'][$ou])){
			spip_log("champs dans $table");
			$auteurs = sql_get_select('id_auteur',table_objet_sql($table),"$ou LIKE '%$quoi%'");
		}
		else{
			spip_log("pas dans la table principale");
			global $tables_jointures;
			if(isset($tables_jointures[table_objet_sql($table)]) && ($jointures=$tables_jointures[table_objet_sql($table)])){
				foreach($jointures as $jointure=>$val){
					if(isset($tables_principales[table_objet_sql($val)]['field'][$ou])){
						$auteurs = sql_get_select('id_auteur',table_objet_sql($table)." AS $table LEFT JOIN ".table_objet_sql($val)." AS $val USING(id_auteur)","$val.$ou LIKE '%$quoi%'");
					}
				}
			}
		}
		return "($auteurs)";	
	}
}

function critere_i2_recherche_dist($idb, &$boucles, $param){
	$boucle = &$boucles[$idb];
	$primary = $boucle->primary;
	$ou = '@$Pile[0]["case"]';
	$quoi = '@$Pile[0]["valeur"]';
	$table = $boucle->type_requete;
	$boucle->hash .= "
	\$auteurs = i2_recherche($quoi,$ou,$table);
	";
	$boucle->where[] = array("'IN'","'$boucle->id_table." . "$primary'",'$auteurs');
}
?>
