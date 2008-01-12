/*
	// javascript/lilo_login.js

	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	*/
	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of LiLo.
	
	LiLo is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	LiLo is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with LiLo; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de LiLo. 
	
	LiLo est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	LiLo est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/
	
	/*
		Si modification, vider le cache (le script est compressé par 'compacte'
	*/
	
jQuery().ready(function(){
	var alea_actuel = '', alea_futur = '';
	var qstring = window.location.search;
	var page_login = (qstring.length && (qstring.search(/page=login/)>0));

	$('#var_login_id').blur(function(){
		if($(this).val().length) {
			$.ajax({
				type: 'post'
				, url: $('#lilo_url_action').val()
				, data: {var_login: $('#var_login_id').val(), url: $('input[@name=url]').val()}
				, success: function(data) {
					var result = data.split(' ');
					var id_auteur = result[0];
					alea_actuel = result[1];
					alea_futur = result[2];
					var logo_src = result[3];
					$('input[@name=session_password_md5]').val(alea_actuel);
					$('input[@name=next_session_password_md5]').val(alea_futur);
					// change logo uniquement si OK et page login
					if((login_voir_logo=='oui') && page_login && logo_src.length) {
						$('img.lilo-logo').attr({ src: logo_src, alt: 'Logo auteur' });
					}
					return true;
				}
				, error: function(xmlhttprequest, type, e) {
					if(lilo_config['login_identifiant_inconnu']) {
						alert(lilo_config['login_identifiant_inconnu']);
					}
				}
			}); // end $.ajax
		} // end if
	}); // end blur
	
	$('#lilo_login').submit( function() {
		if (
			(this.session_password.value.length > 0)
			&& (this.var_login.value.length > 0)
			) {
			this.session_password_md5.value = calcMD5(alea_actuel + this.session_password.value);
			this.next_session_password_md5.value = calcMD5(alea_futur + this.session_password.value);
			this.session_login_hidden.value = this.var_login.value;
			this.session_password.value = ""; 
			return(true);
			}
		return(false);
	}); // end submit
	
	$('#lilo-ventre').hide();
	$('#lilo-statut-public').hover(function(){
		$('#lilo-ventre').show('slow');
	 },function(){
		$('#lilo-ventre').hide('slow');
	});
	
});