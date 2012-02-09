<?php
include_spip('lib/phpQuery/phpQuery/phpQuery');

function ajax_nav_affichage_final($page) {

  // si l'url contient un parametre 'getbyid' non-vide, on filtre pour n'envoyer que le contenu
  // de l'element correspondant a l'id specifie dans l'url.
  $id = _request('getbyid', $_GET);
  if ($id != '') {
    phpQuery::newDocumentHTML($page);
    $idElement = pq('#' . $id);
    return $idElement->html();
  }

  // si l'url contient un parametre 'getinfos' non-vide, on envoie la langue de la page ainsi
  // que la ou les classes de l'element body dans un string au format JSON.
  $getInfos = _request('getinfos', $_GET);
  if ($getInfos != '') {
    phpQuery::newDocumentHTML($page);
    $lang = pq('html')->attr('lang');

    // Ceci devrait marcher, mais en fait non, probablement parce que dans la spec HTML, body
    // ne peut pas avoir d'attribut class...

    //    phpQuery::newDocumentHTML($page);
    //    $bodyClass = pq('body')->attr('class');

    // ...mais comme SPIP utilise les class sur le body par defaut (le vilain!), on fait un
    // truc moche comme ca :
    $bodyClass = preg_replace('/(\r\n|\n|\r)/m', '', $page);
    $bodyClass = preg_replace('/.*<body[^>]class=[\'\"]([^\'\"]*).*/', '$1', $bodyClass);

    return '{"lang":"' . $lang . '","body_classes":"' . $bodyClass . '"}';
  }

  return $page;
}

?>