<?php
function faire_opt($selection) {
	$retour = "";
	foreach($GLOBALS['liste_chp_ldap'] as $v) {
		$retour .="<option value='".$v."'";
		if($selection!="" && $selection==$v) 
			$retour.= " selected";
		$retour.= ">".$v."</option>\n";
	}
	return $retour;
}

/*
 * Liste les champs de l'annuaire ldap
 */
function lister_champs_ldap() {
	$ldap = spip_connect_ldap();
	$r = @ldap_search($ldap['link'], $ldap['base'], "(objectclass=person)") or die(ldap_error("Search : ".$ldaplink));

	if($r!=FALSE) {
		$entries = ldap_first_entry($ldap['link'], $r);
		$a = ldap_get_attributes($ldap['link'], $entries);
		$b = array();
		for($i=0;$i<$a['count'];$i++) {
			if($a[$a[$i]]['count']==1) {
				$b[] = $a[$i];
			}
		}
		sort($b);
		return $b;
	} else {
		return array(ldap_error($ldap_link));
	}
}


/*
 * Liste les champs de la table auteurs_elargis
 */
function lister_champs_auteurs_elargis() {
	$chp_auteurs = sql_showtable('spip_auteurs_elargis');
	unset($chp_auteurs['field']['id_auteur']);
	return $chp_auteurs['field'];
}
?>