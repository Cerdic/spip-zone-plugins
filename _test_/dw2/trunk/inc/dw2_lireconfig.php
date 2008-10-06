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
	if ($result = @sql_select("nom, valeur","spip_dw2_config")) {
		while ($row = sql_fetch($result))
			$GLOBALS['dw2_param'][$row['nom']] = $row['valeur'];
	}
}

?>