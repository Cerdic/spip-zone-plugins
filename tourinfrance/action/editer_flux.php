<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function action_editer_flux_dist() {
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();
        // pas de flux ? on en cree un nouveau, mais seulement si 'oui' en argument.
        if (!$id_flux = intval($arg)) {
                if ($arg != 'oui') {
                        include_spip('inc/headers');
                        redirige_url_ecrire();
                }
                $id_flux = insert_flux();
        }
        if ($id_flux) $err = revisions_flux($id_flux);
        return array($id_flux,$err);
}
function insert_flux() {
        $champs = array(
                'nom' => _T('tourinfrance:item_nouveau_flux')
        );
       
        // Envoyer aux plugins
        $champs = pipeline('pre_insertion', array(
                'args' => array(
                        'table' => 'spip_tourinfrance_flux',
                ),
                'data' => $champs
        ));
       
        $id_flux = sql_insertq("spip_tourinfrance_flux", $champs);
        return $id_flux;
}
// Enregistrer certaines modifications d'un chat
function revisions_flux($id_flux, $c=false) {
        // recuperer les champs dans POST s'ils ne sont pas transmis
        if ($c === false) {
                $c = array();
                foreach (array('nom', 'race', 'robe', 'annee_naissance', 'infos') as $champ) {
                        if (($a = _request($champ)) !== null) {
                                $c[$champ] = $a;
                        }
                }
        }
       
        include_spip('inc/modifier');
        modifier_contenu('flux', $id_flux, array(
                        'nonvide' => array('nom' => _T('info_sans_titre')),
                        'invalideur' => "id='id_flux/$id_flux'"
                ),
                $c);
}
?>