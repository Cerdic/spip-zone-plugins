function mc_lancerlien(a,b){	x='ma'+'ilto'+':'+a+'@'+b;	return x;}
jQuery(function(){
	jQuery('.spancrypt').empty().append('@');
	jQuery('a.spip_mail').attr('title',function(i, val) {	return val.replace(/\.\..t\.\./,'@');	});
});