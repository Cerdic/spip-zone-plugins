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
                        'label' => _T('slick:selecteur'),
                        'explication' => _T('slick:explication_selecteur')
                    )
                ),
                array(
                    'saisie' => 'input',
                    'options' => array(
                        'nom' => 'slide',
                        'label' => _T('slick:slide'),
                        'explication' => _T('slick:explication_slide')
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
                        'explication' => _T('slick:explication_autoplaySpeed'),
                        'afficher_si' => '@autoplay@=="true"'
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
                        'nom' => 'fade',
                        'label' => _T('slick:fade')
                    )
                ),
                array(
                    'saisie' => 'input',
                    'options' => array(
                        'nom' => 'slidesToShow',
                        'label' => _T('slick:slidesToShow'),
                        'afficher_si' => '@fade@=="false"'
                    )
                ),
                array(
                    'saisie' => 'input',
                    'options' => array(
                        'nom' => 'slidesToScroll',
                        'label' => _T('slick:slidesToScroll'),
                        'explication' => _T('slick:explication_slidesToScroll'),
                        'afficher_si' => '@fade@=="false"'
                    )
                ),
                array(
                    'saisie' => 'true_false',
                    'options' => array(
                        'nom' => 'vertical',
                        'label' => _T('slick:vertical'),
                        'defaut' => 'false',
                        'afficher_si' => '@fade@=="false"'
                    )
                ),
                array(
                    'saisie' => 'true_false',
                    'options' => array(
                        'nom' => 'centerMode',
                        'label' => _T('slick:centerMode'),
                        'explication' => _T('slick:explication_centerMode'),
                        'afficher_si' => '@fade@=="false"'
                    )
                ),
                array(
                    'saisie' => 'input',
                    'options' => array(
                        'nom' => 'centerPadding',
                        'label' => _T('slick:centerPadding'),
                        'explication' => _T('slick:explication_centerPadding'),
                        'afficher_si' => '@fade@=="false" && @centerMode@=="true"'
                    )
                ),
                array(
                    'saisie' => 'true_false',
                    'options' => array(
                        'nom' => 'adaptiveHeight',
                        'label' => _T('slick:adaptiveHeight'),
                        'defaut' => 'false',
                        'explication' => _T('slick:explication_adaptiveHeight')
                    )
                ),
                array(
                    'saisie' => 'true_false',
                    'options' => array(
                        'nom' => 'arrows',
                        'label' => _T('slick:arrows'),
                        'defaut' => 'false',
                        'explication' => _T('slick:explication_arrows')
                    )
                ),
                array(
                    'saisie' => 'input',
                    'options' => array(
                        'nom' => 'cssEase',
                        'label' => _T('slick:cssEase'),
                        'explication' => _T('slick:explication_cssEase')
                    )
                ),
                array(
                    'saisie' => 'radio',
                    'options' => array(
                        'nom' => 'lazyload',
                        'label' => _T('slick:lazyload'),
                        'explication' => _T('slick:explication_lazyload'),
                        'datas' => array(
                            'ondemand' => _T('slick:lazyload_ondemand'),
                            'progressive' => _T('slick:lazyload_progressive')
                        )
                    )
                ),
                array(
                    'saisie' => 'true_false',
                    'options' => array(
                        'nom' => 'dots',
                        'label' => _T('slick:dots')
                    )
                ),
                array(
                    'saisie' => 'true_false',
                    'options' => array(
                        'nom' => 'pauseOnHover',
                        'label' => _T('slick:pauseOnHover')
                    )
                ),
                array(
                    'saisie' => 'true_false',
                    'options' => array(
                        'nom' => 'pauseOnDotsHover',
                        'label' => _T('slick:pauseOnDotsHover')
                    )
                ),
                array(
                    'saisie' => 'true_false',
                    'options' => array(
                        'nom' => 'rtl',
                        'label' => _T('slick:rtl')
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