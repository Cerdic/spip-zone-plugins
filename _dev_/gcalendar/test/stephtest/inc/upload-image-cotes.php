<?php
//gestion de l'upload d'un logo pour les groupes de mot
// les logos sont sauvés sous la forme bandeau.ext, et bandeau-id_rubrique.ext pour les bandeaux associés à une rubrique particulière 
function traiter_upload_image_cotes($nom,$rep,$id_groupe=0){

  if (isset($_FILES[$nom])) {
    if ($_FILES[$nom]["error"] == 0) {
      $ext = strtolower(end(explode(".", $_FILES[$nom]["name"])));
      
      $ext_ok = Array("jpg", "gif", "png");
      if (!in_array($ext, $ext_ok)) {
        return "cotes:erreur_extension";
      }
		
      $chem = creer_repertoire_documents($rep);
      
      if (!$id_groupe) {
        $nom_logo = $nom;
      } else {
        $nom_logo = "$nom-$id_groupe";
      }
      
      $dest = $chem.$nom_logo.".".$ext;
      
      $ok = false;
      if ($chem) {
        $ok = @move_uploaded_file($_FILES[$nom]['tmp_name'], $dest);
      }
      
      if ($ok){
        //nettoyage du dossier logo (les logo portant le même nom mais une extension différente du bandeau uploadé sont supprimés)
        $handle = @opendir($chem); 
        while($fichier = @readdir($handle)) {
          if (ereg("^$illu-etudiant\.(jpg|png|gif)$", $fichier) && $fichier != $nom_logo.".".$ext){
            @unlink($chem.$fichier);
          }
        }
        return "cotes:upload_reussi";
      } else {
        return "cotes:upload_rate";
      }
    } else if ($_FILES[$nom]["error"] == 1 || $_FILES[$nom]["error"] == 2) {
      return "cotes:erreur_trop_gros";
    } else if ($_FILES[$nom]["error"] == 3) {
      return "cotes:erreur_transmission";
    }
  }
}

// supprime l'illu de l'etudiant
function traiter_suppression_illu_cotes($id_groupe=0){

    if ($_POST['action'] == "supprimer_illu") {
      $chem = creer_repertoire_documents("illu-etudiant");
      $handle = @opendir($chem);       
      while($fichier = @readdir($handle)) {
        if (!$id_groupe) {
          if (ereg("^illu_etudiant\.(jpg|png|gif)$", $fichier)){
            @unlink($chem.$fichier);
          }
        } else {
          if (ereg("^illu_etudiant-$id_groupe\.(jpg|png|gif)$", $fichier)){
            @unlink($chem.$fichier);
          }
        }
      }
    }
  
}

?>