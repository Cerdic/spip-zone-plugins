<?php
/*
 * Spip Gis plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonz‡lez, Berio Molina
 * (c) 2007 - Distribu’do baixo licencia GNU/GPL
 *
 */

//esta funcion pode valer para subir un ficheiro?
//***************************************************
  function deplacer_fichier_upload_gis($source, $dest) {
      $ok = @copy($source, $dest);
      if (!$ok) $ok = @move_uploaded_file($source, $dest);
      if ($ok)
          @chmod($dest, 0666);
      else {
          $f = @fopen($dest,'w');
          if ($f) {
              fclose ($f);
          } else {
            redirige_par_entete(generer_url_action("test_dirs", "test_dir=". dirname($dest), true));
          }
          @unlink($dest);
      }
      return $ok;
}
//***************************************************


?>