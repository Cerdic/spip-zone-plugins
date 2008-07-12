<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/acs_presentation');

// http://doc.spip.org/@exec_documents_liste_dist
function exec_acs_documents_liste()
{
  echo acs_commencer_page(_T('titre_page_documents_liste'), "naviguer", "documents");
  echo acs_3colonnes(acs_documents_liste_affiche_gauche(), acs_documents_liste_affiche_milieu(), false);
  echo fin_page();
}

function acs_documents_liste_affiche_gauche() {
  // boite "voir en ligne" - donc traduction par spip
  return acs_info_box(
    _T('info_document'),
    false,
    false,
    _T('texte_recapitiule_liste_documents'), // fo pô capitiulé ! ;-)
    _DIR_PLUGIN_ACS."img_pack/acs_config-24.gif");
}

function acs_documents_liste_affiche_milieu() {
  // recupere les types
  $res = spip_query("SELECT * FROM spip_types_documents");
  while ($row = spip_fetch_array($res))
    $types[$row['id_type']] = $row;

  $result = spip_query("SELECT docs.id_document AS id_doc, docs.id_type AS type, docs.fichier AS fichier, docs.date AS date, docs.titre AS titre, docs.descriptif AS descriptif, lien.id_rubrique AS id_rub, rubrique.titre AS titre_rub FROM spip_documents AS docs, spip_documents_rubriques AS lien, spip_rubriques AS rubrique WHERE docs.id_document = lien.id_document AND rubrique.id_rubrique = lien.id_rubrique AND docs.mode = 'document' ORDER BY docs.date DESC");

  while($row=spip_fetch_array($result)){
      $titre=$row['titre'];
      $descriptif=$row['descriptif'];
      $date=$row['date'];
      $id_document=$row['id_doc'];
      $id_rubrique=$row['id_rub'];
      $titre_rub = typo($row['titre_rub']);
      $fichier = $row['fichier'];

      if (!$titre) $titre = _T('info_document').' '.$id_document;
      $r .= acs_affiche_document($id_document, $titre, $types, $row, $date, $descriptif, $fichier, _T('info_dans_rubrique'), "id_rubrique=$id_rubrique", $titre_rub);
  }

  $result = spip_query("SELECT docs.id_document AS id_doc, docs.id_type AS type, docs.fichier AS fichier, docs.date AS date, docs.titre AS titre, docs.descriptif AS descriptif, lien.id_article AS id_art, article.titre AS titre_art FROM spip_documents AS docs, spip_documents_articles AS lien, spip_articles AS article WHERE docs.id_document = lien.id_document AND article.id_article = lien.id_article AND docs.mode = 'document' ORDER BY docs.date DESC");

  while($row=spip_fetch_array($result)){
      $titre=$row['titre'];
      $descriptif=$row['descriptif'];
      $date=$row['date'];
      $id_document=$row['id_doc'];
      $id_article=$row['id_art'];
      $titre_art = typo($row['titre_art']);
      $fichier = $row['fichier'];

      if (!$titre) $titre = _T('info_document').' '.$id_document;
      $r .= acs_affiche_document($id_document, $titre, $types, $row, $date, $descriptif, $fichier, ucfirst(_T('info_article')), "id_article=$id_article", $titre_art);
  }
  return $r;
}

function acs_affiche_document($id_document, $titre, $types, $row, $date, $descriptif, $fichier, $label, $parent, $titre_parent) {
  $icon = find_in_path('vignettes/'.$types[$row['type']]['extension'].'.png');
  if (!$icon)
    $icon = "doc-24.gif";
  $f = $fichier;
  if (!(substr($f, 0, 7) == 'http://'))
    $f = _DIR_RACINE.$f;
  if (!is_readable($f))
    $class = ' class="alert"';
  else
    $class = '';

  $r = debut_cadre_relief($icon, true);
  $r .= '<div style="margin-'.$GLOBALS['spip_lang_left'].': 66px;">'."<small style=\"float:right\"$class>doc".$id_document."</small><b><a href=\"".$f."\">$titre</a></b> (" . $types[$row['type']]['titre'] . ', ' . affdate($date) . ") </div>";
  if ($descriptif)
    $r .=  "<div style=\"margin-".$GLOBALS['spip_lang_left'].": 66px;\">".propre($descriptif)."</div>";
  else
    $r .=  "<div style=\"margin-".$GLOBALS['spip_lang_left'].": 66px;\"><tt>$fichier</tt></div>";
  $r .=  "<br />".$label." <a href='" . generer_url_ecrire("naviguer", $parent) . "'>$titre_parent</a>";
  $r .= fin_cadre_relief(true);
  return $r;
}
?>
