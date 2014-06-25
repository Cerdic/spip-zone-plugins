<?php

//Securite
if (!defined('_ECRIRE_INC_VERSION')) return;

function modifier_date($givendate,$day=0,$mth=0,$yr=0) 
{
      $cd = strtotime($givendate);
      $newdate = date('Y-m-d h:i:s', mktime(date('h',$cd), date('i',$cd), date('s',$cd), date('m',$cd)+$mth, date('d',$cd)+$day, date('Y',$cd)+$yr));
      return $newdate;
}

/**

filtre unique pour determiner l'acces (en cascade) d'un auteur e un objet, ce filtre teste si l'auteur 
- est lie e la zone restreinte e laquelle appartient la rubrique
- a un abonnement direct si article 
- a un abonnement direct si rubrique
- a un abonnement à la rubrique parente si article
- a un abonnement si la date de validite correspond a la date_utile de la rubrique si (exact) est demande
- a un abonnement si le nombre de rubriques à ouvrir est insuffisant (nb_rub)

[(#SESSION{id_auteur}|abonne_objet{rubrique,#ID_RUBRIQUE}|oui) <:acces_ouvert:> ]
[(#SESSION{id_auteur}|abonne_objet{article,#ID_ARTICLE}|non) <:acces_restreint:> ]

*/

/**
 * filtre de test pour savoir si le visiteur est abonne a 
 * l'article ou a la rubrique demande
 * @param int $id_rubrique
 * @return bool
 */
function abonne_objet($id_auteur, $objet, $id_objet){
	
	$valide=false;
	
	//on annule de suite si les donnees sont incompletes
	if (!$id_auteur OR !intval($id_objet)) return false;
	
	$objets = array();
	
	//recuperer l'id_rubrique que l'objet soit un article ou une rubrique
	if($objet=="article"){
		$objets['article']=$id_objet;
		$id_rubrique = sql_getfetsel('id_rubrique','spip_articles',"id_article=".intval($id_objet));
	} else $id_rubrique=$id_objet;
	
	$rubrique = sql_fetsel('date_utile,id_parent','spip_rubriques',"id_rubrique=".intval($id_rubrique));

	#1 //si l'auteur a acces a la rubrique via une zone
		$zones=accesrestreint_zones_rubrique($id_rubrique);
		//ou si id_parent est dans la zone -> deproteger la rubrique enfant
		$id_parent=$rubrique['id_parent'];
		$zones=array_merge($zones,accesrestreint_zones_rubrique($id_parent));
		
		foreach($zones as $id_zone){
			if(accesrestreint_acces_zone($id_zone,$id_auteur)) {
				if (_DEBUG_ABONNEMENT) spip_log("oui, acces zone auteur $id_auteur pour rubrique $id_rubrique",'abodate');
				return true;
			}
		}
		
	#2 //si l'auteur a accès a l'article - ou a sa rubrique - ou a la rubrique elle meme	
		$objets['rubrique']=$id_rubrique;
		foreach($objets as $objet => $id_objet){
			$where = array();
			$where[] = "id_objet=".intval($id_objet);
			$where[] = "objet='$objet'";
			$where[] = "id_auteur=".intval($id_auteur);
			$where[] = "date<='".date('Y-m-d H:i:s')."'";
			$where[] = "validite>='".date('Y-m-d H:i:s')."'";
			$where[] = "statut_abonnement IN ('paye','offert')";
	
			$liste_objets = sql_getfetsel('id_objet','spip_contacts_abonnements',$where);
				if ($liste_objets) {
					if (_DEBUG_ABONNEMENT) spip_log("oui, acces objet auteur $id_auteur pour $objet $id_objet",'abodate');
					return true;
				}
		}
		
	#3 //sinon l'auteur a peut-etre un abonnement(s) en cours ?
		$date_utile=$rubrique['date_utile'];
		$valide=verifier_abonnement($id_auteur,$id_rubrique,$date_utile);
		
	return $valide;
}


function verifier_abonnement($id_auteur,$id_rubrique,$date_utile){
	
	$valide=false;
	
		$w = array();
		$w[] = "objet='abonnement'";
		$w[] = "id_auteur=".intval($id_auteur);
		$w[] = "date<='".date('Y-m-d H:i:s')."'";
		$w[] = "statut_abonnement IN ('paye','offert')";
		
		$contacts_abonnements = sql_allfetsel('id_objet,date,validite','spip_contacts_abonnements',$w);
		
		foreach($contacts_abonnements as $abo){
			$id_abo=$abo['id_objet'];
			$date_debut=$abo['date'];
			$date_fin=$abo['validite'];
		
			//on verifie si l'acces doit etre ouvert, si les dates correspondent ou si un nombre de rubriques est demandé
			//on recherche donc exact et nb_rub	
			$offre_abonnement = sql_fetsel('exact,nb_rub','spip_abonnements',array("id_abonnement=".$id_abo));
				$exact=$offre_abonnement['exact'];
				$nb_rub=$offre_abonnement['nb_rub'];
			
			//aucun acces - (on a demande uniquement un abonnement papier)
			if($exact=='non') {
				if (_DEBUG_ABONNEMENT) spip_log("non, exact=non aucun acces auteur $id_auteur pour rubrique $id_rubrique",'abodate');
				return false;
			}
		
			//acces demande
			if($exact=='oui'){
					//on commence par verifier si il y a une correspondances avec la date de parution
					//la date_utile de la rubrique doit être comprise entre le début et la fin de l'abonnement
					
					if($date_utile>=$date_debut && $date_utile<=$date_fin){
						if (_DEBUG_ABONNEMENT) spip_log("oui, acces auteur $id_auteur pour rubrique $id_rubrique",'abodate');
						return true;
					}
			
					//sinon on verifie si il y a bien le nombre de rubriques demandees
					if($nb_rub>0){
						//compter toutes les rubriques ayant une date de parution superieure au debut de l'abonnement
						$result=sql_allfetsel('id_rubrique','spip_rubriques',"date_utile >=".sql_quote($date_debut));
						$total = count($result);
						//total au-dela du nombre de rubriques demandees en accès
						if($total>$nb_rub){
							if (_DEBUG_ABONNEMENT) spip_log("non, depassement du nombre de rubriques total=$total> nb_rub=$nb_rub",'abodate');
							return false;
						}
						//total en dessous
						foreach($result as $row){
							if($id_rubrique==$row['id_rubrique']) $valide=true;
							if (_DEBUG_ABONNEMENT) spip_log("la rubrique ". $row['id_rubrique']." est dans les rubriques >= $date_debut",'abodate');
						}
					}
			}
		
		}
		
	if ($valide==false) if (_DEBUG_ABONNEMENT) spip_log("non, au final aucun acces auteur $id_auteur pour rubrique $id_rubrique",'abodate');
	
	return $valide;	
}


//ids_zone est une liste d'identifiants separes par une virgule
function fermer_zone($id_auteur,$ids_zone)
{
	$array_ids = explode(",", $ids_zone);
	foreach($array_ids as $id_zone)
	{
	//if (_DEBUG_ABONNEMENT) spip_log("fermer_zone $id_zone pour $id_auteur",'abonnement');
		sql_delete("spip_zones_auteurs", array(
			"id_zone"=>$id_zone,
			"id_auteur"=>$id_auteur
		));
	}
}

?>
