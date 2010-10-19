<?php
include_spip('base/abstract_sql');

/**
 *
 * Transforme les /n en <br />
 *
 */
function n_to_br($texte){
	$texte = str_replace("\n", "<br />", $texte);
	return $texte;
}

/**
 *
 * Donne le nom d'un pays en fonction de son id
 *
 * @return false|string false dans le cas ou il ne reçoit pas de paramètres ou si le paramètre n'est pas bon
 * @param int $id_pays L'id_pays de la table spip_geo_pays
 */
function id_pays_to_pays($id_pays){
	if((is_numeric($id_pays)) && ($id_pays != 0)){
		$pays = sql_getfetsel('nom', 'spip_geo_pays', 'id_pays ='.$id_pays);
		return typo($pays);
	}
	else return;
}

/**
 *
 * Récupère la valeur d'un champs d'un auteur si on ne possède que le nom du champs
 * Dans le cas de la boucle FOR par exemple
 *
 * @return
 * @param object $champs
 * @param object $id_auteur
 */
function inscription2_recuperer_champs($champs,$id_auteur){
	if($champs == 'login'){
		$champs = 'b.login';
	}
	if($champs == 'pays'){
		$resultat = sql_getfetsel("b.nom","spip_auteurs_elargis a LEFT JOIN spip_geo_pays b on a.pays = b.id_pays","a.id_auteur=$id_auteur");
		return typo($resultat);
	}
	if($champs == 'pays_pro'){
		$resultat = sql_getfetsel("b.nom","spip_auteurs_elargis a LEFT JOIN spip_geo_pays b on a.pays_pro = b.id_pays","a.id_auteur=$id_auteur");
		return typo($resultat);
	}
	$resultat = sql_getfetsel($champs,"spip_auteurs_elargis a LEFT JOIN spip_auteurs b ON a.id_auteur=b.id_auteur","a.id_auteur=$id_auteur");
	if (is_array(unserialize($resultat))){
		return $resultat;
	} else {
		return typo($resultat);
	}
}

/**
 *
 * Fonction utilisée par le critère i2_recherche
 *
 * @return array Le tableau des auteurs correspondants aux critères de recherche
 * @param string $quoi[optional] Le contenu textuel recherché
 * @param object $ou[optional] Le champs dans lequel on recherche
 * @param object $table[optional]
 */
function i2_recherche($quoi=NULL,$ou=NULL,$table=NULL){
	if(isset($quoi) && isset($ou)){
		$quoi = texte_script(trim($quoi));
		include_spip('base/serial'); // aucazou !
		global $tables_principales;

		if(isset($tables_principales[table_objet_sql($table)]['field'][$ou])){
			$auteurs = sql_get_select('id_auteur',table_objet_sql($table),"$ou LIKE '%$quoi%'");
		}
		else{
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

/**
 *
 * Critère utilisé pour rechercher dans les utilisateurs (page ?exec=inscription2_adherents)
 *
 */
function critere_i2_recherche_dist($idb, &$boucles){
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