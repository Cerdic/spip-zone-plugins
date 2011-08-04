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

filtre unique pour determiner l'accs (en cascade) d'un auteur ˆ un objet, ce filtre teste si l'auteur 
- est liŽ ˆ la zone restreinte ˆ laquelle appartient la rubrique
- a un abonnement direct si article 
- a un abonnement direct si rubrique
- a un abonnement ˆ la rubrique parente si article
- a un abonnement si la date de validite correspond a la date_utile de la rubrique si exact est demande

[(#SESSION{id_auteur}|abonne_objet{rubrique,#ID_RUBRIQUE}|oui) <:acces_ouvert:> ]
[(#SESSION{id_auteur}|abonne_objet{article,#ID_ARTICLE}|non) <:acces_restreint:> ]

*/

/**
 * filtre de test pour savoir si le visiteur est abonne a 
 * l'article ou a la rubrique demande
 * @param int $id_rubrique
 * @return bool
 */
function abonne_objet($id_auteur, $objet="rubrique", $id_objet){
	
if (is_numeric($id_objet)){
	$objets = array();
	//soit article soit rubrique
	if($objet=="article"){
	$objets['article']=$id_objet;
	$id_rubrique = sql_getfetsel('id_rubrique','spip_articles',"id_article=".intval($id_objet));
	}
	else {
	$id_rubrique=$id_objet;
	}
	$objets['rubrique']=$id_rubrique;
	if (is_null($id_auteur)) $id_auteur=$GLOBALS['visiteur_session']['id_auteur'];
	
	$rubrique = sql_fetsel('date_utile,id_parent','spip_rubriques',"id_rubrique=".intval($id_rubrique));

	//si l'auteur a accs a la rubrique via une zone
	$zones=accesrestreint_zones_rubrique($id_rubrique);
	//ou si id_parent est dans la zone -> deprotege la rubrique enfant
	$id_parent=$rubrique['id_parent'];
	$zones=array_merge($zones,accesrestreint_zones_rubrique($id_parent));
	
	foreach($zones as $id_zone){
		if(accesrestreint_acces_zone($id_zone,$id_auteur)) 
			return true;
	}
	
	foreach($objets as $objet => $id_objet){
	//si l'auteur a accs a l'article - ou sa rubrique - ou a la rubrique elle meme
		$where = array();
		$where[] = "id_objet=".intval($id_objet);
		$where[] = "objet='$objet'";
		$where[] = "id_auteur=".intval($id_auteur);
		$where[] = "validite>='".date('Y-m-d H:i:s')."'";
		$where[] = "statut_abonnement IN ('paye','offert')";

	$liste_objets = sql_getfetsel('id_objet','spip_contacts_abonnements',$where);
	if ($liste_objets) return $liste_objets;
	}
	
	//abonnement(s) en cours
		$w = array();
		$w[] = "objet='abonnement'";
		$w[] = "id_auteur=".intval($id_auteur);
		$w[] = "validite>='".date('Y-m-d H:i:s')."'";
		$w[] = "statut_abonnement IN ('paye','offert')";
		
	$abonnements = sql_allfetsel('id_objet,date,validite','spip_contacts_abonnements',$w);
	foreach($abonnements as $abo){
		$id_abo=$abo['id_objet'];
		$date=$abo['date'];
		$validite=$abo['validite'];
		
	//on verifie les correspondances de dates
	$date_utile=$rubrique['date_utile'];
		if(($date<=$date_utile) && ($validite>=$date_utile)){
			$valide=true;
		}
		
	$exact = sql_getfetsel('exact','spip_abonnements','id_abonnement='."'$id_abo'","'exact' ASC");
	if ($exact!='oui') $valide=false;
	
	if($valide){
	return true;
	}


	}
}

}

//ids_zone est une liste d'identifiants separes par une virgule
function fermer_zone($id_auteur,$ids_zone)
{
	$array_ids = explode(",", $ids_zone);
	foreach($array_ids as $id_zone)
	{
	if (_DEBUG_ABONNEMENT) spip_log("fermer_zone $id_zone pour $id_auteur",'abonnement');
		sql_delete("spip_zones_auteurs", array(
			"id_zone"=>$id_zone,
			"id_auteur"=>$id_auteur
		));
	}
}

?>
