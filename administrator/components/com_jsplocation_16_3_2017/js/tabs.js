var JTabs = new Class({
	Implements: [Options, Events],

	options : {
		display: 0,
		onActive: function(title, description) {
			description.setStyle('display', 'block');
			title.addClass('open').removeClass('closed');
		},
		onBackground: function(title, description){
			description.setStyle('display', 'none');
			title.addClass('closed').removeClass('open');
		},
		titleSelector: 'dt',
		descriptionSelector: 'dd'
	},

	initialize: function(dlist, options){
		this.setOptions(options);
		this.dlist = document.id(dlist);
		this.titles = this.dlist.getChildren(this.options.titleSelector);
		this.descriptions = this.dlist.getChildren(this.options.descriptionSelector);
		this.content = new Element('div').inject(this.dlist, 'after').addClass('current');

	this.options.display = this.options.display.toInt().limit(0, this.titles.length-1);

		for (var i = 0, l = this.titles.length; i < l; i++){
			var title = this.titles[i];
			var description = this.descriptions[i];
			title.setStyle('cursor', 'pointer');
			title.addEvent('click', this.display.bind(this, i));
			description.inject(this.content);
		}

		this.display(this.options.display);

		if (this.options.initialize) this.options.initialize.call(this);
	},

	hideAllBut: function(but) {
		for (var i = 0, l = this.titles.length; i < l; i++){
			if (i != but) this.fireEvent('onBackground', [this.titles[i], this.descriptions[i]]);
		}
	},

	display: function(i) {
		this.hideAllBut(i);
		this.fireEvent('onActive', [this.titles[i], this.descriptions[i]]);
	Cookie.write('jpanetabs_' + this.dlist.id, i);
	}
});
