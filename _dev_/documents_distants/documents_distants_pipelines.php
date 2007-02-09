<?

function documents_distants_ajouter_boutons($boutons_admin){
	$boutons_admin['naviguer']->sousmenu['documents_distants']= new Bouton("plugin-24.gif", _T('documentsdistants:importer') );
   
  return $boutons_admin;
 }
?>