<?php	if (!defined("_ECRIRE_INC_VERSION")) return;	include_spip('inc/actions');	include_spip('inc/editer');	function formulaires_editer_lien_charger_dist($id_blink='new', $retour=''){		$valeurs = formulaires_editer_objet_charger('lien', $id_blink, '', '', $retour, '');		return $valeurs;	}	function formulaires_editer_lien_verifier_dist($id_blink='new', $retour=''){		$erreurs = formulaires_editer_objet_verifier('lien', $id_blink, array('identifiant_blink'));		return $erreurs;	}	function formulaires_editer_lien_traiter_dist($id_blink='new', $retour=''){		return formulaires_editer_objet_traiter('lien', $id_blink, '', '', $retour, '');	}?>