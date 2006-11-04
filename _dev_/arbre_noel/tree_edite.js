function deplie_arbre(){
	tree = $('#myTree');
	$('ul:hidden',tree).siblings('img.expandImage').each(function(){$(this).bascule()});
}
function plie_arbre(){
	tree = $('#myTree');
	$('#myTree ul').hide();
	$('img.expandImage', tree).attr('src',img_deplierhaut);
}
jQuery.fn.set_expandImage = function(){
	$('ul:hidden',$(this)).parent().prepend('<img src="'+img_deplierhaut+'" class="expandImage" />');
	$('ul:visible',$(this)).parent().prepend('<img src="'+img_deplierbas+'" class="expandImage" />');
	$('img.expandImage', $(this)).click(function (){$(this).bascule();});
	return $(this);
}

var recall;
jQuery.fn.deplie = function(){
	$(this).show();
	$(this).siblings('img.expandImage').eq(0).attr('src',img_deplierbas);
	$(this).children('a.ajax').each(function(){
		$(this).before("<div>"+ajax_image_searching+"</div>");
		var id = $(this).parent().id();
		$(this).parent().load($(this).href()+"&var_ajaxcharset=utf-8",function(){$("#"+id).set_expandImage().set_droppables();jQuery.recallDroppables();});
	});
	recall = true;
	jQuery.recallDroppables();
	return $(this);
}

jQuery.fn.bascule = function() {
	subbranch = $(this).siblings('ul').eq(0);
	if (subbranch.is(':hidden')) {
		subbranch.show();
		$(this).attr('src',img_deplierbas);
		subbranch.children('a.ajax').each(function(){
			$(this).before("<div>"+ajax_image_searching+"</div>");
			var id = $(this).parent().id();
			$(this).parent().load($(this).href()+"&var_ajaxcharset=utf-8",function(){$("#"+id).set_expandImage().set_droppables();});
		});
	} else {
		subbranch.hide();
		$(this).attr('src',img_deplierhaut);
	}
	return $(this);
}

jQuery.fn.set_droppables = function(){
	$('span.holder',$(this)).Droppable(
		{
			accept			: 'treeItem',
			hoverclass		: 'none',
			activeclass		: 'fakeClass',
			tollerance		: 'intersect',
			onhover			: function(dragged)
			{
				$(this).parent().addClass('selected');
				if (!this.expanded) {
					subbranch = $(this).siblings('ul').eq(0);
					if (subbranch.is(':hidden')){
						subbranch.pause(1000).deplie();
						this.expanded = true;
					}
				}
			},
			onout			: function()
			{
				$(this).parent().removeClass('selected');
				if (this.expanded){
					subbranch = $(this).siblings('ul').eq(0);
					subbranch.unpause();
					if (recall){
						recall=false;
					}
				}
				this.expanded = false;
			},
			ondrop			: function(dropped)
			{
				$(this).parent().removeClass('selected');
				subbranch = $(this).siblings('ul').eq(0);
				if (this.expanded)
					subbranch.unpause();
				this.expanded = false;
				if (subbranch.size() == 0) {
					$(this).parent().prepend('<img src="'+img_deplierbas+'" width="16" height="16" class="expandImage" />');
					$(this).parent().append('<ul></ul>');
					$(this).siblings('img.expandImage').click(function (){$(this).bascule();});
					subbranch = $(this).siblings('ul').eq(0);
				}
				if (subbranch.is(':hidden')){
					subbranch.show();
					$(this).siblings('img.expandImage').eq(0).attr('src',img_deplierbas);
				}
				var target=$(this).parent().id();
				var quoi=$(dropped).id();
				action=quoi+":"+target;
				//$("#debug").append(quoi+"-&gt;"+target+"<br/>");
				var dep = $("#deplacements");
				dep.html(dep.text()+"\n"+action);
				$("#apply").show();

				oldParent = dropped.parentNode;
				subbranch.append(dropped);
				oldBranches = $('li', oldParent);
				if (oldBranches.size() == 0) {
					$(oldParent).siblings('img.expandImage').remove();
					$(oldParent).remove();
				}
			}
		}
	);
	$('li.treeItem',$(this)).Draggable(
		{
			revert		: true,
			ghosting : true,
			autoSize : true
		}
	);
}

$(document).ready(
	function()
	{
		$('#myTree').set_expandImage();
		$('#myTree').set_droppables();
	}
);