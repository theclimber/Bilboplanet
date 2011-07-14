Ext.onReady(function(){
	var stars = Ext.query('.stars');
	Ext.each(stars, function(item, index) {
		new Ext.ux.Rating(item.id, {
			canReset: false,
			split: 2,
			starWidth: 16
		});
	});

/*	Ext.select('details').on('click', function(e, t) {
		var parent = Ext.fly(t).parent().parent().parent();
		for(var i in parent.dom.children)
		{
			var node= Ext.fly(parent.dom.children[i]);
			if (node.getStyle('display') == 'none') {
				node.setStyle('display', 'block');
			}
		}
   });*/
});
