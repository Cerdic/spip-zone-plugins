<?php

/*! \file doc2img_install.php
 *  \brief tout ce qui concerne l'installation et la desinstallation du plugin
 *
 */
     
/*! \brief Vigorously erase files and directories.
 *  \param $fileglob mixed If string, must be a file name (foo.txt), glob pattern (*.txt), or directory name.
 *  If array, must be an array of file names, glob patterns, or directories.
 *  \return true si tout s'est bien pass�
 *  \author bishop http://fr.php.net/manual/fr/function.unlink.php#53549  
 */
function rm($fileglob) {
    spip_log($fileglob,'doc2img');
    if (is_string($fileglob)) {
        if (is_file($fileglob)) {
            return unlink($fileglob);
        } else if (is_dir($fileglob)) {
            $ok = rm("$fileglob/*");
            if (! $ok) {
            return false;
            }
            return rmdir($fileglob);
        } else {
            $matching = glob($fileglob);
            if ($matching === false) {
               trigger_error(sprintf('No files match supplied glob %s', $fileglob), E_USER_WARNING);
                return false;
            } 
            $rcs = array_map('rm', $matching);
            if (in_array(false, $rcs)) {
                return false;
            }
        } 
    } else if (is_array($fileglob)) {
        $rcs = array_map('rm', $fileglob);
        if (in_array(false, $rcs)) {
            return false;
        }
    } else {
        trigger_error('Param #1 must be filename or glob pattern, or array of filenames or glob patterns', E_USER_ERROR);
        return false;
    }

    return true;
}


/*! \brief determine si installation, mise � jour, ou supression
 *  
 *  Cette fonction est app�l�e � chaque acces � la page /ecrire/?exec=admin_plugin. Elle configure la base spip et les metas n�cessaire au bon fonctionnement du plugin
 *  \param $action soit test, install, uninstall
 *
 */   
function doc2img_install($action){
    //on r�cupere la version depuis plugin.xml
    $doc2img_infos = plugin_get_infos('doc2img');
	$doc2img_version = $doc2img_infos['version'];
	
    //version en m�moire dans spip
    $doc2img_version_meta = $GLOBALS['meta']['doc2img_version'];

    switch ($action){
        case 'test':
            return (isset($doc2img_version_meta) 
                AND version_compare($doc2img_version_meta,$doc2img_version,">=")); 
			break;
		case 'install':
            doc2img_installer($doc2img_version_meta,$doc2img_version);
			break;
		case 'uninstall':
            doc2img_uninstaller();
			break;
	}
}
/*! \brief installeur
 * 
 *  Effectue l'ensemble des actions necessaire au bon fonctionnement du plugin :
 *  - mise en place de la table doc2img
 *  - configuration par d�faut de cfg
 *  - definition de la version en cours du plugin
 *  
 *  \param $version version au moment de l'installation, NULL lors d'une premiere installation
 *  \param $version_finale version sp�cifi�e dans plugin.xml
 */   
function doc2img_installer($version,$version_finale) {

    //m�thode  $version correspond � la version install�e
    //on met � jour � partir de cette version
    //c'est pourquoi pas break;
    //recherche du case correspondant � la version install�e
    //mise � jour jusqu'� la version finale


    spip_log('installation ou mise � jour','doc2img');
    
    // on fait les mise � jour qui suive $version
	// $version == version en cours
    switch ($version) {
        //le plugin n'a �t� jamais install�
        case NULL :
            //on cr�� une table qui servira � faire correspondre les images avec  les documents
            spip_log('cr�ation de la table','doc2img');
			sql_create(
				'spip_doc2img',
				array(
					'id_doc2img' => 'BIGINT (21) AUTO_INCREMENT', 
                	'id_document' => 'BIGINT (21) NOT NULL DEFAULT 0',
 					'fichier' => 'VARCHAR(255) NOT NULL DEFAULT \'\''
				), 
				array(
					'PRIMARY KEY' => 'id_doc2img'
				)
            );
            spip_log('table spip_doc2img cr��e','doc2img');
            //on defini un repertoire de stockage
            spip_log(_DIR_IMG,'doc2img');
            $dir_doc2img = getcwd().'/'._DIR_IMG.'doc2img/';
            mkdir($dir_doc2img);
            spip_log('cr�ation repertoire '.$dir_doc2img,'doc2img');
        //passage en 0.2, rien � faire
        case 0.1 :
        //on attaque la 0.3
        //initialisation d'une configuration par d�faut
        case 0.2 :
            //d�finition des param�tres de base
            $cfg = array(
                "format_document" => "pdf,tif",
                "repertoire_cible" => "doc2img",
                "format_cible" => "png",
                "proportion" => "on"
            );
        	//par d�faut juste le champ d'id text_area est corrigeable
			ecrire_meta('doc2img',serialize($cfg));
        //passage en 0.4, rien � faire
		case 0.3 :
		//passage en 0.5
		case 0.4 :
		    //on permet la num�rotation des page
            sql_alter(
				"TABLE spip_doc2img 
	                ADD page INT NOT NULL DEFAULT 0;"
			);
		//passage en 0.9
		case 0.8 :
			sql_alter(
				"TABLE spip_doc2img 
					ADD UNIQUE document (id_document, page)"
			);
    }

    //on met � jour la version du plugin
    ecrire_meta('doc2img_version', $version_finale);
}

/*! \brief desinstalleur
 * 
 *  Effectue l'ensemble des actions necessaire � la suppresion d�finitive du plugin :
 *  - retrait de la table doc2img et de ses donn�es
 *  - suppression du repertoire par d�faut
 *  - definition de la version en cours du plugin
 *  
 */   
function doc2img_uninstaller() {

    include_spip('cfg_options');

    //la desinstallation se lance depuis la racine du site et non ecrire/
    spip_log('suppression compl�te','doc2img');

    //on n�ttoie ce qui a �t� install�e
    //supprime la table doc2img
	sql_drop_table("spip_doc2img");
	
	spip_log('suppression table','doc2img');
	//on supprime le repertoire cr�� et son contenu
	$dir_doc2img = getcwd().'/'._DIR_IMG.lire_config('doc2img/repertoire_cible');
	spip_log('suppression des doc2img :'.$dir_doc2img,'doc2img');
    rm($dir_doc2img);
 
	//supprime les log
	spip_log('suppression des log : '.getcwd().'../tmp/doc2img.log*','doc2img');

	rm(getcwd().'../tmp/doc2img.log*');

    //on efface la meta indiquant la version install�e
    effacer_meta('doc2img_version');
}
?>
