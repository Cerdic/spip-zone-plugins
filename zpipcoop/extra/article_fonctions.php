<?php
// =======================================================================================================================================
// RIRS esupr le site de mieul http://geekographie.maieul.net/Gerer-le-statut-des-articles
// =======================================================================================================================================

include_spip('inc/puce_statut');
function instituer_article($id_article, $id_rubrique, $statut){
        $instituer_article = charger_fonction('instituer_article', 'inc');
        return str_replace('ecrire%2Fecrire%2F%3Fexec%3Darticles%26id_article%3D','spip.php%3Farticle',$instituer_article($id_article, $statut, $id_rubrique));
}
?>
