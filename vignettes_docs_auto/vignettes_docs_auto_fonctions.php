<?php
// Adapté de 
// https://www.guillaume-leduc.fr/php-convertir-un-pdf-en-jpg-avec-image-magic.html
// & http://snipplr.com/view/10361/
// dont option pour ne recuperer que la 1erer page




function vignette_pdf2jpg($id_document){
$fichier_pdf_adresse = sql_getfetsel('fichier', 'spip_documents', 'id_document=' . intval($id_document));

//$si_pdf = sql_select('extension', "spip_documents", "id_document=".$flux['args']['id_document']." and extension='pdf'");
//if ($si_pdf and sql_count($si_pdf)>0){



//$pdf_file="./pdf/".$fichier_name;
$fichier_name=str_replace("pdf/","",$fichier_pdf_adresse);
$pdf_file=_DIR_RACINE."IMG/pdf/".$fichier_name;
//if (file_exists ($pdf_file)){print ("fichier source ok $fichier_name<br>$pdf_file <br><br>");}else{print ("fichier source PAS $fichier_name<br>$pdf_file <br><br>");}
$save_to=_DIR_RACINE."IMG/tmp/".$fichier_name;




//print ("controle 1 ".$save_to."<br><br>");

$save_to =str_replace(".pdf", ".png", $save_to);
//print ("controle 2 ".$save_to."<br><br>");

/// A FAIRE = si le dossier /IMG/tmp n'existe pas le créer

//if (file_exists ($save_to)){print ("fichier destination ok $save_to <br><br>");}else{print ("fichier destination PAS $save_to <br><br>");}
//echo"<a href='$pdf_file'>$pdf_file</a>  => $save_to<br>";
$vignette_temp=exec('convert "'.$pdf_file.'[0]" -colorspace sRGB -resize 800 "'.$save_to.'"', $output, $return_var);
$chemin=$save_to;
// => doc avancée aussi sur http://www.imagemagick.org/script/command-line-options.php#page 
if($return_var == 0) {return $chemin;//$vignette_temp;
}
};





/*
// V fonctionnelle via fichier
function pdf2jpgfichier($fichier_name){
$pdf_file="./pdf/".$fichier_name;
$save_to="./jpg/".$fichier_name;
$save_to =str_replace(".pdf", ".jpg", $save_to);
echo"<a href='$pdf_file'>$pdf_file</a>  => $save_to<br>";
exec('convert "'.$pdf_file.'[0]" -colorspace RGB -resize 800 "'.$save_to.'"', $output, $return_var);
// => doc avancée aussi sur http://www.imagemagick.org/script/command-line-options.php#page 
if($return_var == 0) {print "Conversion OK";}
};
*/