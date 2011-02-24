<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function traiter_paiement_paypal_dist($args, $retours){
	include_spip('inc/session');
	include_spip('inc/formidable');
	include_spip('base/abstract_sql');
	
	$formulaire = $args['formulaire'];
	$options = $args['options'];
	$saisies = unserialize($formulaire['saisies']);
	$traitements = unserialize($formulaire['traitements']);
	$champs = saisies_lister_champs($saisies);
	
	// Empecher le traitement en AJAX car on sait que le formulaire va rediriger autre part
    refuser_traiter_formulaire_ajax();
    
    // On stocke le montant et la référence de la transaction
    session_start();
    
    
    
    if ($_REQUEST['montant_fixe_1']) $_SESSION['total'] = $_REQUEST['montant_fixe_1'];
    if ($_REQUEST['montant_selection_1']) $_SESSION['total'] = $_REQUEST['montant_selection_1'];
    if ($_REQUEST['montant_1']) $_SESSION['total'] = $_REQUEST['montant_1'];
         
    if (intval($_REQUEST['montant_multiplicateur_1']) > 0) {
	$_SESSION['total'] = $_SESSION['total'] * intval($_REQUEST['montant_multiplicateur_1']);
    } 
    
   
    // ID unique de la transaction
    $_SESSION['ref'] = uniqid();
    $_SESSION['champ_compte_paypal'] = $options['champ_compte_paypal'];
    $_SESSION['champ_devise_paypal'] = $options['champ_devise_paypal'];

    $_SESSION['langue_paypal'] = strtoupper($_GET['lang']);
    if ($_SESSION['langue_paypal'] == '') $_SESSION['langue_paypal'] = 'FR';
	
	$nb_paiement = 0;
    //On compte le nombre de paiement utilisé par le formulaire
    foreach($traitements as $type_traitement=>$options){
		if (substr($type_traitement,0,9) == "paiement_") $nb_paiement++;
	}
	
	// Le formulaire a été validé, on le masque
	$retours['editable'] = false;
	// Si aucun montant n'a été saisie le message pour le paiement n'est pas affiché
	if ($_REQUEST['montant_selection_1'] OR $_REQUEST['montant_fixe_1'] OR $_REQUEST['montant_1']) $retours['message_ok'] .=  "<div class='transaction_ok paypal' style='background: url(".find_in_path('paiement/paypal/logo.png').") no-repeat top left'>"._T('transaction:traiter_message_paypal').'<p><a href="'.find_in_path("paiement/paypal/paiement.php").'"  class="valider"><span>'._T('transaction:valider_paiement').'</span></a></p></div>';

	//enregistrement des résultats
	$options = $args['options'];
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
	$id_formulaires_reponse = intval(_request('deja_enregistre_'.$id_formulaire));
	
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
		$id_formulaires_transactions = sql_insertq(
			'spip_formulaires_transactions',
			array(
				'id_formulaires_reponse' => $id_formulaires_reponse,
				'statut_transaction' => 0,
				'ref_transaction' => $_SESSION['ref']
			)
		);
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
	else{
		$champs = array();
		$insertions = array();
		foreach($saisies as $nom => $saisie){
			// On ne prend que les champs qui ont effectivement été envoyés par le formulaire
			if (($valeur = _request($nom)) !== null){
				$champs[] = $nom;
				$insertions[] = array(
					'id_formulaires_reponse' => $id_formulaires_reponse,
					'nom' => $nom,
					'valeur' => is_array($valeur) ? serialize($valeur) : $valeur
				);
			}
		}
		
		// S'il y a bien des choses à modifier
		if ($champs){
			// On supprime d'abord les champs
			sql_delete(
				'spip_formulaires_reponses_champs',
				array(
					'id_formulaires_reponse = '.$id_formulaires_reponse,
					sql_in('nom', $champs)
				)
			);
			
			// Puis on insère les nouvelles valeurs
			sql_insertq_multi(
				'spip_formulaires_reponses_champs',
				$insertions
			);
		}
	}
//fin
	return $retours;
}

?>
