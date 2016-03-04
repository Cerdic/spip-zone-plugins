<?php
/**
 * Déclarations des pipelines pour le formulaire de dates
 *
 * @author     Anne-lise Martenot (http://elastick.net)
 * @plugin     Depublie
 * @copyright  2014
 * @licence    GNU/GPL
 * @package    SPIP\Depublie\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion dans le pipeline recuperer_fond (SPIP)
 * 
 * Ajouter le champ date_publier dans le formulaire dater
 * 
 * @param array $flux
 * @return array $flux
 */
function depublie_recuperer_fond($flux){
	if($flux['args']['fond'] == 'formulaires/dater'){
		$ajouter_depublier = recuperer_fond("formulaires/inc-depublie",$flux['args']['contexte']);
		$flux['data']['texte'] = preg_replace('%(<div class=["\'][^"\']*editer-groupe(.*?)</div>)%is', '$1'."\n".$ajouter_depublier, $flux['data']['texte']);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_charger (SPIP)
 * 
 * Chargement de la valeur du champ date_depublie du formulaire dater
 * 
 * @param array $flux
 * @return array $flux
 */
function depublie_formulaire_charger($flux){

	// si formulaire dater, charger les données des champs supplémentaires
	if ($flux['args']['form'] == 'dater'){
		$objet=$flux['data']['objet'];
		$id_objet=$flux['data']['id_objet'];

		$row = sql_fetsel("date_depublie,statut", "spip_depublies", "id_objet=".intval($id_objet)." AND objet=".sql_quote($objet));
		$possedeDateDepublie = false;
		
		if (isset($row['date_depublie']) AND
			list($annee_depublie,$mois_depublie,$jour_depublie,$heure_depublie,$minute_depublie) = recup_date($row['date_depublie'], false)) {
			$possedeDateDepublie = true;
			// attention : les vrais dates de l'annee 1 sont stockee avec +9000 => 9001
			// mais reviennent ici en annee 1 par recup_date
			// on verifie donc que le intval($row['date_depublie']) qui ressort l'annee
			// est bien lui aussi <=1 : dans ce cas c'est une date sql 'nulle' ou presque, selon
			// le gestionnnaire sql utilise (0001-01-01 pour PG par exemple)
			if (intval($row['date_depublie'])<=1 AND ($annee_depublie<=1) AND ($mois_depublie<=1) AND ($jour_depublie<=1))
				$possedeDateDepublie = false;
		}
		else{
			$annee_depublie = $mois_depublie = $jour_depublie = 0;
			$heure_depublie = $minute_depublie = 00;
		}
		// attention, si la variable s'appelle date ou date_depublie, le compilo va
		// la normaliser, ce qu'on ne veut pas ici.
		$flux['data']['afficher_date_depublie'] = ($possedeDateDepublie?$row['date_depublie']:'');
		$flux['data']['date_depublie_jour'] = dater_formater_saisie_jour($jour_depublie,$mois_depublie,$annee_depublie);
		$flux['data']['date_depublie_heure'] = "$heure_depublie:$minute_depublie";
		$flux['data']['sans_depublie'] = !$possedeDateDepublie;
	}
  
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_verifier (SPIP)
 * 
 * Vérification de la valeur du champ date_depublie du formulaire dater
 * 
 * @param array $flux
 * @return array $flux
 */
function depublie_formulaire_verifier($flux){
	if ($flux['args']['form'] == 'dater' && _request('changer')){
		$k='date_depublie';
		if(_request('sans_depublie')!=1 && _request('date_jour') && _request($k."_jour") && (dater_recuperer_date_saisie(_request('date_jour')) >= dater_recuperer_date_saisie(_request($k."_jour"))))
			$flux[$k] = _T('depublie:erreur_date_superieure');
		else if ($v=_request($k."_jour") AND !dater_recuperer_date_saisie($v))
			$flux[$k] = _T('format_date_incorrecte');
		elseif ($v=_request($k."_heure") AND !dater_recuperer_heure_saisie($v))
			$flux[$k] = _T('format_heure_incorrecte');
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_traiter (SPIP)
 * 
 * Traitement du champ date_depublie du formulaire dater
 * 
 * @param array $flux
 * @return array $flux
 */
function depublie_formulaire_traiter($flux){
	if ($flux['args']['form'] == 'dater' && _request('changer')){

		//récupère les arguments objet/id_objet
		$objet=$flux['args']['args'][0];
		$id_objet=$flux['args']['args'][1];

		//on teste si il y a déjà une entrée dans spip_depublies
		$possedeDateDepublie = sql_getfetsel('date_depublie', "spip_depublies", 'id_objet='.intval($id_objet).' AND objet='.sql_quote($objet));

		$set = array();
		$set['statut']= lire_config('depublie/statut_depublie','prepa');
		$set['objet']=$objet;
		$set['id_objet']=$id_objet;

		if($objet && intval($id_objet)) {
			if (_request('date_depublie_jour') && !_request('sans_depublie') ){
				$d = dater_recuperer_date_saisie(_request('date_depublie_jour'));
				if (!$h = dater_recuperer_heure_saisie(_request('date_depublie_heure')))
					$h = array(0,0);
				$set['date_depublie'] = sql_format_date($d[0], $d[1], $d[2], $h[0], $h[1]);

				//update ou insert
				if ($possedeDateDepublie){
					sql_updateq('spip_depublies', $set, 'id_objet='.intval($id_objet).' AND objet='.sql_quote($objet));
				} else {
					sql_insertq('spip_depublies',$set);
				}
			} else if ($possedeDateDepublie){
				sql_delete('spip_depublies', 'id_objet='.intval($id_objet).' AND objet='.sql_quote($objet));
			}
		}
	}
	
	return $flux;
}

/**
 * Insertion dans le pipeline post_edition (SPIP)
 * En cas de configuration de dépublication automatique, gérer la date de dépublication au changement de statut en publié
 * 
 * @param array $flux
 * @return array $flux
 */ 
function depublie_post_edition($flux){
	$duree = lire_config('depublie/publication_duree');

	if ($duree>0 
		and ($action = $flux['args']['action']) == 'instituer' // action instituer
		and ($table = $flux['args']['table']) == table_objet_sql('article') // on institue un article
		and ($statut_ancien = $flux['args']['statut_ancien']) != ($statut = $flux['data']['statut']) // le statut a été modifié
		and $statut == 'publie' // uniquement en cas de publication
		and $id_objet = $flux['args']['id_objet'] // on a bien un identifiant
	) {

		$objet='article';
		$infos_article = sql_fetsel('id_rubrique, id_secteur','spip_articles','id_article='.intval($id_objet));
		$id_secteur = $infos_article['id_secteur'];
		$id_rubrique = $infos_article['id_rubrique'];
		$id_secteur_choisi= explode(',',lire_config('depublie/secteur_depublie'));
		$id_rubrique_choisie= explode(',',lire_config('depublie/rubrique_depublie'));

		//seulement si secteur ou rubrique sont dans la config
		if(
			in_array($id_secteur,$id_secteur_choisi)
			OR in_array($id_rubrique,$id_rubrique_choisie)
		){

			//on récupère la configuation de la durée de publication
			$periode= lire_config('depublie/publication_periode');
			$duree= lire_config('depublie/publication_duree');
			$set['statut']=lire_config('depublie/statut_depublie','prepa');

			// jour
			if ($periode == 'jours') {
				$set['date_depublie'] = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('n'),date('j')+$duree,date('Y')));
			}
			// ou mois
			else {
				$set['date_depublie'] = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('n')+$duree,date('j'),date('Y')));
			}

			// détecter si il y a déjà une date de dépublication
			$row = sql_fetsel('date_depublie', "spip_depublies", "id_objet=".intval($id_objet)." AND objet=".sql_quote($objet));
				$possedeDateDepublie = false;
				if (isset($row['date_depublie'])) $possedeDateDepublie = true;
			
				$set['objet']=$objet;
				$set['id_objet']=$id_objet;

			// update ou insert
			if($possedeDateDepublie == true) {
				sql_updateq('spip_depublies', $set, "id_objet=".intval($id_objet)." AND objet=".sql_quote($objet));
			} else {
				sql_insertq('spip_depublies',$set);
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline taches_generales_cron (SPIP)
 * 
 * Ajouter les tâches de CRON deux fois par jour
 * 
 * @param array $taches
 * @return array $taches
 */
function depublie_taches_generales_cron($taches){
	$taches['depublier'] = 60*60*12; // 2 fois par jour
	return $taches;
}

?>
