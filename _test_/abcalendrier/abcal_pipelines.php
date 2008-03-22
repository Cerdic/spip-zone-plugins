<?php
/*pipeline per aggiungere il foglio di stile e il js del plugin alla head delle pagine*/
function abcalendrier_insert_head($flux){
   $css_link="<link rel=\"stylesheet\" href=\""._DIR_PLUGIN_ABCALENDRIER."abcalendrier.css\" type=\"text/css\" media=\"projection, screen\" />\n";
   $js_link="<script type=\"text/javascript\"  src=\""._DIR_PLUGIN_ABCALENDRIER."menuhover.js\"></script>\n";
   
      $flux .=  "\n<!-- Debut header du ABCalendrier -->\n$css_link\n$js_link\n<!-- Fin header du ABCalendrier -->\n\n";
	return $flux;
}

function abcalendrier_body_prive($flux) {
   $exec =  $flux['args']['exec'];
   print_r($flux);
   if ($exec=='breves_voir'){
      $id_breve = $flux['args']['id_breve'];
      $row = spip_fetch_array(spip_query("SELECT * FROM spip_breves WHERE id_breve=$id_breve"));
      $statut = $row['statut'];
      }
}

/*pipeline_aggiunta form evento alla amministraz privata*/
function abcalendrier_affiche_milieu($flux) {
   $exec =  $flux['args']['exec'];
   //   print_r($flux);die;
   if ($exec=='breves_voir'){
      $id_breve = $flux['args']['id_breve'];
      $row = spip_fetch_array(spip_query("SELECT * FROM spip_breves WHERE id_breve=$id_breve"));
      $statut = $row['statut'];

      // aggiungo la form di gestione per la data dell'evento
      $flux['data'] .=  "<div id='abcalendrier'>";
      $flux['data'] .=  "<a name='abcalendrier'></a>";
      $flux['data'] .=  debut_cadre_enfonce($icona='', true, "", $bouton="");

      // carico la funzione inc_evdater_dist nella cartella inc nel file evdater.php
      // questa chiamata è in più... potrebbe bastare chiamare direttamente la funzione...
      $evdater = charger_fonction('evdater', 'inc');
      // la funzione inc_evdater_dist mi restituisce il codice html di output relativo a quanto richiesto
      $flux['data'] .=  $evdater($id_breve, $flag_editable='oui', $statut, 'breve', 'breves_voir', $row['evento']);
      $flux['data'] .= fin_cadre_enfonce(true);
      $flux['data'] .=  "</div>";

   }
   return $flux;
}


/**/


/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/texte');
include_spip('inc/actions');
include_spip('inc/date');

?>
