<?php
// =======================================================================================================================================
// RIRS esupr le site de mieul http://geekographie.maieul.net/Gerer-le-statut-des-breves
// =======================================================================================================================================

include_spip('inc/puce_statut');
function instituer_breve($id_breve, $id_rubrique, $statut){
        $instituer_breve = charger_fonction('instituer_breve', 'inc');
        return str_replace('ecrire%2Fecrire%2F%3Fexec%3Dbreves%26id_breve%3D','spip.php%3Fbreve',$instituer_breve($id_breve, $statut, $id_rubrique));
}
?>
