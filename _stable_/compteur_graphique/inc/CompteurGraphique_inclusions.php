<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_COMPTEURGRAPHIQUE',(_DIR_PLUGINS.end($p)));

function CGpuis ($n){
    $p=1;
    for ($i=1;$i<=$n;$i++){
        $p=$p*10;
    }
    return $p;
}

// Ici, on génère l'image du compteur à partir du nombre de chiffres choisis, de son décompte,
// de l'habillage et du numéro de l'image générée dans le répertoire temporaire. On crée
// l'image dans IMG/CompteurGraphique/ et on retourne le nom de l'image créée.

function compteur_graphique_calcul_image($CG_limite,$CG_num,$CG_habillage,$CG_indent_image) {
//Test 1 : doit-on générer à partir de vignettes GIF ou PNG ?
$test1 = spip_query("SELECT id_compteur FROM ext_compteurgraphique WHERE statut = 11");
$tab1 = spip_fetch_array($test1);
$res1 = $tab1['id_compteur'];
if (isset($res1)) {$t1='.png'; $test1=true;} else {$t1='.gif'; $test1=false;}

//Test 2 : doit-on générer du GIF ou du PNG ?
$test2 = spip_query("SELECT id_compteur FROM ext_compteurgraphique WHERE statut = 12");
$tab2 = spip_fetch_array($test2);
$res2 = $tab2['id_compteur'];
if (isset($res2)) {$t2='.png'; $test2=true;} else {$t2='.gif'; $test2=false;}

//Test 3 : doit-on gérer la transparence ou non ?
$test3 = spip_query("SELECT id_compteur FROM ext_compteurgraphique WHERE statut = 13");
$tab3 = spip_fetch_array($test3);
$res3 = $tab3['id_compteur'];

    $CG_chemin="lib/compteurgraphique_pack/";
    if ($CG_limite==0) {
        $i=1;
        while (!isset($CG_stop)) {
            if (($CG_num/(CGpuis($i)))<1) {
            $CG_stop=1;
            }
            $i++;
        }
        $CG_limite=$i-1;
    }
    for ($j=1;$j<=$CG_limite;$j++) {
        $CG_tab[$CG_limite-$j+1]=$CG_num%10;
        $CG_num=$CG_num/10;
    }
    $CG_taille_image=getimagesize($CG_chemin.$CG_habillage."/0".$t1);
    $CG_image_totale=imagecreate($CG_taille_image[0]*$CG_limite,$CG_taille_image[1]);
    $CG_couleurblanche=imagecolorallocate($CG_image_totale,255,255,255);
    imagefill($CG_image_totale,0,0,$CG_couleurblanche);
    for ($i=1;$i<=$CG_limite;$i++) {
        $CG_chemin_prov=$CG_chemin.$CG_habillage."/".$CG_tab[$i].$t1;
	if ($test1) {$CG_images=imagecreatefrompng($CG_chemin_prov);} else {$CG_images=imagecreatefromgif($CG_chemin_prov);}
        $CG_decallage=$CG_taille_image[0]*($i-1);
        imagecopyresized($CG_image_totale,$CG_images,$CG_decallage,0,0,0,$CG_taille_image[0],$CG_taille_image[1],$CG_taille_image[0],$CG_taille_image[1]);
        imagedestroy($CG_images);
    }
    if (!isset($res3)) {imagecolortransparent($CG_image_totale,$CG_couleurblanche);}
    $CG_chemin_image = "IMG/CompteurGraphique/CompteurGraphique".$CG_indent_image.$t2;
    if ($test2) {header('Content-Type: image/png'); imagepng($CG_image_totale,$CG_chemin_image);} else {header('Content-Type: image/gif'); imagegif($CG_image_totale,$CG_chemin_image);}
    imagedestroy($CG_image_totale);
    $CG_texte_retour="CompteurGraphique".$CG_indent_image.$t2;
return $CG_texte_retour;
}
?>