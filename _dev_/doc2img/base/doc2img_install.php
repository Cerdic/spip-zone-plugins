<?php

/**
* rm() -- Vigorously erase files and directories.
*
* @param $fileglob mixed If string, must be a file name (foo.txt), glob pattern (*.txt), or directory name.
* If array, must be an array of file names, glob patterns, or directories.
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

function clean_file($directory) {
    spip_log('clean_file : '.$directory,'doc2img');
    if( !$dirhandle = @opendir($directory) )
        return;

        while( false !== ($filename = readdir($dirhandle)) ) {
            spip_log("clean :".$filename);
            if( $filename != "." && $filename != ".." ) {
                $filename = $directory. "/". $filename;
                @unlink($filename);
            }
       }
}


//configure la base spip et les metas
function doc2img_install($action){
    //on récupere la version depuis plugin.xml
    $doc2img_infos = plugin_get_infos('doc2img');
	$doc2img_version = $doc2img_infos['version'];
	
    //version en mémoire dans spip
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

function doc2img_installer($version,$version_finale) {

    //méthode  $version correspond à la version installée
    //on met à jour à partir de cette version
    //c'est pourquoi pas break;
    //recherche du case correspondant à la version installée
    //mise à jour jusqu'à la version finale


    spip_log('installation ou mise à jour','doc2img');
    
    // on fait les mise à jour qui suive $version
    switch ($version) {
        //le plugin n'a été jamais installé
        case NULL :
            //on créé une table qui servira à faire correspondre les images avec  les documents
            spip_log('création de la table','doc2img');
            spip_query("CREATE TABLE spip_doc2img (
                id_doc2img BIGINT (21) AUTO_INCREMENT, 
                id_document BIGINT (21) NOT NULL, 
                id_type BIGINT(21) NOT NULL,
                fichier varchar(255) NOT NULL, 
                PRIMARY KEY (id_doc2img)
                );");
            spip_log('table spip_doc2img créée','doc2img');
            //on defini un repertoire de stockage
            $dir_doc2img = getcwd().'/../IMG/doc2img';
            mkdir($dir_doc2img);
            spip_log('création repertoire '.$dir_doc2img,'doc2img');
    }

    //on met … jour la version du plugin
    ecrire_meta('doc2img_version', $version_finale);
    ecrire_metas();
}

function doc2img_uninstaller() {

    //la desinstallation se lance depuis la racine du site et non ecrire/

    spip_log('suppression compléte','doc2img');

    //on néttoie ce qui a été installée
    //supprime la table doc2img
	spip_query("DROP TABLE spip_doc2img");
	
	spip_log('suppression table','doc2img');
	//on supprime le repertoire créé et son contenu
	$dir_doc2img = getcwd().'/IMG/doc2img';
	spip_log('suppression des doc2img :'.$dir_doc2img,'doc2img');
    rm($dir_doc2img);
 
	//supprime les log
	rm(getcwd().'/tmp/doc2img.log');

    //on efface la meta indiquant la version installée
    effacer_meta('doc2img_version');
    ecrire_metas();
}
?>
