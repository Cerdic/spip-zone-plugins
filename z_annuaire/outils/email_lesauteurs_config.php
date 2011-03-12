<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
# --------------------------------------------------------------
# Fichier de configuration pris en compte par config_outils.php
# et specialement dedie a la configuration de ma lame perso
# --------------------------------------------------------------
// Ajout de l'outil 'email_lesauteurs'
function outils_email_lesauteurs_config_dist() { add_outil(array(
        'id' => 'email_lesauteurs',
        'nom' => '<:annuaire:email_lesauteurs:nom:>',
        'description' => '<:annuaire:email_lesauteurs:description:>',
        'categorie' => 'typo-corr',
        'traitement:LESAUTEURS, traitement:DESCRIPTIF' => 'email_lesauteurs',
        'code:options' =>'
// Fonction de traitement
function email_lesauteurs($texte) {
        if (strpos($texte, "@")===false) return $texte;
		$autorises=\'\\!\\#\\$\\%\\&\\\'\\*\\+\\-\\/\\=\\?\\^\\_\\`\\.\\{\\|\\}\\~a-zA-Z0-9\';
        return preg_replace(",\b([{$autorises}]*@)[a-zA-Z][a-zA-Z0-9-.]*\.[a-zA-Z]+(\?[{$autorises}]*)?,", \'$1\', $texte);
}
        ',
));}
?>
