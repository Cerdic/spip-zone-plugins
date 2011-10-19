<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined("_ECRIRE_INC_VERSION")) return;

$max_filesize = ini_get("upload_max_filesize");
$duree_son_64m_max_mn = trim($max_filesize)*1.5;
$duree_video_320x240_max_mn = trim($max_filesize)/4;
$poids_max_image = (_IMG_MAX_SIZE == "0") ? $max_filesize : round(_IMG_MAX_SIZE/1024)."M";

$GLOBALS[$GLOBALS['idx_lang']] = array(

'img_q' => 'What size should my photo be?',
'img' => 'There is no "right" size to display an image in an article. It all depends on its content: if it\'s a portrait, a height of 200px should be sufficient, and if it is a beautiful landscape, you can go up to @largeur_max@ pixels width. In any case, no need to send an image of 3000 pixels width, any screen can\'t display it in its entirety! Unless the document is intended for printing.
_ {Please, note that the maximum weight not to exceed is '. Poids_max_image $.' otherwise the download will be denied}.',

'img_nombre_q' => 'How easily fill a portfolio?',
'img_nombre' => 'It is possible to send a lot of photos in one click in an article:
-* Copy selected photos to a folder on your hard drive
-* Resize them to a good size
-* Insert them into a zip file
-* Join this zip file to the article. At the end of the download, it asked you what you want to do with the file, you can for example put all the pictures in the portfolio.',
);
?>
