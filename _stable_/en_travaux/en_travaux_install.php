<?php
/*
 * Plugin En Travaux
 * (c) 2006-2009 Arnaud Ventre, Cedric Morin
 * Distribue sous licence GPL
 *
 */

function entravaux_install($action){
	switch ($action){
			case 'test':
					include_spip('meta');
					if ($GLOBALS['visiteur_session']['id_auteur']
					 AND $GLOBALS['visiteur_session']['statut']=='0minirezo'){
						ecrire_meta('entravaux_id_auteur',$GLOBALS['visiteur_session']['id_auteur']);
					}
					else{
						effacer_meta('entravaux_id_auteur');
					}
					return (true);
					break;
			case 'install':
					break;
			case 'uninstall':
					break;
	}
}

?>
