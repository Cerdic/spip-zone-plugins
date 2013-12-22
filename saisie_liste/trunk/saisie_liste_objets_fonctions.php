<?php

function liste_objets_numeroter_saisie ($saisie, $nom, $no) {

  $saisie = preg_replace('/name="([^"]+)"/i',
                         'name="' . $nom . '[' . $no . ']' . '[$1]"',
                         $saisie);

  $saisie = preg_replace('/id="champ_([^"]+)"/i',
                         'id="champ_' . $nom . '[' . $no . ']' . '[$1]"',
                         $saisie);

  return $saisie;
}