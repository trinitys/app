var WikiaTray = {

	// Properties
	open: false,
	selected: null,
	speed: 250,
	$search: $('#WikiaTrayHeader input[type="search"]'),
	$avatar: $('#WikiaTrayHeader .avatar'),

	// Methods
	init: function() {
		// Bindings
		this.$search.on( 'input', $.proxy( this.searchChange, this ) );
		this.$avatar.on( 'click', $.proxy( this.avatarClick, this ) );
	},

	searchChange: function() {
		if ( this.$search.val().length == 0 ) {
			this.closeTray();
		} else {
			this.autoComplete();
		}
	},

	autoComplete: function() {
		$.when(
			$.loadMustache()
		).done(function() {

			$.ajax({
				url: wgServer + wgScript + '?action=ajax&rs=getLinkSuggest&format=json',
				data: {
					query: WikiaTray.$search.val()
				},
				success: function( data ) {
					if ( data.suggestions.length ) {
						var list = $('#WikiaTray .wiki-matches');

						console.log('opening');
						WikiaTray.openTray('search');

						list.empty();
						$.each( data.suggestions, function(index, value) {
							list.append( $('#WikiaTray-wiki-match').mustache({
								name: value,
								href: mw.config.values.wgArticlePath.replace('$1', value)
							}) );
						});
					} else {
						console.log('closing');
						WikiaTray.closeTray();
					}
				}
			});

		});
	},

	avatarClick: function() {
		if (this.selected == 'user') {
			this.closeTray();
		} else {
			this.openTray( 'user' );
		}
	},

	selectTab: function( name ) {
		this.deselectTabs();
		$('#WikiaTrayHeader .' + name + '-tab').addClass('selected');
		$('#WikiaTray').find('.user, .search').hide();
		$('#WikiaTray .' + name).show();
		this.selected = name;
	},

	deselectTabs: function() {
		$('#WikiaTrayHeader .tray-header-tab').removeClass('selected');
		this.selected = null;
	},

	openTray: function( tab ) {
		// Don't open if already open
		if ( this.open ) {
			return;
		}

		// Scroll to the top of the page
		$(window).scrollTop( 0 );

		// Highlight tab
		this.selectTab( tab );

		// Animate
		$('body')
			.addClass('tray-open')
			.stop()
			.clearQueue()
			.animate( {
				'margin-right': 300,
				'margin-left': -300
			}, this.speed );

		$('#WikiaTray')
			.show()
			.stop()
			.clearQueue()
			.animate( {
				'margin-right': 0
				},
			this.speed );

		// Update property
		this.open = true;
	},

	closeTray: function() {
		if ( !this.open ) {
			return;
		}

		this.deselectTabs();

		$('body')
			.removeClass('tray-open')
			.stop()
			.clearQueue()
			.animate( {
				'margin-right': 0,
				'margin-left': 0
			}, this.speed );

		$('#WikiaTray')
			.stop()
			.clearQueue()
			.animate( {
				'margin-right': -300
			}, this.speed, function() {
				$(this).hide();
			} );

		this.open = false;
	}

};

WikiaTray.init();