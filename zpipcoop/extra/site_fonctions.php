<?php
// =======================================================================================================================================
// RIRS esupr le site de mieul http://geekographie.maieul.net/Gerer-le-statut-des-breves
// =======================================================================================================================================

include_spip('inc/puce_statut');
function instituer_site($id_syndic, $id_rubrique, $statut){
        $instituer_site = charger_fonction('instituer_site', 'inc');
        return str_replace('ecrire%2Fecrire%2F%3Fexec%3Dsites%26id_syndic%3D','spip.php%3Fsite',$instituer_site($id_syndic, $statut, $id_rubrique));
}
?>
