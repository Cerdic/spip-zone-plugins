<?php
include_spip('inc/exclure_utils');

function exclure_sect_pre_boucle(&$boucle){
    
    if ($boucle->modificateur['tout_voir'] or ($boucle->modificateur['tout'] and lire_config('secteur/tout') == 'oui') or test_espace_prive()==1 or $boucle->nom=='calculer_langues_utilisees'){
        return $boucle;
    }
    $type = $boucle->id_table;

    
    if ($type == 'articles' or $type == 'rubriques' or $type == 'syndic'){
    
        $crit = $boucle->criteres;
        $exclut = exclure_sect_choisir($crit,$type);    
        
        if ($exclut !='z'){
        	$boucle->where[] = "sql_in('id_secteur','$exclut','NOT')";   
        }
    }
    
    if ($type == 'breves'){
    
        $crit = $boucle->criteres;
        $exclut = exclure_sect_choisir($crit,$type);       
        if ($exclut !='z'){
        	$boucle->where[] = "sql_in('id_rubrique','$exclut','NOT')";       
        }
    }
    
    if ($type == 'forum'){
        $crit = $boucle->criteres;
        $exclut = exclure_sect_choisir($crit,$type);        
		
		$select_article = "sql_get_select('id_article', 'spip_articles', sql_in('id_secteur','$exclut'))";
		if ($exclut !='z'){
			$where = array(sql_quote('NOT'),
					array(sql_quote('AND'),
						"sql_in('forum.objet',sql_quote('article'))",
						"sql_in('id_objet',$select_article)"
					));
		}	
    }
    return $boucle;
}
?>