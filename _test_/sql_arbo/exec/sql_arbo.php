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

    if ($table) {
        echo "<h1>Conversion autojointure -> intervallaire</h1>";
        echo sql_arbre_convertir($table, 'FAM_ID', 'FAM_PERE');
        
        echo "<h1>Obtenir toutes les feuilles</h1>";
        
        $ressource = sql_arbre_get_feuilles($table);
        
        while($r = sql_fetch($ressource)) {
            echo $r['FAM_ID']."<br/>";
        }
        
        echo "<h1> Insertion d'une feuille Roller</h1>";
        
        print_r(sql_arbre_set_feuille($table,array('id'=>'1','champ' => 'FAM_ID'),'droit',array('FAM_LIB'=>'Roller')));
        print_r(sql_arbre_set_feuille($table,array('id'=>'3','champ' => 'FAM_ID'),'gauche',array('FAM_LIB'=>'Parapente')));        
        
    } else {
        echo "Veuillez donner en argument de la page la table à tester &table=matable";    
    }
    
	fin_page();
}

?>
