<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_editer_priveperso_charger_dist(){
	

// Si on vient de la page ?exec=priveperso, rub_id dans l'url indique une modification
// d'où récupération des données dans la base.
	if( $rub_id=$_GET["rub_id"] ){
			include_spip('inc/inscrire_priveperso');
			$priveperso = priveperso_recuperer_valeurs($rub_id);
			if ($priveperso['textperso']==='oui'){
				$priveperso_texte = priveperso_texte_recuperer_valeurs($rub_id);
				}
	}
	else{
	 $priveperso = priveperso_post_formulaire();
	 if ($priveperso['textperso']==='oui')	 $priveperso_texte = priveperso_texte_post_formulaire();
	}

	if ($priveperso['textperso']==='oui'){
		return array_merge($priveperso,$priveperso_texte);
	}
	else{
		return $priveperso;
		}
}


function formulaires_editer_priveperso_verifier_dist(){
	$erreurs = array();
	
	
	// recuperer les valeurs postees
	$priveperso = priveperso_post_formulaire();

	// on doit choisir une rubrique à personnaliser
		if ( ($priveperso['rub_id']=='') && (!_request('annuler')) ) {
			$erreurs['message_erreur'] = _T('priveperso:veuillez_choisir_rubrique');
		}

	return $erreurs;
}


function formulaires_editer_priveperso_traiter_dist(){

	include_spip('inc/inscrire_priveperso');

	// recuperer les valeurs postees
	$priveperso = priveperso_post_formulaire();
	$priveperso_texte = priveperso_texte_post_formulaire();
	
	// Suppression des données de la base via le bouton supprimer du formulaire
	if (_request('supprimer')){
			$rub_id = $priveperso['rub_id'];
			sql_delete('spip_priveperso', 'rub_id = ' . intval($rub_id));
			sql_delete('spip_priveperso_texte', 'rub_id = ' . intval($rub_id));
			$res['redirect'] = generer_url_ecrire("priveperso");
			
			return $res;	
		}
		
			if (_request('annuler')){
			$res['redirect'] = generer_url_ecrire("priveperso");
			
			return $res;	
		}

// Inscription des données dans la base.
		if ( ($priveperso['textperso']==='oui') && (!_request('annuler')) ){
			$res1 = priveperso_ecrire_db($priveperso,'spip_priveperso');
			$res2 = priveperso_ecrire_db($priveperso_texte,'spip_priveperso_texte');	
		}
		if ( ($priveperso['textperso']==='non') && (!_request('annuler')) ){
			$res1 = priveperso_ecrire_db($priveperso,'spip_priveperso');
			$res2 = true;
		}

		if ($res1 && $res2){
			$res['message_ok'] = _T('priveperso:perso_sauvegarde');
		}
		else{
			$res['message_erreur'] = _T('priveperso:pb_sauvegarde');	
		}		


	return $res;
}

// recuperer les valeurs postees par le formulaire
function priveperso_post_formulaire() {
	$priveperso = array();

   $trouver_table = charger_fonction('trouver_table', 'base');
   $desc = $trouver_table('priveperso');
	foreach ($desc['field'] as $key => $val){
		if (!_request($key)){ $priveperso[$key] = $GLOBALS['meta'][$key];}
		else {$priveperso[$key] = _request($key);}
	}
	$priveperso['rub_id'] = intval(_request('rub_id'));
	if (!_request('sousrub')) $priveperso['sousrub']='non';
	else $priveperso['sousrub'] = _request('sousrub');	
	if (!_request('textperso')) $priveperso['textperso']='non';
	else $priveperso['sousrub'] = _request('sousrub');

	
	return $priveperso;	
}

// recuperer les valeurs postees par le formulaire
function priveperso_texte_post_formulaire() {
	$priveperso_texte = array();

   $trouver_table = charger_fonction('trouver_table', 'base');
   $desc = $trouver_table('priveperso_texte');
	foreach ($desc['field'] as $key => $val){
		if (!_request($key)){ $priveperso_texte[$key] = _T($key);}
		else {$priveperso_texte[$key] = _request($key);}
	}
	$priveperso_texte['rub_id'] = intval(_request('rub_id'));

	
	return $priveperso_texte;	
}

?>
