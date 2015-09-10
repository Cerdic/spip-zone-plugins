<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/yaml');
include_spip('inc/headers');

function formulaires_fchmlatex_charger_dist($self)
{
    if (isset($_GET['num']))
    {
        //si il y a un parametre num dans l'url alors nous ne plus au début de l'execution du script
        $num = _request('num');
        $sDirExport = getDirExport();
        $data = yaml_decode_file($sDirExport.'liste.yaml');
        $a = $data[$num];
        $max = count($data);

        if ($max != 0) $avancement = round($num*100/count($data),0);

        $format = _request('format');

        if($num<$max)
        {
            $code = recuperer_fond('inc/chmlatex_progression', array(
                'boite_titre' => _T("chmlatex:generation_$format"),
                'titre_objet' => _T('public:'.$a['type']).' '.$a['id'].' : '.$a['titre'],
                'progression' => $avancement)
            );

            echo $code;

            $secteur = _request('secteur');
            $sFunctionAction = $format.'_export';
            $sFunctionAction($a,$num,$secteur,_request('langue'));
            $num = $num + 1;
            $url = parametre_url($self, 'num', $num);
            $url = str_replace('&amp;','&',$url);
            echo '<script>window.location = "'.$url.'"</script>';
        }
        elseif($num==$max)
        {
            $sZipFileName = getZipFileName();
            zipDir($sDirExport ,$sZipFileName);
            return array(
                'message_ok'=>"<a href='$sZipFileName'>"._T("chmlatex:telecharger_$format")."</a>",
                'format'    => $format,
                'langue'    => _request('langue'));
        }
    }
    return array();
}


function formulaires_fchmlatex_verifier_dist()
{
    $erreurs = array();
    // verifier que les champs obligatoires sont bien la :
    foreach(array('secteur_region') as $obligatoire)
        if (!_request($obligatoire)) $erreurs[$obligatoire] = 'Ce champ est obligatoire';

    if (count($erreurs))
            $erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
    return $erreurs;
}


/**
 * Traitement du formulaire CVT :
 * Préparation de l'export et rechargement javascript de la page pour traiter le premier objet
 * @param $self adresse de la page du formulaire
 * @author Hicham Gartit, David Dorchies
 */
function formulaires_fchmlatex_traiter_dist($self)
{
    $format = _request('format_export');
    $langue = _request('langue_export');
    $secteur = _request('secteur_region');

    // Suppression du dossier d'export
    $sDirExport = getDirExport();
    if (file_exists($sDirExport)) delTree($sDirExport);
    // Suppression du ZIP
    $sZipFileName = getZipFileName();
    if (file_exists($sZipFileName)) unlink($sZipFileName);
    // Suppression du YAML
    if (file_exists($sDirExport.'liste.yaml')) unlink($sDirExport.'liste.yaml');
    // Création des dossiers d'export
    if (!file_exists($sDirExport)) mkdir($sDirExport, 0777);
    if (!file_exists($sDirExport.'images')) mkdir($sDirExport.'images', 0777);
    if ($format=='tex' && !file_exists($sDirExport.'inclus'))
        mkdir($sDirExport.'inclus', 0777);
    // Création du fichier YAML contenant la liste des rubriques et articles du secteur
    cree_yaml($langue,$secteur);
    // Construction de l'URL de rechargement javasscript du formulaire
    $url = parametre_url($self, 'num', '0');
    $url = parametre_url($url, 'format', $format);
    $url = parametre_url($url, 'secteur', $secteur);
    $url = parametre_url($url, 'langue', $langue);
    $url = str_replace('&amp;','&',$url);
    echo'<script>window.location = "'.$url.'"</script>';
    return array();
}


/**
 * Fonction pour transformer les balises img en \includegraphics
 */
function tex_img2includegraphics($matches)
{
    $chemin = $matches[1];

    if(substr($chemin, 0, strlen('../')) === '../' || substr($chemin, 0, strlen('http')) === 'http')
    {
        $chemin = str_replace(' ','%20', $chemin);
        $source = $chemin;
    }
    else
    {
        $source = '../'.$chemin;
    }
    return '\includegraphics{'.$source.'}';
}


/**
 * Traitement des balises latex includegraphics - copie des images
 */
function tex_copie_images($matches)
{
    $d = getDirExport()."images/";
    $nomimg = pathinfo($matches[1], PATHINFO_FILENAME);
    $extimg = pathinfo($matches[1], PATHINFO_EXTENSION);
    $ext = substr($extimg , 0, 3);
    $chemin = str_replace($extimg,$ext,$matches[1]);
    $nm = $nomimg.'.'.$ext;
    $nom = str_replace("\\", "", $nm);

    if(strstr($chemin, '}'))
    {
        $chemin = substr($chemin, 0, strpos($chemin, "}"));
    }

    $chemin = str_replace('%20',' ', $chemin);
    if(strstr($nom, '}'))
    {
        $nom = substr($nom, 0, strpos($nom, "}"));
    }
    $dest = $d.$nom;
    $chemin = str_replace("\\_",'_',$chemin);

    copy($chemin,$dest);
    if ($ext == 'gif')
    {
        $ext = 'png';
        $n = $nomimg.'.png';
        $n1 = str_replace("\\", "", $n);
        if (imagepng(imagecreatefromstring(file_get_contents($d.$nom)), $d.$n1))
            {
                unlink($d.$nom);
            }
    }
    $nm = $nomimg.'.'.$ext;
    $nom = str_replace("\\", "", $nm);
    if(strstr($nom, '}'))
    {
        $nom = substr($nom, 0, strpos($nom, "}"));
    }
    $copie = 'images/'.$nom;
    $include = str_replace('includegraphics{','includegraphics[max width=\textwidth]{',$matches[0]);
    return str_replace($matches[1],$nom,$include);
}


/**
 * Post-traitement d'une page pour l'export Latex
 * @param $code code de la page à traiter
 * @return Code traité
 */
function tex_post_traitement($code) {
    //Traitement des images
    $code = preg_replace_callback('#<img.*src="(.*)".*>#iU','tex_img2includegraphics',$code);
    $code = preg_replace_callback("#<img.*src='(.*)'.*>#iU",'tex_img2includegraphics',$code);
    $code = preg_replace_callback("#\\includegraphics{(.*)}#i",'tex_copie_images',$code);
    $code = str_replace("\\\\includegraphics{",'\\includegraphics{',$code);
    return $code;
}


/**
 * Renvoie le dossier d'exportation
 * @author David Dorchies
 * @date 09/06/2015
 */
function getDirExport() {
    $s = _DIR_RACINE . _NOM_TEMPORAIRES_INACCESSIBLES;
    $s .= _request('format').'_'._request('langue').'/';
    return $s;
}


/**
 * Renvoie le nom du fichier ZIP
 * @author David Dorchies
 * @date 09/06/2015
 */
function getZipFileName() {
    $s = _DIR_RACINE . _NOM_TEMPORAIRES_ACCESSIBLES;
    $s .= _request('format').'_'._request('langue').'.zip';
    return $s;
}


/**
 * Traitement des balises img pour export HTML/CHM.
 * Copie du fichier image dans le dossier d'export et modification du ilen
 * @param $matches [0]: balise img, [1]: lien vers l'image
 * @return lien modifié pour pointer vers le dossier d'export
 * @author Hicham Gartit
 */
function imagehtml($matches)
{
    $langue = $_GET['langue'];
    $chemin = $matches[1];
    $nomimg = pathinfo($chemin, PATHINFO_FILENAME);
    $extimg = pathinfo($chemin, PATHINFO_EXTENSION);
    $nom = $nomimg.'.'.$extimg;

    if(substr($chemin, 0, strlen('../')) === '../' || substr($chemin, 0, strlen('http')) === 'http')
    {
        $chemin = str_replace(' ','%20', $chemin);
        $source = $chemin;
    }
    else
    {
        $source = '../'.$chemin;
    }
    $dest = getDirExport().'images/'.$nom;
    copy($source,$dest);
    $copie = 'images/'.$nom;
    $ret = $ret = str_replace($matches[1],$copie,$matches[0]);
    return $ret;
}


/**
 * Traitement des liens entre articles et rubriques pour l'export HTML/CHM
 * @param
 * @author Hicham Gartit
 */
function html_lien($matches)
{
    $chemin = $matches[1];
    $nomf = pathinfo($chemin, PATHINFO_FILENAME);
    $ext = pathinfo($chemin, PATHINFO_EXTENSION);
    $nom = $nomf.'.'.$ext;
    $id = 0;

    spip_log('S: '.$chemin,'html_lien');

    if(substr($chemin, 0, strlen('../')) === '../' || substr($chemin, 0, strlen('http')) === 'http')
    {
        if(substr($chemin, 0, strlen($GLOBALS['meta']['adresse_site'].'/ecrire/?exec=')) === $GLOBALS['meta']['adresse_site'].'/ecrire/?exec=')
        {
            if(strstr($chemin, 'exec=article'))
            {
                $id = substr(strstr($chemin, "id_article="),11);
                $type = 'article';
            }
            else if(strstr($chemin, 'exec=rubrique'))
            {
                $id = substr(strstr($chemin, "id_rubrique="),12);
                $type = 'rubrique';
            }
            $aId = explode('#',$id); // Traitement des ancres
            $id = $aId[0];
            $nom = $type.$id.'.html';
            if(isset($aId[1])) $nom .= '#'.$aId[1];
            spip_log('R: '.$nom,'html_lien');
            spip_log('M: '.$matches[0],'html_lien');

            return str_replace($matches[1],$nom,$matches[0]);
        }
    }
    spip_log('N: '.$matches[0],'html_lien');
    return $matches[0];
}


/**
 * Ecriture de la liste des articles et rubriques dans liste.yaml
 */
function cree_yaml($langue,$secteur)
{
                $sDirExport = getDirExport();
                $yaml = recuperer_fond("yaml/index", array('id_rubrique' => $secteur,'lang' => $langue,));
                file_put_contents($sDirExport.'liste.yaml',$yaml);
}


/**
 * Export HTML/CHM
 */
function html_export($a,$num,$secteur,$langue)
{
     $sDirExport = getDirExport();
     if ($num == 0)
    {
        // Rubrique parente
        $t = recuperer_fond("chm/index", array('id_rubrique' => $secteur,'lang' => $langue,));
        $rubriqueParent = $sDirExport."rubrique$secteur.html";
        file_put_contents($rubriqueParent ,$t);
        copy($rubriqueParent,$sDirExport.'index.html');

        // Fichiers HHC, HHK et HHP
        $t = recuperer_fond("hhc/index", array('id_rubrique' => $secteur,'lang' => $langue,));
        file_put_contents($sDirExport.'chmlatex.hhc',$t);
        $t = recuperer_fond("chm/hhk", array('id_rubrique' => $secteur,'lang' => $langue,));
        file_put_contents($sDirExport.'chmlatex.hhk',$t);
        $t = recuperer_fond("chm/hhp", array('id_rubrique' => $secteur,'lang' => $langue,));
        file_put_contents($sDirExport.'chmlatex.hhp',$t);

        // fichier chm/css.html : traitement des images
        $t = recuperer_fond("chm/css");
        $t = preg_replace_callback("#url\('(.*)'\);#iU",'imagehtml',$t);
        file_put_contents($sDirExport.'chm.css',$t);
    }

    //Traitement des pages HTML contenues dans liste.yaml
    $id = $a['id'];
    if($a['type']=='article')
    {
        // traiter article article#ID_ARTICLE.html
        $code = recuperer_fond("chm/article_content", array('id_article' => $a['id'],'lang' => $langue,));
        $n = 'article'.$id;
    }
    else
    {
        // traiter rubrique rubrique#ID_RUBRIQUE.html
        $code = recuperer_fond("chm/index", array('id_rubrique' => $a['id'],'lang' => $langue,));
        $n = 'rubrique'.$id;
    }

    //Traitement des images
    $code = preg_replace_callback('#<img.*src="(.*)".*>#iU','imagehtml',$code);
    $code = preg_replace_callback("#<img.*src='(.*)'.*>#iU",'imagehtml',$code);

    //Traitement des liens
    $code = preg_replace_callback("#href='(.*)'#iU",'html_lien',$code);
    $code = preg_replace_callback('#href="(.*)"#iU','html_lien',$code);

    // Enregistrement du fichier HTML
    file_put_contents("$sDirExport$n.html",$code);
}


/**
 * Export LaTeX
 */
function tex_export($a,$num,$secteur,$langue)
{
    $sDirExport = getDirExport();
    if ($num == 0)
    {
        // Document maître
        $code = recuperer_fond("tex/index", array('id_rubrique' => $secteur,'lang' => $langue,));
        file_put_contents($sDirExport.'chmlatex_'.$langue.'.tex',$code);

        // 1ère de couverture
        $code = recuperer_fond("tex/premiere", array('id_rubrique' => $secteur,'lang' => $langue,));
        $code = tex_post_traitement($code);
        file_put_contents($sDirExport.'inclus/premiere.tex',$code);

        // 4ème de couverture
        $code = recuperer_fond("tex/derniere", array('id_rubrique' => $secteur,'lang' => $langue,));
        $code = tex_post_traitement($code);
        file_put_contents($sDirExport.'inclus/derniere.tex',$code);
    }

    // Traitement des articles et rubriques
    $id = $a['id'];
    if($a['type']=='article')
    {
        $n = 'article'.$id;
        //traiter article article#ID_ARTICLE.html
        $code = recuperer_fond("tex/article_content", array('id_article' => $a['id'],'lang' => $langue,));
    }
    else
    {
        $n = 'rubrique'.$id;
        //traiter rubrique rubrique#ID_RUBRIQUE.html
        $code = recuperer_fond("tex/rubrique_content", array('id_rubrique' => $a['id'],'lang' => $langue,));
    }
    $code = tex_post_traitement($code);
    file_put_contents($sDirExport."inclus/$n.tex",$code);
}


/**
 * Suppression d'un dossier et de son contenu
 * @param $dir Dossier à supprimer
 * @return retourne TRUE en cas de succès ou FALSE si une erreur survient
 */
function delTree($dir)
{
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file)
        {
          (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
        }
    return rmdir($dir);
}


/**
 * Ajout récursif d'un dossier dans un fichier ZIP
 * @param $folder
 * @param $zipFile
 * @param $exclusiveLength
 */
function folderToZip($folder, &$zipFile, $exclusiveLength)
{
    $handle = opendir($folder);

    while (false !== $f = readdir($handle)) {
      if ($f != '.' && $f != '..') {
        $filePath = "$folder/$f";
        // Remove prefix from file path before add to zip.
        $localPath = substr($filePath, $exclusiveLength);
        if (is_file($filePath)) {
          $zipFile->addFile($filePath, $localPath);
        } elseif (is_dir($filePath)) {
          // Add sub-directory.
          $zipFile->addEmptyDir($localPath);
          folderToZip($filePath, $zipFile, $exclusiveLength);
        }
      }
    }
    closedir($handle);
}


/**
 * Création d'un fichier ZIP à partir d'un dossier
 * @param $sourcePath Dossier à compresser
 * @param $outZipPath Nom du fichier ZIP
 */
function zipDir($sourcePath, $outZipPath)
{
    $pathInfo = pathInfo($sourcePath);
    $parentPath = $pathInfo['dirname'];
    $dirName = $pathInfo['basename'];
    $z = new ZipArchive();
    $z->open($outZipPath, ZIPARCHIVE::CREATE);
    $z->addEmptyDir($dirName);
    folderToZip($sourcePath, $z, strlen("$parentPath/"));
    $z->close();
}

?>
