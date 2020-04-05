<?php
/***************************************************************************\
 * Plugin Nouvelle Version pour Spip 2.0
 * Licence GPL (c) 2011-2014
 * Modération de la nouvelle version d'un article
 *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}
function versioning_declarer_tables_principales($tables_principales)
{
    // Extension de la table articles
    $tables_principales['spip_articles']['field']['version_of'] = "int(11) NOT NULL";
    return $tables_principales;
}

?>