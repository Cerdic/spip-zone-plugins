<?php
 
if (!defined("_ECRIRE_INC_VERSION")) return;

function editer_contactabonnement($arg=null)
{

	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	//$c = array('statut' => $statut);

	//instituer_contactabonnement($id_contactabonnement, $c);
	
	$id_auteur=$arg['id_auteur'];
	$statut=$arg['statut'];
	$objet=$arg['objet'];
	$table=$arg['table'];
	$ids=$arg['ids'];
	$prix=$arg['prix'];
	$duree=$arg['duree'];
	$periode=$arg['periode'];
	$statut=$arg['statut'];
	$id_commandes_detail=$arg['id_commandes_detail'];
	$stade_relance=$arg['stade_relance'];
	
	if (_DEBUG_ABONNEMENT) spip_log("editer_contactabonnement1 $objet et ".$ids[0],'abonnement');


	if(is_array($ids) && $id_auteur>0)
	foreach($ids as $key => $id_objet)
	{

		if($id_objet!='non')
		{
				
			$verif = sql_fetsel('*', $table, 'id_'."$objet = " . $id_objet);
			if (!$verif) 
			{
				if (_DEBUG_ABONNEMENT) spip_log("$objet $id_objet inexistant",'abonnement');
				die("$objet $id_objet inexistant");
			}
			
			//todo verifier avec plugin montants?
			$calculer_prix = charger_fonction('prix', 'inc/');
			$prix=($statut=='offert')?'':$calculer_prix($objet,$id_objet);//pas de prix puisque offert
			$date = date('Y-m-d H:i:s');
			$validite = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('n'),date('j')+$duree,date('Y')));
			
			//specific a abonnement
			if($objet=='abonnement'){
				$prix=($statut=='offert')?'':$verif['prix'];//pas de prix puisque offert
				$duree = $verif['duree'];
				$periode = $verif['periode'];
					
				// jour
				if ($periode == 'jours') {
					$validite = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('n'),date('j')+$duree,date('Y')));
				}
				// ou mois
				else {
					$validite = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('n')+$duree,date('j'),date('Y')));
				}
			}
				
			if (_DEBUG_ABONNEMENT) spip_log("editer_contactabonnement $objet $id_objet date $validite",'abonnement');

			if (!$deja = sql_getfetsel('id_auteur,validite',
				'spip_contacts_abonnements',
				'id_auteur='.$id_auteur.' AND id_objet='.sql_quote($id_objet)." AND objet='$objet'")
				)
			{
				$ids_zone=$verif['ids_zone'];
				if($ids_zone!='')
				ouvrir_zone($id_auteur,$ids_zone);
					
				sql_insertq("spip_contacts_abonnements",array(
					'id_auteur'=> $id_auteur,
					'objet'=>$objet,
					'id_objet' => $id_objet,
					'date'=>$date,
					'validite'=>$validite,
					'prix'=>$prix,
					'id_commandes_detail'=>$id_commandes_detail,
					'statut_abonnement'=>$statut
					));
			}else{
				if (_DEBUG_ABONNEMENT) spip_log("editer_contactabonnement pour auteur=$id_auteur $objet $id_objet existe deja",'abonnement');
				//modif des dates d'echeances
				$echeances = _request('validites');
				if($echeances[$key]!='' && ($echeances[$key]!=$deja['validite'])){
					if (_DEBUG_ABONNEMENT) spip_log("changer date ". $echeances[$key]."!=".$deja['validite'],'abonnement');
				sql_updateq("spip_contacts_abonnements",array('validite'=>$echeances[$key]),
					'id_auteur='.$id_auteur.' and id_objet='.sql_quote($id_objet)." and objet='$objet'");
				}
			}
		}
	}
			// Notifications, gestion des revisions, reindexation...
		pipeline('post_edition',
		array(
			'args' => array(
				'table' => 'spip_contacts_abonnements',
				'id_auteur' => $id_auteur,
				'objet'=>$objet,
				'id_objet' => $id_objet,
				'statut_abonnement' => $statut
			),
			'data' => $ids
		)
		);
		
}


function ouvrir_zone($id_auteur,$ids_zone)
{
	$array_ids = explode(",", $ids_zone);
	foreach($array_ids as $id_zone)
	{
	if (_DEBUG_ABONNEMENT) spip_log("ouvrir_zone $id_zone pour $id_auteur",'abonnement');
		sql_insertq("spip_zones_auteurs", array(
			"id_zone"=>$id_zone,
			"id_auteur"=>$id_auteur
		));
	}
}

?>
