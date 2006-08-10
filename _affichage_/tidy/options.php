<?php
/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;


//
// Activation du plugin 'tidy'
//

// Cette ligne active le mode tidy
$xhtml = 'tidy';


// Cette ligne definit le chemin de la commande 'tidy'
// a modifier si cette commande n'est pas dans le PATH du serveur
// par exemple :
// define ('_TIDY_COMMAND', '/usr/local/bin/tidy');
define ('_TIDY_COMMAND', 'tidy');


?>