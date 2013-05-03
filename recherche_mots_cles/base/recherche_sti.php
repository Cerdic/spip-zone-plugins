<?php
######################################################################
# RECHERCHE 			                                     #
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. #
# Pour plus de details voir le fichier COPYING.txt                   #
######################################################################

//ce fichier définit les tables à créer par spip lors de l'installation du plugin
//on se sert de ecrire/base/create.php de spip pour créer les tables dans la base de donnée

function recherche_sti_declarer_tables_objets_sql($tables)
{
    $tables['spip_sti_groupes_mots_cles'] = array(
           
       'principale' => "oui",
       'field'=> array(
                        "id_groupes_mots_cles" => "bigint(21) NOT NULL",
						"titre" => "text",
						"mode_presentation" => "tinyint DEFAULT 0 NOT NULL",
						"nbre_colonnes" => "tinyint DEFAULT 2 NOT NULL"
                       ),
   	                 'key' => array(
                            "PRIMARY KEY"   => "id_groupes_mots_cles",
   			                 ),
  		       );
    return $tables;
}

?>
