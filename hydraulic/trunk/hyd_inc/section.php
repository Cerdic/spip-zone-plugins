<?php 
function caract_communes() {

    $caract_com = array(

                'FT'          => array(
                                       'def_section_trap',
                                       array(
                                             'rLarg'  =>array('largeur_fond',2.5),
                                             'rFruit' =>array('fruit', 0.56, false)
                                            )
                ),

                'FR'          => array(
                                       'def_section_rect',
                                       array(
                                             'rLarg'  =>array('largeur_fond',2.5),
                                            )
                ),

                'FC'          => array(
                                       'def_section_circ',
                                       array(
                                             'rDiam'  =>array('diametre',2)
                                            )
                )
	);

                /*'FP'          => array(
                                       'def_section_puis',
                                       array(
                                             'puiss1' =>array('champs_puissance1',10),
                                             'puiss2' =>array('champs_puissance2', 0.7)
                                            )
                ),*/
                
	return $caract_com;
}

?>
