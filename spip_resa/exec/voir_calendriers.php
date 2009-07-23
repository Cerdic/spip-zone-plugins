<?php

function exec_voir_calendriers()
{
    $commencer_page = charger_fonction('commencer_page', 'inc') ;
    echo $commencer_page(_T('resa:liste_calendriers')) ;
   
    echo debut_gauche('', true) ;
	echo recuperer_fond('prive/menu') ;

    echo debut_droite('', true) ;
	
	echo '
	<table id="liste-calendriers">
		<caption>' . _T('resa:liste_calendriers') . '</caption>
		<thead>
			<tr>
				<th>' . _T('resa:label_article') . '</th>
				<th>' . _T('resa:label_voir_reservations') . '</th>
				<th>' . _T('resa:label_supprimer') . '</th>
			</tr>
		</thead>
		<tbody>' ;
		
	$resCal = sql_select(array('id_calendrier, id_article'), 'spip_resa_calendrier') ;
	if( sql_count($resCal) )
	{
		while( $calendrier = sql_fetch($resCal) ) {
			$resArt = sql_select('titre', 'spip_articles', 'id_article=' . sql_quote((int) $calendrier['id_article'])) ;
			$article = sql_fetch($resArt) ;
			echo '
			<tr>
				<td>' . $article['titre'] . '</td>
				<td><a href="?exec=voir_calendrier&amp;id_calendrier=' . (int) $calendrier['id_calendrier'] . '"><img src="../plugins/spip_resa/prive/images/voir.png" alt="' . _T('resa:voir_calendrier') . '" /></a></td>
				<td><a href="?exec=supprimer_calendrier&amp;id_calendrier=' . (int) $calendrier['id_calendrier'] . '&amp;id_article=' . (int) $calendrier['id_article'] . '"><img src="../plugins/spip_resa/prive/images/supprimer.png" alt="' . _T('resa:supprimer_calendrier') . '" /></a></td>
			</tr>' ;
		}
	} else {
		echo '<tr><td colspan="3">' . _T('resa:aucun_calendrier') . '</td></tr>' ;
	}
	echo '
	</tbody>
	</table>' ;

	echo fin_gauche() ;
    echo fin_page() ;
}
