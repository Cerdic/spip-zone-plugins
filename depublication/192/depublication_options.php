<?php
	
include_spip('base/depublication_installer');	
// On vérifie que la structure de la base est okpour c eplugin
depublication_upgrade();

// on regarde si des articles sont à dépublier
depublication_articles();
?>