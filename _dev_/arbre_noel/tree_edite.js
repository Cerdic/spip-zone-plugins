function deplie_arbre(){
	$('#myTree ul').show();
	$('img.expandImage', tree).attr('src',img_deplierbas);
}
function plie_arbre(){
	$('#myTree ul').hide();
	$('img.expandImage', tree).attr('src',img_deplierhaut);
}
var recall;
jQuery.fn.deplie = function(){
	$(this).show();
	$('img.expandImage',$(this).parent()).eq(0).attr('src',img_deplierbas);
	recall = true;
}

$(document).ready(
	function()
	{
		tree = $('#myTree');
		//$('ul',tree).hide();
		$('li[ul]',tree).each(function(){
			if ($('ul',this).eq(0).css('display')=='none')
				$(this).prepend('<img src="'+img_deplierhaut+'" width="16" height="16" class="expandImage" />');
			else
				$(this).prepend('<img src="'+img_deplierbas+'" width="16" height="16" class="expandImage" />');
		});
		$('img.expandImage', tree).click(
			function() {
				subbranch = $('ul', this.parentNode).eq(0);
				if (subbranch.css('display') == 'none') {
					subbranch.show();
					this.src = img_deplierbas;
				} else {
					subbranch.hide();
					this.src = img_deplierhaut;
				}
			}
		);
		$('.textHolder').Droppable(
			{
				accept			: 'treeItem',
				hoverclass		: 'none',
				activeclass		: 'fakeClass',
				tollerance		: 'pointer',
				onhover			: function(dragged)
				{
					$(this).parent().addClass('selected');
					if (!this.expanded) {
						subbranch=$('ul', this.parentNode).eq(0);
						if (subbranch.css('display')=='none'){
							subbranch.pause(1000).deplie();
							this.expanded = true;
						}
					}
				},
				onout			: function()
				{
					$(this).parent().removeClass('selected');
					if (this.expanded){
						subbranch=$('ul', this.parentNode).eq(0);
						subbranch.unpause();
						if (recall){
							jQuery.recallDroppables();
							recall=false;
						}
					}
					this.expanded = false;
				},
				ondrop			: function(dropped)
				{
					$(this).parent().removeClass('selected');
					if (this.expanded)
						$('ul', this.parentNode).eq(0).unpause();
					this.expanded = false;
					subbranch = $('ul', this.parentNode).eq(0);
					if (subbranch.size() == 0) {
						$(this).parent().prepend('<img src="'+img_deplierbas+'" width="16" height="16" class="expandImage" />');
						$(this).after('<ul></ul>');
						subbranch = $('ul', this.parentNode);
					}
					if (subbranch.css('display')=='none'){
						subbranch.show();
						$('img.expandImage', this.parentNode).eq(0).attr('src',img_deplierbas);
					}
					var target=$(this).parent().id();
					var quoi=$(dropped).id();
					action=quoi+":"+target;
					//$("#debug").append(quoi+"-&gt;"+target+"<br/>");
					$("#deplacements").html($("#deplacements").text()+"\n"+action);
					$("#apply").show();

					oldParent = dropped.parentNode;
					subbranch.append(dropped);
					oldBranches = $('li', oldParent);
					if (oldBranches.size() == 0) {
						$('img.expandImage', oldParent.parentNode).remove();
						$(oldParent).remove();
					}
				}
			}
		);
		$('li.treeItem').Draggable(
			{
				revert		: true
			}
		);
	}
);