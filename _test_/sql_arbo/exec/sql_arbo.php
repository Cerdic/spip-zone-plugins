<?php

include_spip('base/abstract_sql');
include_spip('base/abstract_arbre');
include_spip('inc/vieilles_defs'); 


# securite
if (!defined("_ECRIRE_INC_VERSION")) return;


function exec_sql_arbo() {
	global $connect_statut, $connect_id_auteur, $connect_toutes_rubriques;
	global $exec;
	include_spip('inc/presentation');

	pipeline('exec_init',
		array('args'=>array('exec'=>'configuration'),'data'=>''));

	debut_page(_L('Test arbo'), "configuration", "configuration");
	echo "<br><br><br>";
	gros_titre(_L('Test'));

	debut_gauche();
	debut_droite();

    $table = _request('table');
    $id_noeud = _request('id_noeud');
    $champ_noeud = _request('champ_noeud');
    $champ_parent = _request('champ_parent');
    $champ_description = _request('champ_description');
    
    print_r($_GET);

    if (!is_null($table) && !is_null($id_noeud) && !is_null($champ_noeud) && !is_null($champ_parent)) {
        echo "<h1>Conversion autojointure -> intervallaire</h1>";
        
        echo sql_arbre_convertir($table, $champ_noeud, $id_noeud, $champ_parent);
        
        echo "<h1>Obtenir toutes les feuilles</h1>";
        
        $ressource = sql_arbre_get_feuilles($table);
        
        while($r = sql_fetch($ressource)) {
            echo $r[$champ_noeud]."<br/>";
        }
        
        echo "<h1> Insertion d'une feuille Roller</h1>";
        if ($champ_description ) {
            print_r(sql_arbre_set_feuille($table,array('id'=>'1','champ' => $champ_noeud),'droit',array($champ_description=>'Roller')));
            print_r(sql_arbre_set_feuille($table,array('id'=>'3','champ' => $champ_noeud),'gauche',array($champ_description=>'Parapente')));        
        }
        
    } else {
        echo "Veuillez donner en argument de la page :  <br/>
            <ul>
                <li>la table à tester &table=matable</li>
                <li>l'id du noeud de depart &id_noeud=id</li>
                <li>le nom du champ des clefs &champ_noeud=champ</li>
                <li>le nom du champ de la clef etrangére (parent) &champ_parent=champ</li>
                <li>le nom du champ de description (pour tester les insertions) &champ_description</li>                
                <li> <a href='?exec=sql_arbo&table=spip_autojointure&id_noeud=0&champ_noeud=FAM_ID&champ_parent=FAM_PERE&champ_description=FAM_LIB'> Exemple si la base autojointure de sql.developpez.com existe  </a></li>
            </ul>
        ";  
    }
    
	fin_page();
}

?>
