<?php
	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008
	 * Yohann Prigent (potter64) repris des travaux de Bernard Blazin (http://www.plugandspip.com )
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
	function guestbook_install($action){
   switch ($action){
       case 'test':
           return (isset($GLOBALS['meta']['guestbook_base_version']) AND ($GLOBALS['meta']['guestbook_base_version']>=$version_base));
       break;
       case 'install':
           guestbook_verifier_tables();
       break;
       case 'uninstall':
           //Appel de la fonction de suppression
           //quand l'utilisateur clique sur "supprimer tout" (disponible si test retourne true)
       break;
   }
}

function guestbook_verifier_tables(){
		include_spip('inc/meta');
		$nom_meta_plug = 'guestbook_base_version';
		$version_base = $GLOBALS['guestbook_base_version'];
        $current_version = 0.0;
        if (   (!isset($GLOBALS['meta'][$guestbook_base_version]) )
                        || (($current_version = $GLOBALS['meta'][$guestbook_base_version])!=$version_cible)){
                if ($current_version==0.0){
						$this_version = '1.0';
						include_spip('base/create');
						include_spip('base/abstract_sql');
                        include_spip('base/guestbook_install');
                        creer_base();
                        ecrire_meta($nom_meta_plug,$this_version,'non');
                }
                ecrire_metas();
        }
}
?>