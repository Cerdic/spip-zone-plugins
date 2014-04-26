<?php
/**
 * Plugin Agenda 4 pour Spip 3.0
 * Licence GPL 3
 *
 * 2006-2011
 * Auteurs : cf paquet.xml
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * 
 * @return array
 */
function formulaires_evenement_participants_charger_dist($evenement,$tri){
	
	if (autoriser('voir','evenement',$evenement)) {
		$valeurs = array(
			'id_evenement'=>$evenement,
			'tri_inscrits'=>$tri,
			'supprimer_lien'=>''
		);
	}
	
	return $valeurs;
}

/**
 * Traiter le post des informations de suppression d'inscription
 *
 * @param string $a
 * @param bool $editable
 * @return array
 */
function formulaires_evenement_participants_traiter_dist($a,$editable=true){
	
	$supprimer = _request('supprimer_lien');
		
	if ($supprimer){
		foreach($supprimer as $k=>$v) {
			if ($lien = lien_verifier_action($k,$v)){
				$lien = explode("-",$lien);
				list($objet_source,$ids,$objet_lie,$idl) = $lien;
				if(autoriser('modifier','evenement',$ids)){
					if ($idl=="*") sql_delete("spip_evenements_participants", "id_evenement=$ids");
					else sql_delete("spip_evenements_participants", "id_evenement=$ids and id_auteur=$idl");
				}
			}
		}
	}
	
	return $res;
}

/**
 * Fonction issue de prive/formulaires/editer_liens.php
 *
 * Les formulaires envoient une action dans un tableau ajouter_lien
 * ou supprimer_lien
 * L'action est de la forme
 * objet1-id1-objet2-id2
 *
 * L'action peut etre indiquee dans la cle, ou dans la valeur
 * Si elle est indiquee dans la valeur, et que la cle est non numerique,
 * on ne la prend en compte que si un submit avec la cle a ete envoye
 *
 * @param string $k
 * @param string $v
 * @return string
 */
function lien_verifier_action($k,$v){
	if (preg_match(",^\w+-[\w*]+-[\w*]+-[\w*]+,",$k))
		return $k;
	if (preg_match(",^\w+-[\w*]+-[\w*]+-[\w*]+,",$v)){
		if (is_numeric($k))
			return $v;
		if (_request($k))
			return $v;
	}
	return '';
}
?>
