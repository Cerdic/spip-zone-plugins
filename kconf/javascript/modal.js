jQuery(document).ready(function($) {
   $("a.modal").modal();
});

(function($){
	$.fn.modal = function() {
		return this.each(function(){
			$(this).bind('click',function(){
				$("<div id='modal_back' style='position:"+$.Modal.position+";top:0px;left:0px;width:100%;height:"+$('body').height()+"px;background-color:black;opacity:0.8;z-index:89;'>&nbsp;</div>")
				.appendTo('body')
				.bind(
					"click",
					function(){
						$(this).remove();
						$('.modal_front').remove();
					}
				);
				$.Modal.charge(this.href);
				if ($.Modal.position=="absolute")	window.scrollTo(0,0); /*IE<7*/
				return false;
			});
		});
	};
	
	$.Modal = {
	  position : ($.browser.msie && parseInt($.browser.version)<7) ? 'absolute' : 'fixed' /*IE<7*/,
		charge : function(href) {
			var load = $("<img style='position:"+$.Modal.position+";top:50%;left:50%;margin-top:-16px;margin-left:-16px;z-index:91;' src='http://www.google.com/accounts/hosted/helpcenter/images/tooltips/spin_32.gif' />");
			$.ajax({
				type: "GET",
				url: href,
				beforeSend : function() {
					$('body').append(load);
				},
				success: function(data) {
					var old = $('#modal_front');
					var modal = $("<div class='modal_front' style='position:"+$.Modal.position+";top:50%;left:50%;background-color:white;display:none;z-index:90;'></div>")
						.append(data)
						.appendTo('body')
						.find("a.modal")
						.each(function(){
							$(this).bind("click",function(){
								$.Modal.charge(this.href);
								return false;
							})
						})
						.end();
					var o = {width:modal.width(),height:modal.height()}
					modal.css({
						width: o.width+'px',
						height: o.height+'px',
						marginTop: '-'+parseInt(o.height/2)+'px',
						marginLeft: '-'+parseInt(o.width/2)+'px'
					})
					if (old.length>0) {
						old.attr('id','')
							.fadeOut(1000,function(){
								$(this).remove();
							});
					}
					modal.fadeIn(1000)
						.attr('id',"modal_front");
					load.remove();
				}
			});
		},
		recharge: function() {
			$('a.modal',this)
				.each(function(){
					$(this).bind("click",function(){
						$.Modal.charge(this.href);
						return false;
					})
				})
		}
	};
})(jQuery);