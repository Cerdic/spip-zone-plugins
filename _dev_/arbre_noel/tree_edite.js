function deplie_arbre(){
	tree = $('#myTree');
	$('#myTree ul').show();
	$('img.expandImage', tree).attr('src',img_deplierbas);
}
function plie_arbre(){
	tree = $('#myTree');
	$('#myTree ul').hide();
	$('img.expandImage', tree).attr('src',img_deplierhaut);
}
var recall;
jQuery.fn.deplie = function(){
	$(this).show();
	$(this).siblings('img.expandImage').eq(0).attr('src',img_deplierbas);
	recall = true;
	jQuery.recallDroppables();
}

jQuery.fn.bascule = function() {
	subbranch = $(this).siblings('ul').eq(0);
	if (subbranch.is(':hidden')) {
		subbranch.show();
		$(this).attr('src',img_deplierbas);
	} else {
		subbranch.hide();
		$(this).attr('src',img_deplierhaut);
	}
}

$(document).ready(
	function()
	{
		tree = $('#myTree');
		//$('ul',tree).hide();
		$('li[ul]',tree).each(function(){
			if ($('ul',this).eq(0).is(':hidden'))
				$(this).prepend('<img src="'+img_deplierhaut+'" class="expandImage" />');
			else
				$(this).prepend('<img src="'+img_deplierbas+'" class="expandImage" />');
		});
		$('img.expandImage', tree).click(function (){$(this).bascule();});
		$('span.textHolder').Droppable(
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
		$('li.treeItem').Draggable(
			{
				revert		: true,
				ghosting : true,
				autoSize : true
			}
		);
	}
);