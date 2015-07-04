<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function inc_unites_dist(){

    return array(
        'poids'=>array(
            'g'=>_T('livraison:label_unite_g'),
            'kg'=>_T('livraison:label_unite_kg')
            ),
        'volume'=>array(
            'cm3'=>_T('livraison:label_unite_cm3'),
            'm3'=>_T('livraison:label_unite_m3'),
            'cl'=>('livraison:label_unite_cl'),
            'l'=>('livraison:label_unite_l')
            ),
        );
}