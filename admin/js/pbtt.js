(function($) {

	"use strict";

	var pbZones,
			$pbNormalTabs;

            var pbtt = {};

            init();
	/**
	 * Helper fonction to test if variable is set and not null
	 *
	 * @since 1.1
	 *
	 * @param	 mixed variable
	 * @return	bool
	 */
	function isset( variable ) {

		if ( undefined === variable ) { return false; }
		else if ( null === variable ) { return false; }

		return true;
	}

	/**
	 * Create the tab menu
	 * @since 1.0
	 */
	function init() {

		var zone = 'advanced';

			pbtt[zone] = {
				tabs		: $("<ul>", {"id": "pbtt_tabs_"+zone, "class": "pbtt-tabs" }),
				wrapper	: $("#"+zone+"-sortables"),
			};

			setTabs( zone );

			pbtt[zone].wrapper
				.before(pbtt[zone].tabs)
				.addClass("pbtt-postboxes")
				.children()
					.removeClass('active closed')
					.filter(":not(.hide-if-js):first")
						.addClass("active");

			updateHeight( pbtt[zone].wrapper );

		var zone = 'normal';

			pbtt[zone] = {
				tabs		: $("<ul>", {"id": "pbtt_tabs_"+zone, "class": "pbtt-tabs" }),
				wrapper	: $("#"+zone+"-sortables"),
			};

			setTabs( zone );

			pbtt[zone].wrapper
				.before(pbtt[zone].tabs)
				.addClass("pbtt-postboxes")
				.children()
					.removeClass('active closed')
					.filter(":not(.hide-if-js):first")
						.addClass("active");

			updateHeight( pbtt[zone].wrapper );


		// Events
		$(".pbtt-tabs").on( "click", "li", changeActive );
		$(".hide-postbox-tog").on("click", hideshow );

		setSortable();
		receivePostbox();
	}

	/**
	 * Create the tabs according to visible postboxes in zone
	 * @since 1.1
	 */
	function setTabs( zone ) {

		// Clean tabs
		pbtt[zone].tabs.children().remove();

		var $el,
				$tab;

		pbtt[zone].wrapper.children().each(function(i, el) {

			$el		= $(el);
			$tab	= $( "<li>", {"id": el.id +"_tab"} ).text( $el.children("h2").text() );

			if ( $el.hasClass("hide-if-js") ) {
				$tab.addClass("hide");
			}

			pbtt[zone].tabs.append($tab);
		});

		// Activate first available tab
		pbtt[zone].tabs.children(":not(.hide):first").addClass("active");
	}

	/**
	 * Update the shown postbox according to selected tab
	 * @since 1.0.2
	 */
	function changeActive( event ) {
		event.preventDefault();

		var $this					= $(event.target),
				$zoneWrapper	= $(event.delegateTarget).next('.pbtt-postboxes');

		$this
			.addClass("active")
			.siblings()
				.removeClass("active");

		$zoneWrapper.children().removeClass("active");
		$zoneWrapper.children().eq( $this.index() ).addClass("active");

		updateHeight( $zoneWrapper );
	}

	/**
	 * Update the postbox container height
	 * @since 1.1
	 */
	function updateHeight( $zoneWrapper ) {
		$zoneWrapper.height( $zoneWrapper.children('.active').height() );
	}

	/**
	 * Hide or show the postboxes when screen option if updated
	 * @since 1.0.2
	 */
	function hideshow( event ) {

		var $tab			= $("#" + event.target.value +"_tab"),
				$wrapper	= $("#" + event.target.value).parent();

		$tab.toggleClass("hide");

		if ( $tab.is(".hide") ) {
			$tab.siblings(":not(.hide):first").click();
		}
		else {
			$tab.click();
		}

		updateHeight( $wrapper );
	}

	/**
	 * Sets and handle the sorting of postboxes in tab
	 * @since 1.0.2
	 */
	function setSortable() {

		var selectors = '#pbtt_tabs_'+ Object.keys(pbtt).join(',#pbtt_tabs_');

		$(selectors).sortable({
			opacity: 0.65,
			cursor: 'move',
			placeholder: 'pbtt-placeholder',
			forcePlaceholderSize: true,
			connectWith: '.meta-box-sortables:not(.pbtt-postboxes), .pbtt-tabs',
			start: sortStart,
			update: sortUpdate,
		});
	}

	/**
	 * Add "original" information to the sort item
	 * in order to be able to correctly sort at the end.
	 *
	 * @since 1.1
	 *
	 * @param Event		event	Reference to current event
	 * @param Object	ui		jQuery UI object
	 */
	function sortStart( event, ui ) {

		ui.item.data( {
			'pos'		: ui.item.index(),
			'parent': ui.item.parent().prop('id'),
		});

	}

	/**
	* Sort and move correctly the postbox according to sorted/moved tab
	*
	* @since 1.1
	*
	* @param Event		event	Reference to current event
	* @param Object	ui		jQuery UI object
	*/
	function sortUpdate( event, ui ) {

		// Avoid the second event fire when sorting from connected lists
		if ( isset(ui.sender) )
			return;

		var $el				= ui.item,
				newPos		= $el.index(),
				oldPos		= $el.data('pos'),
				oldParent	= $el.data("parent"),
				newParent	= $el.parent().prop('id'),
				oldZone		= oldParent.replace("pbtt_tabs_", ""),
				newZone		= newParent.replace("pbtt_tabs_", ""),

				$movedPB	= pbtt[oldZone].wrapper.children().eq(oldPos).detach();

		if ( $("#"+newParent).is('.pbtt-tabs') ) {

			var nbTabs = $el.siblings().length;

			if			( 0 		 === newPos )	{ pbtt[newZone].wrapper.prepend($movedPB); }
			else if	( nbTabs === newPos )	{ pbtt[newZone].wrapper.append($movedPB); }
			else													{ pbtt[newZone].wrapper.children().eq(newPos-1).after( $movedPB ); }

			$el.click();

			if ( oldZone !== newZone ) {
				pbtt[oldZone].tabs.children(":not(.hide):first").click();
			}
		}
		else {
		// Moved to different zone which is not tabbed
			$el
				.after( $movedPB )
				.remove();

			pbtt[oldZone].tabs.children(":not(.hide):first").click();
		}


		// Trigger WP save postboxes order in DB
		postboxes.save_order( pagenow );
	}

	/**
	 * Update the tabs when new postbox from different zone
	 * @since 1.1
	 */
	function receivePostbox() {

		// create an observer instance
		var observer = new MutationObserver(function(mutations) {

			if ( 2 === mutations.length &&
				isset(mutations[0]) && isset(mutations[0].addedNodes[0]) && mutations[0].addedNodes[0].classList.contains('postbox') &&
				isset(mutations[1]) && isset(mutations[1].removedNodes[0]) && mutations[1].removedNodes[0].classList.contains('sortable-placeholder') ) {

					var zone = mutations[0].target.id.replace( "-sortables", "");

					setTabs( zone );

					pbtt[zone].wrapper
						.children(":not(.hide-if-js):first")
						.addClass("active")
						.siblings()
							.removeClass('active');

					updateHeight( pbtt[zone].wrapper );
			}
		});

		for (var zone in pbtt) {
			observer.observe( document.getElementById( zone+"-sortables" ), { childList: true } );
		}
	}


}(jQuery));
