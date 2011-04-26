function mc_lancerlien(a,b){
	x='ma'+'ilto'+':'+a+'@'+b;
	return x;
}
$(document).ready(function(){
	$('.spancrypt').empty();
	$('.spancrypt').append('@');
	$('a.spip_mail').attr('title',function(i, val) {
		return val.replace(/\.\..t\.\./,'@');
	});
});
