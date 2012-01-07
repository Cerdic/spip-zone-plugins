<?php
// Auteur : JLuc
// 
//
// - Paramétrage du traitement =
//		* spécification de la table destinataire pour le formulaire
//		* correspondance des champs entre formulaire et table
//		sous la forme d'une suite de couples champform1|champtable
//		* SI plusieurs champs du formulaire ont la même destination dans la table (mm notation champ|dest et un par lignes)
//		les valeurs sont fusionnées et insérés avec mise en forme simple label : valeur dans le champ de destination
//		
// - Enregistrement des valeurs saisies dans la table utilisateur
//
// Notes techniques :
// La gestion des "modification de réponses existantes" est une dupplication  
// de cette partie du traitement 'enregistrement'.
// Ce serait bien de mutualiser le code et la config en sortant cette partie des traitements.

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Passe un tableau d'élements en html 
 *
 *
 * @param mixed $tableau
 * @return string html simple 
**/
function saisies_tableau2html($tableau) {
	if ($tableau and is_array($tableau)){
		$chaine = '';
	
		foreach($tableau as $cle=>$valeur){
			$label = trim("$cle:<br/>");
			$ligne = trim("<p>$valeur</p>");
			$chaine .= $label.$ligne;
		}
		$chaine = trim($chaine);
	
		return $chaine;
	}
	// Si c'est déjà une chaine on la renvoie telle quelle
	elseif (is_string($tableau)){
		return $tableau;
	}
	else{
		return '';
	}
}


// convertir le format des dates issu de formidable à un format SQL de type DATE 
function traitement_date_fr_vers_sql ($date) {
	sscanf ($date, "%2d/%2d/%4d%s",$jour,$mois,$an,$s);
	return "$an-$mois-$jour";
};

// Exemples : 
// - $valeur : valeur renvoyée par la saisie
// - $table : table SQL utilisateur destinatrice des données
// - $champ : champ de la table SQL
// - $nom : id de l'input dans le formulaire généré par spip
// 
// function traitement_champ_bateau_couleur ($valeur, $table, $champ, $nom) 
// function traitement_champ_date_debut ($valeur, $table, $champ, $nom) 
// function traitement_champ_evenement_date_debut ($valeur, $table, $champ, $nom) 
// function traitement_champ ($valeur, $table, $nom)

//function traitement_champ_naissance ($valeur, $table, $champ, $nom) {
//	return traitement_date_fr_vers_sql();
//};

/* exemple d'usage :
function traitement_champ ($valeur, $table, $champ, $nom) {
	// Toutes les saisies DATES sont stockées dans un format un champt de type DATE
	if (strpos($nom,'date')===0) {
		$valeur = traitement_date_fr_vers_sql($valeur);
//		echo "<br>Reçu date fr et convertit vers SQL = $valeur";
	};
	return $valeur;
}; */

function traiter_table_dist($args, $retours){
	include_spip('inc/formidable');
	include_spip('base/abstract_sql');
	$options = $args['options'];

	$table_destinataire = $options['table_destinataire'];
	$correspondance_champs_formulaire_table = $options['correspondance_champs_formulaire_table'];
	$correspondance_champs_formulaire_table = saisies_chaine2tableau($correspondance_champs_formulaire_table);
	
	// Détection similaires
	if(array_count_values($correspondance_champs_formulaire_table)!=1) :
		
		$similaires = array_count_values($correspondance_champs_formulaire_table);
			
		foreach($similaires as $val => $nb) {
			//si le nombre de champ utilisant la mm destination est sup à 1
			if($nb>1) $similaire = $val;				
		}
	endif;

	$formulaire = $args['formulaire'];
	$id_formulaire = intval($formulaire['id_formulaire']);
	$saisies = unserialize($formulaire['saisies']);
	$saisies = saisies_lister_par_nom($saisies);
	
	// La personne a-t-elle un compte ?
	global $auteur_session;
	$id_auteur = $auteur_session ? intval($auteur_session['id_auteur']) : 0;

	// On cherche le cookie et sinon on le crée
	$nom_cookie = formidable_generer_nom_cookie($id_formulaire);
	if (isset($_COOKIE[$nom_cookie]))
		$cookie = $_COOKIE[$nom_cookie];
	else {
		include_spip("inc/acces");
		$cookie = creer_uniqid();
	}
	
	// On regarde si c'est une modif d'une réponse existante
	$modif_reponse = 
		$id_formulaires_reponse 
			= intval(_request('deja_enregistre_'.$id_formulaire));

	// Si la moderation est a posteriori ou que la personne est un boss, on publie direct
	if ($options['moderation'] == 'posteriori' or autoriser('instituer', 'formulaires_reponse', $id_formulaires_reponse, null, array('id_formulaire'=>$id_formulaire, 'nouveau_statut'=>'publie')))
		$statut='publie';
	else
		$statut = 'prop';

	// Si ce n'est pas une modif d'une réponse existante, on crée d'abord la réponse
	if (!$id_formulaires_reponse){
		$id_formulaires_reponse = sql_insertq(
			'spip_formulaires_reponses',
			array(
				'id_formulaire' => $id_formulaire,
				'id_auteur' => $id_auteur,
				'cookie' => $cookie,
				'ip' => $GLOBALS['ip'],
				'date' => 'NOW()',
				'statut' => $statut
			)
		);
		// ont récupère l'id_formulaires_reponse incrémenté dans la base de formidale
		// et ont post l'identique dans la table utilisateur
		$id_new = sql_getfetsel('id_formulaires_reponse', 'spip_formulaires_reponses', 'id_formulaires_reponse='. intval($id_formulaires_reponse));
		// ont ajoute a inserts
		$inserts['id_formulaires_reponse'] = $id_new;
		// Si on a pas le droit de répondre plusieurs fois ou que les réponses seront modifiables, il faut poser un cookie
		if (!$options['multiple'] or $options['modifiable']){
			include_spip("inc/cookie");
			// Expiration dans 30 jours
			spip_setcookie($nom_cookie, $_COOKIE[$nom_cookie] = $cookie, time() + 30 * 24 * 3600);
		}
	}

	// Si l'id n'a pas été créé correctement alors erreur
	if (!($id_formulaires_reponse > 0)){
		$retours['message_erreur'] .= "\n<br/>"._T('formidable:traiter_enregistrement_erreur_base');
	}
	// Sinon on continue à mettre à jour
	else {
		$champs = array();
		$insertions = array();
		$concat = array();
		
		foreach($saisies as $nom => $saisie){
			// On ne prend que les champs qui ont effectivement été envoyés par le formulaire
			if (($valeur = _request($nom)) !== null){
				$champs[] = $nom;
				//ont test ceux qui ont été détectés comme ayant une destination similaire
				if (isset($similaire)&&$correspondance_champs_formulaire_table[$nom]==$similaire) {
					$label = $saisies[$nom]['options']['label'];
					$concat[$label]=$valeur;
				}
				if (isset ($correspondance_champs_formulaire_table[$nom])){
					$colname = $correspondance_champs_formulaire_table[$nom];
				}else {
					$retours['message_erreur'] .= "\n<br/>Erreur : le champ du formulaire ".$nom."n'a pas de correspondance déclarée dans la table utilisateur";
					break;
				};

				// traitements des valeurs avant enregistrement : fonction de la table destinataire et du champ destination
				if (function_exists($f = 'traitement_champ_'.$table_destinataire.'_'.$colname) 
					OR function_exists($f = 'traitement_champ_'.$colname)
					OR function_exists($f = 'traitement_champ'))
					$valeur = $f($valeur, $table_destinataire, $colname, $nom);

			$inserts[$colname] = (is_array($valeur) ? serialize($valeur) : $valeur);
			}
		}
		
		if(isset($concat)) {
			$compil = saisies_tableau2html($concat);
			$inserts[$similaire]=$compil;
		}

		if ($modif_reponse) // On modifie l'enregistrement trouvé
			$inserts['id_formulaires_reponse'] = $id_formulaires_reponse;
		

		// S'il y a bien des choses à modifier
		if ($champs) {
//			if ($modif_reponse)
//				sql_updateq ($table_destinataire, $inserts);
//			else
			// echo "<pre>".print_r($champ,1)."</pre>";
			
			// On insère les nouvelles valeurs
			$id= sql_insertq ($table_destinataire, $inserts);
			
			#TEST
			//echo "sql_insertq ($table_destinataire, <pre>".print_r($inserts,1)."</pre>)";
			
			if (!$id)	
				$retours['message_erreur'] .= "\n<br/>Erreur : l'insertion dans la table ($table_destinataire) ne se fait pas bien.".sql_error()."<pre class='reponse_formulaire_erreur'>".print_r($inserts,1)."</pre>";
		};
	}

	return $retours;
}

function traiter_table_update_dist($id_formulaire, $traitement, $saisies_anciennes, $saisies_nouvelles){
	// Si des champs ont été supprimés, il faut supprimer les réponses à ces champs
	// il faut supprimer les colonnes de la table
	// mais cela se régle en dehors de formidable
	// et mettre à jour la table de correspondance
	// mais le plugin ne fait pas de vérification au moment du changement.
}

?>