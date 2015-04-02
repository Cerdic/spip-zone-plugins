<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Teste si l'objet est dans la zone Réservation Evenement
 *
 * @param  int $id Id de l'objet
 * @param  string $objet L'objet
 * @param  array $rubrique_reservation Les rubriques de la zone Réservation Évènement
 * @param  array $options Possible valeur array(
 * 												'tableau'=>'oui',
 * 												'where'=>'requete sql',
 * 												'select=>'les champs',
 * 												'resultat=>'par_id')
 * @return Bolean/array   array si $options['tableau']=oui
 */
function rubrique_reservation($id='',$objet,$rubrique_reservation='',$options=array()){
	include_spip('inc/rubriques');		
	//On récupère la config si pas passé comme variable
	if(!$rubrique_reservation){
		include_spip('inc/config');
		include_spip('formulaires/selecteur/generique_fonctions');	
		$config=lire_config('reservation_evenement/',array());
		$rubrique_reservation=isset($config['rubrique_reservation'])?picker_selected($config['rubrique_reservation'],'rubrique'):'';
	}
	//Chercher les rubriques de la branche
	if(is_array($rubrique_reservation))$rubrique_reservation=explode(',',calcul_branche_in($rubrique_reservation));
	

	//Si une zone a été définit
	if(is_array($rubrique_reservation) and count($rubrique_reservation)!=0){
		
		//Teste si l'objet se trouve dans la zone
		if($id){
			//On teste si l'objet se trouve dans la zone
			if($objet=='article'){
				$select='id_article';
				$from=array('spip_articles');
				$where=array('id_article='.$id.' AND id_rubrique IN ('.implode(',',$rubrique_reservation).')');				
			}
			elseif($objet=='evenement'){
				$select='e.id_evenement';
				$from=array('spip_evenements AS e INNER JOIN spip_articles AS a ON e.id_article=a.id_article');
				$where=array('e.id_evenement='.$id.' AND a.id_rubrique IN ('.implode(',',$rubrique_reservation).')');
			}
			elseif($objet=='rubrique'){
				$select='id_rubrique';
				$from=array('spip_rubriques');
				$where=array('id_rubrique='.$id.' AND id_rubrique IN ('.implode(',',$rubrique_reservation).')');				
			}
			if(isset($options['e.id_evenement']))array_push($where,$options['where']);
					
			if(sql_getfetsel($select,$from,$where)) $return=true; //Objet se trouve dans la zone
			else $return=false;//Objet ne se trouve pas dans la zone
			
		}
		//Afficher les id_articles se trouvant dans la zone
		elseif(isset($options['tableau']) AND $options['tableau']=='oui'){
			//On teste si l'objet se trouve dans la zone
			if($objet=='article'){
				$select=array('id_article');
				$from=array('spip_articles');
				$where=array('id_rubrique IN ('.implode(',',$rubrique_reservation).')');				
			}
			if($objet=='evenement'){
				$select=array('e.id_evenement');
				$from=array('spip_evenements AS e INNER JOIN spip_articles AS a ON e.id_article=a.id_article');
				$where=array('e.id_evenement_source=0 AND a.id_rubrique IN ('.implode(',',$rubrique_reservation).')');
			}
			if($objet=='rubrique'){
				$select=array('id_rubrique');
				$from=array('spip_rubriques');
				$where=array('id_rubrique IN ('.implode(',',$rubrique_reservation).')');	
			}
			if(isset($options['where']))array_push($where,$options['where']);
			if(isset($options['select'])){
				if($options['select'] != '*')array_push($where,$options['where']);
				else $select=$options['select'];
				}			
								
			$sql=sql_select($select,$from,$where);
			
			$ids=array();
			if(!isset($options['resultat'])){
				while($data=sql_fetch($sql)){
					$ids[]=$data['id_'.$objet];
				}
			}
			elseif($options['resultat']=='par_id'){
				
				while($data=sql_fetch($sql)){
					$ids[$data['id_evenement']]=$data;
				}					
			}
			$return=$ids;
		}
	}
	elseif(!isset($options['tableau'])) $return=true; //Test sur objet, pas de zone définit
	elseif(isset($options['tableau'])) $return=false; //Affichage tableau, pas de zone définit donc pas de résultat	

	return $return;
}