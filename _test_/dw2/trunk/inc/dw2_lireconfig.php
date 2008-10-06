<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| fonctions : declare les superglobale de config
+--------------------------------------------+
*/
//
// initialise globales config
function lire_dw2_config() {
	if ($result = @spip_query("SELECT nom, valeur FROM spip_dw2_config")) {
		while ($row = spip_fetch_array($result))
			$GLOBALS['dw2_param'][$row['nom']] = $row['valeur'];
	}
}

?>
