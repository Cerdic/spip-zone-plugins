<?php
/*
 *   Plugin HoneyPot
 *   Copyright (C) 2007 Pierre Andrews
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(
									   'type0' => 'moteur de recherche',
									   'type1' => 'suspicieux',
									   'type2' => 'faucheur',
									   'type3' => 'faucheur suspicieux',
									   'type4' => 'spammeur de commentaires',
									   'type5' => 'spammeur de commentaires suspicieux',
									   'type6' => 'faucheur et spammeur de commentaires',
									   'type7' => 'faucheur et spammeur de commentaires suspicieux',


									   'cfg_titre' => 'Restrictions d\'acc&eacute;s par liste noire.',
									   'cfg_descriptif' => 'Le plugin pot de miel utilise la base de donn&eacute;es fournie par le projet honey pot pour limiter l\'acc&eacute;s aux sites des visiteurs suspicieux.

																																																																																																									    Pour plus d\'information, voir [la documentation de http:bl->http://projecthoneypot.org/httpbl_configure.php].

																																																																																																										 Pour activer cette fonctionalit&eacute;, vous devez d\'abord [obtenir une clef d\'acc&eacute;es aupr&egrave;s du projet honey pot->http://www.projecthoneypot.org/httpbl_configure.php].',
									   'cfg_apikey' => 'Clef fournies par le P.H.Pot: ',
									   'cfg_sivisiteur' => 'Si le visiteur est consid&eacute;r&eacute; comme un :',
									   'cfg_bloquer' => 'le bloquer',
									   'cfg_rien' => 'ne rien faire',
									   'cfg_tohoneypot' => 'l\'envoyer vers le pot de miel',
									   'cfg_cacheremail' => 'cacher les adresses emails',
									   'cfg_cacherforum' => 'cacher les forums',
									   'cfg_cachertout' => 'cacher les forums et les adresses emails',
									   'cfg_minthreat' => 'quand la menace est sup&eacute;rieur &agrave; :',
									   'cfg_remplacement_email' => 'Cacher les adresse emails en les rempla&ccedil;ant par : ',
'cfg_log_stats' => 'Logs et Statistiques de filtrage',
									   'cfg_log' => 'Logguer les acc&eacute;s :',
									   'cfg_log_all' => 'de tous les types connus',
									   'cfg_log_blocked' => 'qui sont filtr&eacute;s',
									   'cfg_cache' => 'Dur&eacute;e de cache',
									   'cfg_cache_doc' => 'les informations &agrave; propos du niveau de risque de chaque visiteur sera gard&eacute; en cache un minimum de temps dans votre base de donn&eacute;es pour ne pas faire de requ&eacute;tes http:BL &agrave; chacune de leur visite.',
									   'cfg_stats'=>'Activer les statistiques de filtrage',
									   'stat_bouton'=>'Statistiques Filtrage',
									   'stat_gen' => 'Tous les Filtrages',
									   'stat_filtre1' => 'Bloqu&eacute;s',
									   'stat_filtre2' => 'Vers le pot de miel',
									   'stat_filtre3' => 'Forums Bloqu&eacute;s',
									   'stat_filtre4' => 'Vus mais pas Filtr&eacute;s',
									   'stat_filtre5' => 'Emails cach&eacute;s',
									   'titre_page_statistiques' => 'Statistiques de filtrage http:BL',
									   'stat_info_gauche' => 'Cette page pr&eacute;sente les statistiques des filtrages qui ont &eacute;t&eacute; eff&eacute;ctu&eacute;s par la fonctionalit&eacute; http:BL du plugin Honeypot',
									   'stat_info_visites' => 'visiteurs filtr&eacute;s :',
									   'stat_info_moyenne' => 'Moyenne des visiteurs filtr&eacute;s :',
									   'stat_info_aujourdhui' => 'aujourd\'hui :',
									   'stat_info_total' => 'total :',
									   'stat_info_threat' => 'Menace moyenne :',
									   'stat_info_par_mois' => 'Affichage par mois :',
									   );

?>
