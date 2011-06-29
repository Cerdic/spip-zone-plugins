<?php// D�broussailleur : JLuc//// OK : // - Param�trage du traitement =//		* sp�cification de la table destinataire pour le formulaire//		* correspondance des champs entre formulaire et table//		sous la forme d'une suite de couples champform1|champtable// - Enregistrement dans la table//// Approximatif ou foireux :// - Toute la m�canique de gestion des "modification de r�ponses existantes"//	est dupliqu�e depuis le traitement "enregistrement"// 	Notamment, il utilise les valeurs de cette configuration qui doit donc �tre activ�e//	Il faudrait soit retirer tout �a et faire au plus simple//	soit mutualiser le code ET la config ET l'interface de config !// - La fonction traiter_table_update_dist n'a pas encore �t� touch�e, // 	elle doit �tre adapt�e pour ce nouveau traitement//// S�curit�if (!defined("_ECRIRE_INC_VERSION")) return;function traitement_date_fr_vers_sql($date) {	sscanf ($date, "%2d/%2d/%4d%s",$jour,$mois,$an,$s);	return "$an-$mois-$jour";};// exemples :// function traitement_champ_bateau_couleur ($valeur, $table, $nom) // function traitement_champ_date_debut ($valeur, $table, $nom) // function traitement_champ_evenement_date_debut ($valeur, $table, $nom) // function traitement_champ ($valeur, $table, $nom)/* exemple d'usage :function traitement_champ ($valeur, $table, $nom) {	// ICI appels des fonctions de conversions	if (strpos($nom,'date')===0) {		$valeur = traitement_date_fr_vers_sql($valeur);		echo "<br>Re�u date fr et convertit vers SQL = $valeur";	};};*/function traiter_table_dist($args, $retours){		include_spip('inc/formidable');	include_spip('base/abstract_sql');	$options = $args['options'];		$table_destinataire = $options['table_destinataire'];		$correspondance_champs_formulaire_table = $options['correspondance_champs_formulaire_table'];	$correspondance_champs_formulaire_table = saisies_chaine2tableau($correspondance_champs_formulaire_table);		$formulaire = $args['formulaire'];	$id_formulaire = intval($formulaire['id_formulaire']);	$saisies = unserialize($formulaire['saisies']);	$saisies = saisies_lister_par_nom($saisies);	// La personne a-t-elle un compte ?	global $auteur_session;	$id_auteur = $auteur_session ? intval($auteur_session['id_auteur']) : 0;		// On cherche le cookie et sinon on le cr�e	$nom_cookie = formidable_generer_nom_cookie($id_formulaire);	if (isset($_COOKIE[$nom_cookie]))		$cookie = $_COOKIE[$nom_cookie];	else {		include_spip("inc/acces");		$cookie = creer_uniqid();	}		// On regarde si c'est une modif d'une r�ponse existante	$modif_reponse = 		$id_formulaires_reponse 			= intval(_request('deja_enregistre_'.$id_formulaire));		// Si la moderation est a posteriori ou que la personne est un boss, on publie direct	if ($options['moderation'] == 'posteriori' or autoriser('instituer', 'formulaires_reponse', $id_formulaires_reponse, null, array('id_formulaire'=>$id_formulaire, 'nouveau_statut'=>'publie')))		$statut='publie';	else		$statut = 'prop';		// Si ce n'est pas une modif d'une r�ponse existante, on cr�e d'abord la r�ponse	if (!$id_formulaires_reponse){		$id_formulaires_reponse = sql_insertq(			'spip_formulaires_reponses',			array(				'id_formulaire' => $id_formulaire,				'id_auteur' => $id_auteur,				'cookie' => $cookie,				'ip' => $GLOBALS['ip'],				'date' => 'NOW()',				'statut' => $statut			)		);		// Si on a pas le droit de r�pondre plusieurs fois ou que les r�ponses seront modifiables, il faut poser un cookie		if (!$options['multiple'] or $options['modifiable']){			include_spip("inc/cookie");			// Expiration dans 30 jours			spip_setcookie($nom_cookie, $_COOKIE[$nom_cookie] = $cookie, time() + 30 * 24 * 3600);		}	}		// Si l'id n'a pas �t� cr�� correctement alors erreur	if (!($id_formulaires_reponse > 0)){		$retours['message_erreur'] .= "\n<br/>"._T('formidable:traiter_enregistrement_erreur_base');	}	// Sinon on continue � mettre � jour	else {		$champs = array();		$insertions = array();		foreach($saisies as $nom => $saisie){			// On ne prend que les champs qui ont effectivement �t� envoy�s par le formulaire			if (($valeur = _request($nom)) !== null){				$champs[] = $nom;				if (isset ($correspondance_champs_formulaire_table[$nom]))					$colname = $correspondance_champs_formulaire_table[$nom];				else {					$retours['message_erreur'] .= "\n<br/>Erreur : le champ du formulaire ".$nom."n'a pas de correspondance d�clar�e dans la table utilisateur";					break;				};								// traitements des valeurs avant enregistrement : fonction de la table destinataire et du champ destination				if (function_exists($f = 'traitement_champ_'.$table_destinataire.'_'.$nom) 					OR function_exists($f = 'traitement_champ_'.$nom)					OR function_exists($f = 'traitement_champ'))					$valeur = $f($valeur, $table_destinataire, $nom);									$inserts[$colname] = (is_array($valeur) ? serialize($valeur) : $valeur);			}		}		if ($modif_reponse) // On modifie l'enregistrement trouv�			$inserts['id_formulaires_reponse'] = $id_formulaires_reponse;			// S'il y a bien des choses � modifier		if ($champs) {//			if ($modif_reponse)//				sql_updateq ($table_destinataire, $inserts);//			else			// On ins�re les nouvelles valeurs			$id= sql_insertq ($table_destinataire, $inserts);			if (!$id)					$retours['message_erreur'] .= "\n<br/>Erreur : l'insertion dans la table utilisateur ne se fait pas bien.".sql_error();		};	}	return $retours;}function traiter_table_update_dist($id_formulaire, $traitement, $saisies_anciennes, $saisies_nouvelles){	// Si des champs ont �t� supprim�s du formulaire, 	// il faut supprimer les colonnes de la table	// mais cela se r�gle en dehors de formidable	// et mettre � jour la table de correspondance	// mais le plugin ne fait pas de v�rification au moment du changement.}?>