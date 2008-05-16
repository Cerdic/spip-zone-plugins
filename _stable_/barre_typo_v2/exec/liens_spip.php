<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/minipres');
include_spip('inc/presentation');
include_spip('inc/documents');


function exec_liens_spip() {

  $rubroot = array();
  $GLOBALS['rubname'] = array();
  $GLOBALS['rubtree'] = array();
  $GLOBALS['rubart'] = array();
  $GLOBALS['rubbrv'] = array();
  $GLOBALS['artname'] = array();
  $GLOBALS['brvname'] = array();

  $fetch = function_exists('sql_fetch')?'sql_fetch':'spip_fetch_array';
  
  echo install_debut_html(_T('icone_doc_rubrique'));

  echo "<style type='text/css'>\n";
  echo "a {padding-bottom: 6px;}\n";
  echo "a:hover {border-bottom: dotted 1px red; color: red;}\n";
  echo "div#minipres {width: auto;}\n";
  echo "li {margin-top: 10px;margin-bottom: 10px;}\n";
  echo "</style>\n";

  echo "<script>\n";
  echo "function setlink(value) {\n";
  echo "  var txt = window.opener.document.getElementById('lien_nom1');\n";
  echo "  txt.value=value\n";
  echo "  window.close();\n";
  echo "  return false;\n";
  echo "}\n";
  echo "</script>\n";

  $allrubs = spip_query("SELECT id_rubrique, id_parent, titre FROM spip_rubriques ORDER BY id_rubrique");
  while ($row = $fetch($allrubs)) {
      $GLOBALS['rubname'][$row['id_rubrique']] = $row['titre'];
      if ($row['id_parent'] > 0) {
        $GLOBALS['rubtree'][$row['id_parent']][] = $row['id_rubrique'];
      } else {
        $rubroot[$row['id_rubrique']][] = $row['titre'];
      }
  }

  $allarts = spip_query("SELECT id_rubrique, id_article, titre FROM spip_articles ORDER BY id_article");
  while ($row = $fetch($allarts)) {
    $GLOBALS['artname'][$row['id_article']] = $row['titre'];
    $GLOBALS['rubart'][$row['id_rubrique']][] = $row['id_article'];
  }

  $allbrvs = spip_query("SELECT id_rubrique, id_breve, titre FROM spip_breves ORDER BY id_breve");
  while ($row = $fetch($allbrvs)) {
    $GLOBALS['brvname'][$row['id_breve']] = $row['titre'];
    $GLOBALS['rubbrv'][$row['id_rubrique']][] = $row['id_breve'];
  }

  echo "<ul>\n";
  foreach ($rubroot as $id => $titre) {
    affiche_rubrique($id);
  }
  echo "</ul>\n";
  echo install_fin_html();
}

function affiche_rubrique($id) {
  echo '<li><a href="" onclick="return setlink(\'rub'.$id.'\');"><img src="'._DIR_IMG_PACK.'rubrique-24.gif" align="middle" /> <font style="line-height: 24px; vertical-align: bottom; font-size: 16px; font-weight: bold;">'.$GLOBALS['rubname'][$id]."</font></a>\n";
  if (defined($GLOBALS['rubtree'][$id])) {
    echo "<ul>\n";
    foreach ($GLOBALS['rubtree'][$id] as $sid) {
      affiche_rubrique($sid);
    }
    echo "</ul>\n";
  }
  if ($GLOBALS['rubart'][$id]) {
    echo "<ul>\n";
    foreach ($GLOBALS['rubart'][$id] as $aid) {
      echo '<li><a href="" onclick="return setlink(\'art'.$id.'\');"><img src="'._DIR_IMG_PACK.'article-24.gif" align="middle" /> <font style="line-height: 24px; vertical-align: bottom; font-size: 14px;">'.$GLOBALS['artname'][$aid]."</font></a>\n";
    }
    echo "</ul>\n";
  }
  if ($GLOBALS['rubbrv'][$id]) {
    echo "<ul>\n";
    foreach ($GLOBALS['rubbrv'][$id] as $bid) {
      echo '<li><a href="" onclick="return setlink(\'br'.$id.'\');"><img src="'._DIR_IMG_PACK.'breve-24.gif" align="middle" /> <font style="line-height: 24px; vertical-align: bottom; font-size: 14px;">'.$GLOBALS['brvname'][$bid]."</font></a>\n";
    }
    echo "</ul>\n";
  }
}

?>
