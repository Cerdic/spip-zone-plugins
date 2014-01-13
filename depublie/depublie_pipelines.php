<?php
/* 
 2014
 Anne-lise Martenot 
 elastick.net 
*/
/**
 * Déclarations des pipelines pour le formulaire de dates
 *
 * @plugin     Depublie
 * @copyright  2014
 * @licence    GNU/GPL
 * @package    SPIP\Depublie\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
Insere via le pipeline le morceau de squelette calculé (juste le input) avec la date de dépublication dans le formulaire dater
*/
function depublie_recuperer_fond($flux){
		if($flux['args']['fond'] == 'formulaires/dater'){
			 $ajouter_depublier = recuperer_fond("formulaires/inc-depublie",$flux['args']['contexte']);
			 $flux['data']['texte'] = preg_replace("%(<\/ul>.*<!--extra-->)%is", "$ajouter_depublier $0", $flux['data']['texte']);
		}
	return $flux;
}


// Les pipelines greffent les champs supplémentaires aux flux des étapes CVT du formulaire dater

function depublie_formulaire_charger($flux){
		// si formulaire dater, charger les données des champs supplémentaires
		if ($flux['args']['form'] == 'dater'){
			$objet=$flux['data']['objet'];
			$id_objet=$flux['data']['id_objet'];
			
			$select = "`date_depublie`,`statut`";
			
			$row = sql_fetsel($select, "spip_depublies", "id_objet=".intval($id_objet)." AND objet='$objet'");
			$possedeDateDepublie = false;
			
			if (isset($row['date_depublie']) AND
				$regs = recup_date($row['date_depublie'], false)) {
				$annee_depublie = $regs[0];
				$mois_depublie = $regs[1];
				$jour_depublie = $regs[2];
				$heure_depublie = $regs[3];
				$minute_depublie = $regs[4];
				$possedeDateDepublie = true;
				// attention : les vrai dates de l'annee 1 sont stockee avec +9000 => 9001
				// mais reviennent ici en annee 1 par recup_date
				// on verifie donc que le intval($row['date_depublie']) qui ressort l'annee
				// est bien lui aussi <=1 : dans ce cas c'est une date sql 'nulle' ou presque, selon
				// le gestionnnaire sql utilise (0001-01-01 pour PG par exemple)
				if (intval($row['date_depublie'])<=1 AND ($annee_depublie<=1) AND ($mois_depublie<=1) AND ($jour_depublie<=1))
					$possedeDateDepublie = false;
			}
			else
				$annee_depublie = $mois_depublie = $jour_depublie = $heure_depublie = $minute_depublie = 0;
			
			// attention, si la variable s'appelle date ou date_depublie, le compilo va
			// la normaliser, ce qu'on ne veut pas ici.
			$flux['data']['afficher_date_depublie'] = ($possedeDateDepublie?$row['date_depublie']:'');
			$flux['data']['date_depublie_jour'] = dater_formater_saisie_jour($jour_depublie,$mois_depublie,$annee_depublie);
			$flux['data']['date_depublie_heure'] = "$heure_depublie:$minute_depublie";
		
			$flux['data']['sans_depublie'] = !$possedeDateDepublie;
		}

	return $flux;		
}


function depublie_formulaire_verifier($flux){
		// si formulaire dater, vérifier les données des champs supplémentaires
		if ($flux['args']['form'] == 'dater'){
				
		$k='date_depublie';
		if ($v=_request($k."_jour") AND !dater_recuperer_date_saisie($v))
			$flux[$k] = _T('format_date_incorrecte');
		elseif ($v=_request($k."_heure") AND !dater_recuperer_heure_saisie($v))
			$flux[$k] = _T('format_heure_incorrecte');
		}
		
	return $flux;		
}


function depublie_formulaire_traiter($flux){
	
	/********************************/	
	# Traitement du formulaire dater #
	/********************************/
	
		//si formulaire dater, se greffer pour enregistrer les données des champs supplémentaires
		if ($flux['args']['form'] == 'dater') {
			
			//récupère les arguments objet/id_objet
			$objet=$flux['args']['args'][0];
			$id_objet=$flux['args']['args'][1];

			if (_request('changer')){
				//on teste si il y a déjà une entrée dans spip_depublies
				$row = sql_fetsel('date_depublie', "spip_depublies", "id_objet=".intval($id_objet)." AND objet='$objet'");
				$possedeDateDepublie = false;
				if (isset($row['date_depublie'])) $possedeDateDepublie = true;
					
					
				$set['statut']= lire_config('depublie/statut_depublie');
		
				if (!_request('date_depublie_jour') OR _request('sans_depublie'))
					$set['date_depublie'] = sql_format_date(0,0,0,0,0,0);
				else {
					if (!$d = dater_recuperer_date_saisie(_request('date_depublie_jour')))
						$d = array(date('Y'),date('m'),date('d'));
					if (!$h = dater_recuperer_heure_saisie(_request('date_depublie_heure')))
						$h = array(0,0);
					$set['date_depublie'] = sql_format_date($d[0], $d[1], $d[2], $h[0], $h[1]);
				}

				$set['objet']=$objet;
				$set['id_objet']=$id_objet;
				
				//update ou insert
				if($possedeDateDepublie == true) 
					sql_updateq('spip_depublies', $set, "id_objet=".intval($id_objet)." AND objet='$objet'");
				if($possedeDateDepublie == false) 
					sql_insertq('spip_depublies',$set);

			}
				
		}
				
	/***********************************/	
	#   Gérer la date de dépublication  #
	# au changement de statut en publié #
	/***********************************/
	
	$duree= lire_config('depublie/publication_duree');
	
		//si on a demandé la durée automatique de publication se greffer sur le traitement du formulaire de changement de statut d'un article
		if($duree>0 AND $flux['args']['form'] == 'instituer_objet' AND $flux['args']['args'][0] == 'article') {
			
			$id_objet=$flux['args']['args'][1];
			$objet='article';
			$id_rubrique_choisie= lire_config('offres/rubrique_offres');
			
			$row = sql_fetsel("id_rubrique, statut", "spip_articles", "id_article=$id_objet AND id_rubrique=$id_rubrique_choisie");
						
			//seulement si la rubrique est celle choisie et l'article est publié
			if(isset($row['id_rubrique']) && $row['statut']=='publie'){
				
				//on récupère la configuation de la durée de publication
				$periode= lire_config('depublie/publication_periode');
				$duree= lire_config('depublie/publication_duree');
				$set['statut']=lire_config('depublie/statut_depublie');
				
				
				// jour
				if ($periode == 'jours') {
					$set['date_depublie'] = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('n'),date('j')+$duree,date('Y')));
				}
				// ou mois
				else {
					$set['date_depublie'] = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('n')+$duree,date('j'),date('Y')));
				}
								
				//ne rien faire si il y a déjà une date de dépublication
				$row = sql_fetsel('date_depublie', "spip_depublies", "id_objet=".intval($id_objet)." AND objet='$objet'");
					$possedeDateDepublie = false;
					if (isset($row['date_depublie'])) $possedeDateDepublie = true;
				
					$set['objet']=$objet;
					$set['id_objet']=$id_objet;
					
				//update ou insert
				if($possedeDateDepublie == true) {
				    sql_updateq('spip_depublies', $set, "id_objet=".intval($id_objet)." AND objet='$objet'");
				} else {
				    sql_insertq('spip_depublies',$set);
				}								
				
				
			}
			
		}
		
	
	return $flux;
}

// déclaration des taches à exécuter
function depublie_taches_generales_cron($taches){
    $taches['depublier'] = 60*60*12; // 2 fois par jour
    return $taches;
}

?>
