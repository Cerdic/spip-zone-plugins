<?php



# hors de la fonction, de facon a ce que la class soit chargee
# meme si le resultat est deja dans le cache (sinon le cache est inexploitable).
# cf. iterateur/data.php

#
# Pour obtenir des cles, il faut s'enregistrer sur 
# https://affiliate-program.amazon.com/gp/advertising/api/detail/main.html

# ces valeurs sont a definir dans mes_options.php
# todo : les passer en CFG
defined('AWS_API_KEY') or define('AWS_API_KEY', 'API KEY');
defined('AWS_API_SECRET_KEY') or define('AWS_API_SECRET_KEY', 'SECRET KEY');

include_spip('lib/AmazonECS.class');

function inc_booksearch_to_array($u) {

    $amazonEcs = new AmazonECS(AWS_API_KEY, AWS_API_SECRET_KEY, 'FR');

    // from now on you want to have pure arrays as response
    $amazonEcs->setReturnType(AmazonECS::RETURN_TYPE_ARRAY);

    $response = $amazonEcs
      ->responseGroup('Large')
      ->category('Books')
      ->search($u);

    // on se limite a la premiere page de resultats
    $u = @$response['Items']['Item'];

    // simplifier le tableau des resultats, on se fiche des liens amazon
    if (is_array($u))
    foreach($u as $k => &$v) {
      #unset($u[$k]['DetailPageURL']);
      #unset($u[$k]['ItemLinks']);
      foreach ($u[$k]['ItemAttributes'] as $k2 => $v2)
        $u[$k][$k2] = $v2;
      unset($u[$k]['ItemAttributes']);
    }
    # cas a 0 reponse
    else
      $u = array();

  return $u;

}

