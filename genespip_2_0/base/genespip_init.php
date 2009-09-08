<?
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/

function genespip_install($action){
 switch ($action)
 {  // La base est deja cree ?
    case 'test':
       // Verifier que le champ id_mon_plugin est present...
       include_spip('base/abstract_sql');
       $desc = spip_abstract_showtable("spip_genespip_individu", '', true);
       return (isset($desc['field']['id_individu']));
       break;
     // Installer la base
     case 'install':
       include_spip('base/create');  // definir la fonction
       include_spip('base/genespip'); // definir sa structure
       creer_base();
       break;
     // Supprimer la base
     case 'uninstall':
       spip_query("DROP TABLE spip_genespip_individu");
       spip_query("DROP TABLE spip_genespip_mariage");
       spip_query("DROP TABLE spip_genespip_documents");
       spip_query("DROP TABLE spip_genespip_liste");
       break;
  }
}
?>

