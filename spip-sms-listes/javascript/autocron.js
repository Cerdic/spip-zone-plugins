var target = $('#envois_restants');
var total = $('#envois_total').html();
var target_pc = $('#envois_restant_pourcent');
function redirect_fin(){
	redirect = $('#redirect_after');
	if (redirect.length>0){
		href = redirect.attr('href');
		setTimeout('document.location.href = "'+href+'"',0);
	}
}
jQuery.fn.runProcessus = function(url) {
	var proc=this;
	var href=url;
	$(target).load(url,function(data){
		restant = $(target).html();
		pourcent=Math.round(restant/total*100);
		$(target_pc).html(pourcent);
		if (Math.round(restant)>0)
			$(proc).runProcessus(href);
		else
			redirect_fin();
	});
}
$('span.processus').each(function(){
	var href = $(this).attr('name');
	$(this).html(ajax_image_searching).runProcessus(href);
	//run_processus($(this).attr('id'));
});