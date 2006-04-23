<h1>{'liens_admin'|translate}</h1>
<div id="administration">
	<p class="texte">
	{'admin_intro'|translate:$link_doc}
	</p>
	
	<div id="generalAdmin">
	
		<h3>{'install_general_setup'|translate}</h3>
		<h5>{'admin_configuration'|translate}</h5>
		<ul>
		<li><a href="./?exec=phpmv&mod=admin_general_config">{'admin_general_conf'|translate}</a></li>
		<li><a href="./?exec=phpmv&mod=admin_db_config">{'install_database_setup'|translate}</a></li>
		</ul>
		
		<h5>{'admin_user_title'|translate}</h5>
		<ul>
		<li><a href="./?exec=phpmv&mod=admin_group">{'admin_group_title'|translate}</a></li>
		<li><a href="./?exec=phpmv&mod=admin_user&amp;action=add">{'admin_user_add'|translate}</a></li>
		<li><a href="./?exec=phpmv&mod=admin_user&amp;action=mod">{'admin_user_mod'|translate}</a></li>
		<li><a href="./?exec=phpmv&mod=admin_user&amp;action=del">{'admin_user_del'|translate}</a></li>
		</ul
		
		<h5>{'admin_server_info'|translate}</h5>
		<ul>
		<li><a href="./?exec=phpmv&mod=admin_server_info">{'admin_server_info'|translate}</a></li>
		</ul>
		
		<h5>{'generique_other'|translate}</h5>
		<ul>
		<li><a href="./?exec=phpmv&mod=send_mail">{'admin_send_mail'|translate}</a></li>
		<li><a href="./?exec=phpmv&mod=view_rss&amp;rss_hash={$rss_hash}">{'admin_rss_feed'|translate}</a></li>
		</ul>
	</div>
	
	
	<div id="detailAdmin">
		<h3>{'admin_site_admin'|translate}</h3>
		<h5>{'install_general_setup'|translate}</h5>
		<ul>
		<li><a href="./?exec=phpmv&mod=admin_site_general&amp;action=add">{'admin_site_add'|translate}</a></li>
		<li><a href="./?exec=phpmv&mod=admin_site_general&amp;action=mod">{'admin_site_mod'|translate}</a></li>
		<li><a href="./?exec=phpmv&mod=admin_site_general&amp;action=del">{'admin_site_del'|translate}</a></li>
		</ul>
		
		<h5>{'generique_other'|translate}</h5>
		<ul>
		<li><a href="./?exec=phpmv&mod=admin_site_javascript_code">{'admin_afficherjavascript'|translate} <b>(+ HELP with page naming, files download, URL click count, etc.)</b></a></li>
		<li><a href="./?exec=phpmv&mod=admin_site_cookie_exclude">{'admin_cookieadmin'|translate}</a></li>
		<li><a href="./?exec=phpmv&mod=admin_site_ip_exclude">{'admin_ip_ranges'|translate}</a></li>
		<li><a href="./?exec=phpmv&mod=admin_site_urls">{'admin_url_alias'|translate}</a></li>
		</ul>
		
		<h5>{'affluents_newslettersimg'|translate}</h5>
		<small><p>To detect a visitor from a newsletter in the Referrers section of phpMyVisites, you have
		to add the parameter "nid" in the URLs in your newsletters.</p>
		<p>For example if you have a link in your newsletter which is "http://www.site.com/?exec=phpmv&part=misc"</p>
		<p>You have to change it to "http://www.site.com/?exec=phpmv&part=misc&nid=XXXXXXX" where XXXXXXX is the nid 
		of the newsletter you've saved in phpmyvisites.</p>
		<p>Another example : "http://www.site.com/" will be "http://www.site.com/?nid=XXXXXXX".</p>
		</small>
		<ul>
		<li><a href="./?exec=phpmv&mod=admin_site_newsletters&amp;action=add">{'admin_nl_add'|translate}</a></li>
		<li><a href="./?exec=phpmv&mod=admin_site_newsletters&amp;action=mod">{'admin_nl_mod'|translate}</a></li>
		<li><a href="./?exec=phpmv&mod=admin_site_newsletters&amp;action=del">{'admin_nl_del'|translate}</a></li>
		</ul>
		
		<h5>{'affluents_partenairesimg'|translate}</h5>
		<small>
		<p>To detect a partner website as is, you have to enter its url or add a parameters to the link from its site
		to your site (as in the newsletters). This parameter's name is "pid".</p>
		<p>For example if the partner "Tourloutiti" has a "pid=14", you can ask him to change its link to your site adding "pid=14" as an URL parameter.
		But the simplest is to save all its urls and "Tourloutiti" will be automatically recognized as a partner website in the Referers part.</p>
		</small>
		<ul>	
		<li><a href="./?exec=phpmv&mod=admin_site_partners&amp;action=add">{'admin_partner_add'|translate}</a></li>
		<li><a href="./?exec=phpmv&mod=admin_site_partners&amp;action=mod">{'admin_partner_mod'|translate}</a></li>
		<li><a href="./?exec=phpmv&mod=admin_site_partners&amp;action=del">{'admin_partner_del'|translate}</a></li>
		</ul>
	</div>
	
	<div class="both"></div>
</div>
<p align="center">Version : phpMyVisites {$PHPMV_VERSION}</p>