<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_COTES',(_DIR_PLUGINS.end($p)));

$forcer_lang = true;

// structure des tables
require(_DIR_PLUGIN_COTES.'/base/table_cotes_etudiants.php');
require(_DIR_PLUGIN_COTES.'/base/table_cotes_exercices.php');
require(_DIR_PLUGIN_COTES.'/base/table_cotes_cotes.php');
require(_DIR_PLUGIN_COTES.'/base/table_cotes_classes.php');
require(_DIR_PLUGIN_COTES.'/base/table_cotes_mails.php');
?>
