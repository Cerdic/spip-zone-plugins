function targetLinks() {
	var str;
	var where;
	where="_blank";

	for(var i=0;i<=(document.links.length-1);i++) {
	 str=document.links[i].href;
	 if((str.search(liens_sortants_site)==-1)&&((str.search('http://')!=-1)
	 ||(str.search('www.')!=-1)
	 ||(str.search('ftp://')!=-1))){
		document.links[i].target=where;
	 }
	}
}
if (window.jQuery)
	(function($){
		if(typeof onAjaxLoad == "function") onAjaxLoad(targetLinks);
		$('document').ready(targetLinks);
	})(jQuery);

