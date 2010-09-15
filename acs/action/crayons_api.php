<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt
#
# Encapsulation des appels à l'API du plugin crayons afin de maintenir la compatibilité avec les versions successives

/**
 * Encapsule la fonction json var2js, remplacée par crayons_var2js le 11/09/2010 rev 40650
 */
function api_crayons_var2js($var) {
	if (is_callable("crayons_var2js"))
		return crayons_var2js($var);
	else
		return var2js($var);
}
?>