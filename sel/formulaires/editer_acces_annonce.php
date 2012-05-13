<?php
function formulaires_editer_acces_annonce_charger_dist($tri = '',$senstri = '',$retour = '') {

	$valeurs = array();

	$valeurs['tri'] = $tri;
	$valeurs['senstri'] = $senstri;
	$valider = array();
	$annonces = sql_select(array('id_annonce','statut'),'spip_annonces','statut IN ("0nouvelle", "1annonce_ok", "2annonce_ko")');
	while ($r = sql_fetch($annonces)) {

		$idannonce = $r['id_annonce'];
		$valider[$idannonce] = $r['statut'];

	}

	$valeurs['valider'] = $valider;

	return $valeurs;	
}

function formulaires_editer_acces_annonce_verifier_dist($tri = '',$senstri = '', $retour = '') {
	$erreurs = array();
	return $erreurs;
}

function formulaires_editer_acces_annonce_traiter_dist($tri = '',$senstri = '', $retour = '') {

	$valider = _request('valider');
	$message = array();

	if (is_array($valider)) {
		foreach ($valider as $id_annonce => $statut) {
			$resupdate = sql_updateq('spip_annonces',array('statut'=>$statut),'id_annonce='.sql_quote($id_annonce));
			if ($resupdate)	{
				$message_stt = 'message_ok';
				$message_lang = _T('sel:msgok_mise_a_jour_acces_auteur');
			}
			else {
				$message_stt = 'message_erreur';
				$message_lang = _T('sel:msgerr_mise_a_jour_acces_auteur');
			}
		}
	}
	$traitement[$message_stt] = $message_lang;
	$retour = parametre_url($retour,'tri',$tri);
	$retour = parametre_url($retour,'senstri',$senstri);
	$retour = parametre_url($retour,'var_mode','calcul');
	$retour = ancre_url ($retour, 'pagination_utilisateurs');
	$traitement['redirect'] = $retour;
	return $traitement;
}

?>