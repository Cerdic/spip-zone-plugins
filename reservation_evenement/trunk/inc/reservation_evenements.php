<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Teste si l'objet est dans la zone Réservation Evenement
 *
 * @param  int $id Id de l'objet
 * @param  string $objet L'objet
 * @param  array $rubrique_reservation Les rubriques de la zone Réservation Évènement
 * @param  array $options Possible valeur array('tableau'=>'oui')
 * @return Bolean/array   array si $options['tableau']=oui
 */
function rubrique_reservation($id='',$objet,$rubrique_reservation='',$options=array()){
	//On récupère la config si pas passé comme variable
	if(!$rubrique_reservation){
		include_spip('inc/config');
		include_spip('formulaires/selecteur/generique_fonctions');
		$config=lire_config('reservation_evenement/',array());
		$rubrique_reservation=isset($config['rubrique_reservation'])?picker_selected($config['rubrique_reservation'],'rubrique'):'';
	}

	//Si une zone a été définit
	if(is_array($rubrique_reservation) and count($rubrique_reservation)!=0){
		
		//Teste si l'objet se trouve dans la zone
		if($id){
			//On teste si l'objet se trouve dans la zone
			if($objet=='article'){
				$i=sql_getfetsel('id_article','spip_articles','id_article='.$id.' AND id_rubrique IN ('.implode(',',$rubrique_reservation).')');
			}
			elseif($objet=='evenement'){
				$i=sql_getfetsel('e.id_evenement','spip_evenements AS e INNER JOIN spip_articles AS a ON e.id_article=a.id_article','e.id_evenement='.$id.' AND a.id_rubrique IN ('.implode(',',$rubrique_reservation).')');
			}
			elseif($objet=='rubrique'){
				$i=sql_getfetsel('id_rubrique','spip_rubriques','id_rubrique='.$id.' AND id_rubrique IN ('.implode(',',$rubrique_reservation).')');
			}
					
			if($i) $return=true; //Objet se trouve dans la zone
			else $return=false;//Objet ne se trouve pas dans la zone
			
		}
		//Afficher les id_articles se tropuvant dans la zone
		elseif(isset($options['tableau']) AND $options['tableau']=='oui'){
			//On teste si l'objet se trouve dans la zone
			if($objet=='article'){
				$sql=sql_select('id_article','spip_articles','id_rubrique IN ('.implode(',',$rubrique_reservation).')');
			}
			
			if($sql){
				$ids=array();
				while($data=sql_fetch($sql)){
					$ids[]=$data['id_'.$objet];
				}
			}
			$return=$ids;
		}
	}
	elseif(!isset($options['tableau'])) $return=true; //Test sur objet, pas de zone définit
	elseif(isset($options['tableau'])) $return=false; //Affichage tableau, pas de zone définit donc pas de résultat	

	return $return;
}