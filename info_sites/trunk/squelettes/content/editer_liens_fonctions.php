<?php
/**
 * On invalide le cache de l'objet pour que la liaison puisse s'afficher sans problème
**/
include_spip('inc/utils');
$objet = _request('objet');
$id_objet = _request('id_objet');

include_spip('inc/invalideur');
suivre_invalideur("id='$objet/$id_objet'");
