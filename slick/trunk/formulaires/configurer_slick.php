<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_slick_saisies_dist() {

    $saisies = array(
        array(
            'saisie' => 'case',
            'options' => array(
                'nom' => 'charger',
                'explication' => _T('slick:explication_charger'),
                'label' => _T('slick:charger')
            )
        ),
        array(
            'saisie' => 'fieldset',
            'options' => array(
                'nom' => 'slick',
                'label' => _T('slick:charger'),
                'afficher_si' => '@charger@=="on"'
            ),
            'saisies' => array(
                array(
                    'saisie' => 'input',
                    'options' => array(
                        'nom' => 'selecteur',
                        'label' => _T('slick:selecteur')
                    )
                ),
                array(
                    'saisie' => 'input',
                    'options' => array(
                        'nom' => 'slide',
                        'label' => _T('slick:slide')
                    )
                ),
                array(
                    'saisie' => 'input',
                    'options' => array(
                        'nom' => 'slidesToShow',
                        'label' => _T('slick:slidesToShow')
                    )
                ),
                array(
                    'saisie' => 'input',
                    'options' => array(
                        'nom' => 'slidesToScroll',
                        'label' => _T('slick:slidesToScroll')
                    )
                ),
                array(
                    'saisie' => 'true_false',
                    'options' => array(
                        'nom' => 'autoplay',
                        'label' => _T('slick:autoplay')
                    )
                ),
                array(
                    'saisie' => 'input',
                    'options' => array(
                        'nom' => 'autoplaySpeed',
                        'label' => _T('slick:autoplaySpeed'),
                        'afficher_si' => '@autoplay@=="true"'
                    )
                ),
                array(
                    'saisie' => 'true_false',
                    'options' => array(
                        'nom' => 'fade',
                        'label' => _T('slick:fade')
                    )
                ),
                array(
                    'saisie' => 'input',
                    'options' => array(
                        'nom' => 'speed',
                        'label' => _T('slick:speed')
                    )
                ),
                array(
                    'saisie' => 'true_false',
                    'options' => array(
                        'nom' => 'vertical',
                        'label' => _T('slick:vertical'),
                        'afficher_si' => '@fade@=="false"'
                    )
                ),
                array(
                    'saisie' => 'radio',
                    'options' => array(
                        'nom' => 'lazyload',
                        'label' => _T('slick:lazyload'),
                        'datas' => array(
                            'ondemand' => 'ondemand',
                            'progressive' => 'progressive'
                        )
                    )
                )
            )
        )
    );

    return $saisies;
}

function formulaires_configurer_slick_charger_dist() {
    return lire_config('slick');
}