<?php
function formulaires_editer_acces_auteur_charger_dist($tri = '',$senstri = '',$retour = '') {

	$valeurs = array();

	$valeurs['tri'] = $tri;
	$valeurs['senstri'] = $senstri;
	$certifier = array();
	$utilisateurs = sql_select(array('id_auteur','acces'),'spip_auteurs','acces IN ("0nouveau", "1utilisateur_ok", "2utilisateur_ko")');
	while ($r = sql_fetch($utilisateurs)) {

		$idauteur = $r['id_auteur'];
		$certifier[$idauteur] = $r['acces'];

	}

	$valeurs['certifier'] = $certifier;

	return $valeurs;	
}

function formulaires_editer_acces_auteur_verifier_dist($tri = '',$senstri = '', $retour = '') {
	$erreurs = array();
	return $erreurs;
}

function formulaires_editer_acces_auteur_traiter_dist($tri = '',$senstri = '', $retour = '') {

	$certifier = _request('certifier');
	$message = array();

	if (is_array($certifier)) {
		foreach ($certifier as $id_auteur => $acces) {
			if ($acces = '1utilisateur_ok') $stt = '1comite'; else $stt = '6forum';
			$resupdate = sql_updateq('spip_auteurs',array('acces'=>$acces,'statut'=>$stt),'id_auteur='.sql_quote($id_auteur));
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
	spip_log($certifier,'nn');
	spip_log($resupdate,'nn');
	return $traitement;
}

?>