<?php

function multilang_recuperer_fond($flux) {
  $args = $flux["args"];
  if($args["fond"]=="prive/editer/rubrique") {
    $flux["data"]["texte"] .= "<script type=\"text/javascript\" src=\"".find_in_path("javascript/multilang.js")."\"></script>\n".
             "<script type=\"text/javascript\">\n".
             "var multilang_avail_langs = '".$GLOBALS["meta"]["langues_multilingue"]."'.split(','),\n". 
             "multilang_def_lang = '".$GLOBALS["meta"]["langue_site"]."';\n".
             "multilang_init_lang({fields:':text,textarea',root:'div.cadre-formulaire-editer'});\n".
             "</script>\n";
  }
  return $flux;
}

?>
