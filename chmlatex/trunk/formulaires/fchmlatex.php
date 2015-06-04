<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/yaml');
include_spip('inc/headers');

function formulaires_fchmlatex_charger_dist($self)
{
    $tmptmp = _DIR_RACINE  . _NOM_TEMPORAIRES_INACCESSIBLES;
    $locallocal = _DIR_RACINE  . _NOM_TEMPORAIRES_ACCESSIBLES;
    if (isset($_GET['num']))//si il y a un parametre num dans l'url alors nous ne plus au début de l'execution du script
                {
                        $f = file_get_contents($tmptmp."liste.yaml");
                        $data = yaml_decode($f);
                        $num = $_GET['num'];
                        $langue = $_GET['lang'];
                        $a = $data[$num];

                        $max = count($data);
                        $secteur = $_GET['secteur'];

                        if (count($data) != 0)
                            {
                                $avancement = round($num*100/count($data),0);
                            }

                            if($_GET['format'] == 0)// format HTML pour CHM
                                {
                                    if ($num  != (count($data)))
                                        {
                                            echo _T('chmlatex:generation_html').'&nbsp;: </br>';
                                            echo "<progress id='barre-progression' max=100 value=$avancement></progress> <span id='lbl-avancement'>$avancement%</span>";
                                            action_chmlatexhtml($a,$num,$secteur,$langue);
                                            $num = $num + 1;
                                            $url = parametre_url($self, 'num', $num);
                                            $url = str_replace('&amp;','&',$url);
                                            echo '<script>window.location = "'.$url.'"</script>';

                                        }

                                    else
                                        {

                                            $avancement = 100;
                                            zipDir($tmptmp.'site',$locallocal.'site.zip');
                                            $loc = $locallocal.'site.zip';
                                            echo "<a href='$loc'>"._T('chmlatex:telecharger_html')."</a>";
                                        }


                                }

                            elseif($_GET['format'] == 1 )// format Latex pour PDF
                                {

                                    if ($num  != (count($data)) )
                                        {

                                            echo _T('chmlatex:generation_latex').'&nbsp;: </br>';
                                            echo "<progress id='barre-progression' max=100 value=$avancement></progress> <span id='lbl-avancement'>$avancement%</span>";
                                            action_chmlatextex($a,$num,$secteur,$langue);
                                            $num = $num + 1;
                                            $url = parametre_url($self, 'num', $num);
                                            $url = str_replace('&amp;','&',$url);
                                            echo '<script>window.location = "'.$url.'"</script>';

                                        }

                                    else
                                        {
                                            $avancement = 100;
                                            zipDir($tmptmp.'tex', $locallocal.'tex.zip');
                                            $loc = $locallocal.'tex.zip';
                                            echo "<a href='$loc'>"._T('chmlatex:telecharger_latex')."</a>";
                                        }

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


function formulaires_fchmlatex_traiter_dist($self)
{
            $tmptmp = _DIR_RACINE  . _NOM_TEMPORAIRES_INACCESSIBLES;
            $locallocal = _DIR_RACINE  . _NOM_TEMPORAIRES_ACCESSIBLES;
            $url = explode("?",$self);
            echo '<script>window.location = "'.$url.'"</script>';

            if (file_exists($tmptmp."site"))
            {
                delTree($tmptmp."site");
            }

            if (file_exists($locallocal."site.zip"))
            {
                unlink($locallocal."site.zip");
            }

            if (file_exists($locallocal."tex.zip"))
            {
                unlink($locallocal."tex.zip");
            }

            if (file_exists($tmptmp."tex"))
            {
                delTree($tmptmp."tex");
            }

            if (file_exists($tmptmp."liste.yaml"))
            {
                unlink($tmptmp."liste.yaml");
            }


            $format = _request('format');
            $langue = _request('lang');
            $secteur = _request('secteur_region');


            if (!isset($_GET['num']))
            {
                cree_yaml($langue,$secteur);
            }



            if(strcmp($format, 'zip') == 0)
            {
                    if (!file_exists($tmptmp."site"))
                    {
                            mkdir($tmptmp."site", 0777);
                    }

                    if (!file_exists($tmptmp."site/images"))
                    {
                            mkdir($tmptmp."site/images", 0777);
                    }
                    $val = 0;
            }

            else
            {
                    if (!file_exists($tmptmp."tex"))
                    {
                        mkdir($tmptmp."tex", 0777);
                    }

                    if (!file_exists($tmptmp."tex/images"))
                    {
                        mkdir($tmptmp."tex/images", 0777);
                    }

                    if (!file_exists($tmptmp."tex/inclus"))
                    {
                        mkdir($tmptmp."tex/inclus", 0777);
                    }
                    $val = 1;

            }
         spip_log($langue, 'laaaang');
            $url = parametre_url($self, 'num', '0');
            $url = parametre_url($url, 'format', $val);
            $url = parametre_url($url, 'secteur', $secteur);
            $url = parametre_url($url, 'lang', $langue);
            $url = str_replace('&amp;','&',$url);
            echo'<script>window.location = "'.$url.'"</script>';
            return array();

}


function imagetex($matches)
{
    $tmptmp = _DIR_RACINE  . _NOM_TEMPORAIRES_INACCESSIBLES;
    $chemin = $matches[1];
    $chem = $matches[0];
    $d = $tmptmp."tex/images/";
    $nomimg = pathinfo($chemin, PATHINFO_FILENAME);
    $extimg = pathinfo($chemin, PATHINFO_EXTENSION);
    $ext = substr($extimg , 0, 3);
    $chemin = str_replace($extimg,$ext,$chemin);
    $nm = $nomimg.'.'.$ext;
    $nom = str_replace("\\", "", $nm);


        if(substr($chem, 0, strlen('includegraphics{')) === 'includegraphics{')
        {
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
            $ar = getimagesize ($chemin);

            if ($ar[0] > 320 || $ar[1] >240 )
            {
            //redimensionner l'image...


            }
            $include = str_replace('includegraphics{','includegraphics[max width=\textwidth]{',$matches[0]);
            $ret = str_replace($matches[1],$nom,$include);



        }
        else
        {
                if(substr($chemin, 0, strlen('../')) === '../' || substr($chemin, 0, strlen('http')) === 'http')
                {
                    $chemin = str_replace(' ','%20', $chemin);
                    $source = $chemin;
                }

                else
                {
                    $source = '../'.$chemin;
                }
            $ret = "\includegraphics{".$source."}";
            //$ret = str_replace($matches[1],$source,$matches[0]);
        }



    return $ret;
}


function imagehtml($matches)
{
    $tmptmp = _DIR_RACINE  . _NOM_TEMPORAIRES_INACCESSIBLES;
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

    $d = $tmptmp."site/images/";
    $dest = $d.$nom;
    copy($source,$dest);
    $copie = 'images/'.$nom;
    $ret = $ret = str_replace($matches[1],$copie,$matches[0]);
    //return '<img src="'.$copie.'">';
    return $ret;

}


function lien($matches)
{
    $tmptmp = _DIR_RACINE  . _NOM_TEMPORAIRES_INACCESSIBLES;
    $chemin = $matches[1];
    $chem = $matches[0];
    $nomf = pathinfo($chemin, PATHINFO_FILENAME);
    $ext = pathinfo($chemin, PATHINFO_EXTENSION);
    $nom = $nomf.'.'.$ext;
    $id = 0;

        if(substr($chemin, 0, strlen('../')) === '../' || substr($chemin, 0, strlen('http')) === 'http')
        {
                $source = $chemin;
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
                    if(strstr($id, '#'))
                        {
                            $idd= substr($id, 0, strpos($id, "#"));
                        }
                    else
                        {
                            $idd = $id;
                        }
                    $nom = $type.$idd.'.html';
                    //$return ='href="'.$nom.'"';
                    $return = str_replace($matches[1],$nom,$matches[0]);
                }

                elseif(!substr($chemin, 0, strlen('../')) === '../' || substr($chemin, 0, strlen('http')) === 'http')
                {
                $source = '';
                $return = $matches[0];

                }
        }

        else
        {
            $source = '../'.$chemin;
            //$return = 'href="'.$chemin.'"';
            $return = str_replace($matches[1],$chemin,$matches[0]);
        }

    return $return;

}


function jscss($matches)
{
    $tmptmp = _DIR_RACINE  . _NOM_TEMPORAIRES_INACCESSIBLES;
    $chemin = $matches[1];
    $chem = $matches[0];
    $nomf = pathinfo($chemin, PATHINFO_FILENAME);
    $ext = pathinfo($chemin, PATHINFO_EXTENSION);
    $nom = $nomf.'.'.$ext;

    if(substr($chemin, 0, strlen('../')) === '../' || substr($chemin, 0, strlen('http')) === 'http')
        {
                $source = $chemin;
        }

    else
        {
                $source = '../'.$chemin;
        }


}
function cree_yaml($langue,$secteur)
{
                $tmptmp = _DIR_RACINE  . _NOM_TEMPORAIRES_INACCESSIBLES;
                $yaml = recuperer_fond("yaml/index", array('id_rubrique' => $secteur,'lang' => $langue,));
                file_put_contents($tmptmp."liste.yaml",$yaml);
}

function action_chmlatexhtml($a,$num,$secteur,$langue)
{
                 $tmptmp = _DIR_RACINE  . _NOM_TEMPORAIRES_INACCESSIBLES;
                 if ($num == 0)
                    {
                        $c0 = recuperer_fond("chm/index", array('id_rubrique' => $secteur,'lang' => $langue,));
                        file_put_contents($tmptmp."site/rubrique$secteur.html",$c0);

                        $rubriqueParent = $tmptmp."site/rubrique".$secteur.".html";
                        copy($rubriqueParent,$tmptmp."site/index.html");

                        $c1 = recuperer_fond("hhc/index", array('id_rubrique' => $secteur,'lang' => $langue,));
                        file_put_contents($tmptmp."site/chmlatex.hhc",$c1);

                        $c2 = recuperer_fond("chm/hhk", array('id_rubrique' => $secteur,'lang' => $langue,));
                        file_put_contents($tmptmp."site/chmlatex.hhk",$c2);

                        $c3 = recuperer_fond("chm/hhp", array('id_rubrique' => $secteur,'lang' => $langue,));
                        file_put_contents($tmptmp."site/chmlatex.hhp",$c3);

                        $c5 = recuperer_fond("chm/css");
                        preg_match_all("#url\('(.*)'\);#iU",$c5,$matches);
                        foreach ($matches[1] as $img)
                        {

                            $nomf = pathinfo($img, PATHINFO_FILENAME);
                            $ext = pathinfo($img, PATHINFO_EXTENSION);
                            $nom = $nomf.'.'.$ext;
                            $dest = $tmptmp."site/images/".$nom;
                            copy($img,$dest);
                            $c5 = str_replace($img ,"images/".$nom,$c5);
                            spip_log($img,'img');

                        }
                        file_put_contents($tmptmp."site/chm.css",$c5);
                    }

                //Traitement des pages HTML
                $id = $a['id'];

                    if(strcmp($a['type'], 'article') == 0)
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
                $code = preg_replace_callback("#href='(.*)'#iU",'lien',$code);
                $code = preg_replace_callback('#href="(.*)"#iU','lien',$code);



                file_put_contents($tmptmp."site/$n.html",$code);
}

function action_chmlatextex($a,$num,$secteur,$langue)
{
                    $tmptmp = _DIR_RACINE  . _NOM_TEMPORAIRES_INACCESSIBLES;
                    if ($num == 0)
                    {
                        $cd = recuperer_fond("tex/index", array('id_rubrique' => $secteur,'lang' => $langue,));
                        file_put_contents($tmptmp."tex/chmlatex.tex",$cd);

                        $c1 = recuperer_fond("tex/premiere", array('id_rubrique' => $secteur,'lang' => $langue,));
                        $c1 = preg_replace_callback('#<img.*src="(.*)".*>#iU','imagetex',$c1);
                        $c1 = preg_replace_callback("#<img.*src='(.*)'.*>#iU",'imagetex',$c1);
                        $c1 = preg_replace_callback("#\\includegraphics{(.*)}#i",'imagetex',$c1);
                        $c1 = str_replace("\\\\includegraphics{",'\\includegraphics{',$c1);
                        file_put_contents($tmptmp."tex/inclus/premiere.tex",$c1);

                        $c0 = recuperer_fond("tex/derniere", array('id_rubrique' => $secteur,'lang' => $langue,));
                        file_put_contents($tmptmp."tex/inclus/derniere.tex",$c0);



                    }

                    $id = $a['id'];

                    if(strcmp($a['type'], 'article') == 0)
                    {
                        $n = 'article'.$id;
                        $chemin = $tmptmp."tex/inclus/$n.tex";
                        //traiter article article#ID_ARTICLE.html
                        $code = recuperer_fond("tex/article_content", array('id_article' => $a['id'],'lang' => $langue,));

                    }

                    else
                    {
                        $n = 'rubrique'.$id;
                        $chemin = $tmptmp."tex/inclus/$n.tex";
                        //traiter rubrique rubrique#ID_RUBRIQUE.html
                        $code = recuperer_fond("tex/rubrique_content", array('id_rubrique' => $a['id'],'lang' => $langue,));

                    }

                    //Traitement des images
                    $code = preg_replace_callback('#<img.*src="(.*)".*>#iU','imagetex',$code);
                    $code = preg_replace_callback("#<img.*src='(.*)'.*>#iU",'imagetex',$code);
                    $code = preg_replace_callback("#\\includegraphics{(.*)}#i",'imagetex',$code);
                    $code = str_replace("\\\\includegraphics{",'\\includegraphics{',$code);
                    file_put_contents($tmptmp."tex/inclus/$n.tex",$code);
}

function delTree($dir) //permet de supprimer un dossier et tout ce qu'il contient en passant le chemin du dossier par paramètre
{
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file)
        {
          (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
        }
    return rmdir($dir);
}

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
