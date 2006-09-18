<?php

  @header('Content-type: text/html; charset=utf-8');
  include('ecrire/inc_version.php');

  if (!$auteur_session)
    redirige_par_entete('spip.php?page=login&url='.rawurlencode(str_replace('&amp;', '&', self())));

  if ($url = _request('u')) {
    $s = spip_query("SELECT * FROM spip_articles WHERE url_site='".addslashes($url)."'");
    if ($t = spip_fetch_array($s))
      $id_article = $t['id_article'];
    else {
      // on ne devrait pas creer d'article ici, mais simplement proposer un formulaire de creation...
      spip_query("INSERT spip_articles (titre, url_site, descriptif, statut, date)
        VALUES ('".addslashes(_request('t'))."',
        '".addslashes(_request('u'))."',
        '".addslashes(_request('c'))."',
        'prop',
        NOW()
        )");
      $id_article = spip_insert_id();
    }

    redirige_par_entete('spip.php?page=tags&id_article='.$id_article);
  }

  
  echo "Voici le link: <a href=\"javascript:q=location.href;p=document.title;e=window.getSelection();void(open('".$GLOBALS['meta']['adresse_site']."/add.php?u='+encodeURIComponent(q)+'&t='+encodeURIComponent(p)+'&c='+encodeURIComponent(e),'sources','toolbar=no,width=700,height=500'));\">r</a>\n";

?>