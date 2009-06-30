<?php
include_spip('base/metas');

$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_METAS', (_DIR_PLUGINS.end($p)));
?>