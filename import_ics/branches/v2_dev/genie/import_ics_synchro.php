<?php

/**
 * Gestion du génie import_ics_synchro
 *
 * @plugin import_ics pour SPIP
 * @license GPL
 * 
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip('inc/import_ics');
/**
 * Actualise tous les almanachs
 *
 * @genie import_ics_synchro
 *
 * @param int $last
 *     Timestamp de la dernière exécution de cette tâche
 * @return int
 *     Positif : la tâche a été effectuée
 */
function genie_import_ics_synchro_dist($t){

//on recupère toutes les infos sur les almanachs
if(
	$resultats = sql_allfetsel('*', 'spip_almanachs')
	and is_array($resultats)
)
{
//librairie icalcreator incluse dans le plugin icalendar
include_spip('lib/iCalcreator.class');

//pour chacun des almanachs, on va traiter les différences
foreach ($resultats as $r) {
		//	on va faire une sélection des evenemnts associés à l'almanach en cours 
		//donc jointure sur les table spip_evenemnts et spip_almanachs_liens
		$evenements_lies = sql_allfetsel('E.uid, E.id_evenement, E.sequence',
			'spip_evenements AS E 
			INNER JOIN spip_almanachs_liens AS L
			ON E.id_evenement = L.id_objet AND L.id_almanach='.intval($r['id_almanach']));

		//tableau des uid associés à cet almanach tiré du tableau précédent
			$uid =array();
			foreach ($evenements_lies as $u ) {
				$uid[] = $u['uid'];
			};

		//configuration nécessaire à la récupération et parsing du calendrier distant

			$config = array("unique_id" => "distant",
							"url" => $r['url']);
			$v = new vcalendar($config);
			$v->parse();

			while ($comp = $v->getComponent())
			{
		//les variables qui vont servir à vérifier l'existence et l'unicité 
			  $sequence_distante = $comp->getProperty( "SEQUENCE" );#sequence d l'evenement http://kigkonsult.se/iCalcreator/docs/using.html#SEQUENCE
				$uid_distante = $comp->getProperty("UID");#uid de l'evenement;
				//au cas où le flux ics ne fournirait pas le champ sequence, on initialise la valeur à 0 comme lors d'un import
				if (!is_int($sequence_distante)){$sequence_distante="0";}
				//On commence à vérifier l'existence et l'unicité  maintenant et on met à jour 
				//ou on importe selon le cas
				if (in_array($uid_distante, $uid)){//si l'uid_distante est présente dans la bdd
					// on utilise le fait que les deux tableaux ont le même index pour le récupérer
					$cle = array_search($uid_distante, $uid); 
					//sequence presente dans la base ayant le meme index
					$sequence = $evenements_lies[$cle]['sequence'];
					if ($sequence < $sequence_distante) {
						importation_evenement($comp,$r);
					}
				}
				else{
					importation_evenement($comp,$r);
				}
			}
		}
}

return 1;

}


?>