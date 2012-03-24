<?php

function formulaires_ajax_nav_config_charger_dist() {

  $res = sql_select('valeur', 'spip_meta', 'nom="ajax_nav_config"');
  if (sql_count($res) == 1) {
    $options = sql_fetch($res);
    $options = unserialize($options['valeur']);

    /* evite les problemes lors de mises a jour du plugin */
    if ( ! $options['autoReplaceDivs'] ) {
      $options['autoReplaceDivs'] = 'on';
    }

    return $options;
  }

  // Valeurs par defaut :
  $options = array(
		   /* les types de page qui seront chargees en ajax. */
		   "pagesToAjaxify"	=> "sommaire article rubrique",
		   /* les id des div a charger en ajax. */
		   "ajaxDivs"		=> "contenu spip-admin",
		   /* les id des div a recharger en cas de changement de langue. */
		   "localizedDivs"	=> "navigation",
		   /* Active les urls hashbang pour les navigateurs html4 */
		   "html4Fallback"	=> "",
		   /* Utilise la lib modernizr fournie avec le plug */
		   "useModernLib"	=> "on",
		   /* Utilise la lib history.js fournie avec le plug */
		   "useHistoryLib"	=> "on",
		   /* Remplace les divs automatiquement */
		   "autoReplaceDivs"	=> "on",
		   );
  return $options;
}

function formulaires_ajax_nav_config_verifier_dist() {

  $erreurs = array();

  foreach(array('pagesToAjaxify', 'ajaxDivs', 'localizedDivs') as $i => $key) {
    if (filter_var(_request($key), FILTER_VALIDATE_REGEXP,
		   array(
			 'options' => array(
					    'regexp' => "/^([a-zA-Z0-9\-\_]*[\s\t]*)*$/")
			 )
		   ) === FALSE) {
      $erreurs[$key] = "saisie non-valide.";
    }
  }

  return $erreurs;
}

function formulaires_ajax_nav_config_traiter_dist() {

  $options =
    array(
	  'pagesToAjaxify'	=> _request('pagesToAjaxify'),
	  'ajaxDivs'		=> _request('ajaxDivs'),
	  'localizedDivs'	=> _request('localizedDivs'),
	  'html4Fallback'	=> _request('html4Fallback'),
	  'useModernLib'	=> _request('useModernLib'),
	  'useHistoryLib'	=> _request('useHistoryLib'),
	  'autoReplaceDivs'	=> _request('autoReplaceDivs'),
	  );

  $options = array(
		   'nom' => 'ajax_nav_config',
		   'valeur' => serialize($options),
		   );

  $result = sql_count(sql_select('*', 'spip_meta', 'nom="ajax_nav_config"'));

  if ($result != 0) {
    sql_updateq('spip_meta', $options, 'nom = "ajax_nav_config"');
  } else {
    sql_insertq('spip_meta', $options);
  }

  return array('message_ok'=>'La configuration a &eacute;t&eacute; enregistr&eacute;e. ');
}

?>