<?php

include_spip('base/abstract_sql');

//configure la base spip et les metas
function archive_install($action){
	//version en cours
	$archive_version = 0.5;
	
   switch ($action){
       case 'test':
           //Contrôle du plugin à chaque chargement de la page d'administration
           // doit retourner true si le plugin est proprement installé et à jour, false sinon
		   if ((!isset($GLOBALS['meta']['archive_version'])) || $GLOBALS['meta']['archive_version'] < $archive_version) {
			   //lance la mise à jour
			   return archive_installer($archive_version);
		   } else {
			   //on est à jour
			   return true;
		   }
       break;
       case 'install':
           //Appel de la fonction d'installation. Lors du clic sur l'icône depuis le panel.
           //quand le plugin est activé et test retourne false
		   return archive_installer($archive_version);
       break;
       case 'uninstall':
           //Appel de la fonction de suppression
           //quand l'utilisateur clickque sur "supprimer tout" (disponible si test retourne true)
		   return archive_uninstaller();
       break;
   }
}

//configure la base spip
function archive_installer($archive_version) {	
	//recupere les champs de spip_articles
	$desc = spip_abstract_showtable("spip_articles", '', true);
	//ajoute le champ archive si champ inexistant
	if (!isset($desc['field']['archive'])){
			spip_query("ALTER TABLE spip_articles ADD `archive` BOOL AFTER `statut`");
	}
	//ajoute le champ archive_date si champ inexistant
	if (!isset($desc['field']['archive_date'])){
			spip_query("ALTER TABLE spip_articles ADD `archive_date` DATETIME AFTER `archive`");
	}
	//on précise que le plugin est initialisé donc base de donnée modifiée
	ecrire_meta('archive_version',$archive_version);
	//regenere le cache des metas
	ecrire_metas();
	//retourne que tout ok
	$ok = true;

}


//supprime les données de la base spip
function archive_uninstaller() {

	//ajoute le champ archive si le plugin n'est pas initialise
	$desc = spip_abstract_showtable("spip_articles", '', true);
	if (isset($desc['field']['archive'])){
			spip_query("ALTER TABLE spip_articles DROP `archive`");
	}
	if (isset($desc['field']['archive_date'])){
			spip_query("ALTER TABLE spip_articles DROP `archive_date`");
	}
	//on précise que le plugin est initialisé donc base de donnée modifiée
	effacer_meta('archive_version');
	//regenere le cache des metas
	ecrire_metas();
	//retourne que tout ok
	$ok = true;

	return $ok;
}

?>