/* global jQuery:false */
/* global CHINCHILLA_STORAGE:false */

jQuery( window ).on( 'load', function() {

	"use strict";

	var gutenberg_editor_inited = false;

 	chinchilla_gutenberg_first_init();

	if ( typeof window.MutationObserver !== 'undefined' ) {
		// Create the observer to reinit visual editor after switch from code editor to visual editor
		chinchilla_create_observer('check_visual_editor', jQuery('.block-editor,#edit-site-editor,#widgets-editor').eq(0), function( mutationsList ) {
			var gutenberg_editor = chinchilla_gutenberg_editor_object();
			if ( gutenberg_editor.length ) {
				chinchilla_gutenberg_first_init( gutenberg_editor );
			}
		});
		// Create the observer to add class 'scheme_xxx' to the each widgets area in the Widgets Block Editor
		var widgets_editor = jQuery('#widgets-editor').eq(0);
		if ( widgets_editor.length ) {
			chinchilla_create_observer('check_editor_styles_wrapper', widgets_editor, function( mutationsList ) {
				var styles_wrapper = widgets_editor.find( '.editor-styles-wrapper:not([class*="scheme_"])' );
				if ( styles_wrapper.length ) {
					styles_wrapper.addClass( 'scheme_' + CHINCHILLA_STORAGE['color_scheme'] );
				} else {
					chinchilla_remove_observer( 'check_editor_styles_wrapper' );
				}
			});
		}
	}

	// Return Gutenberg editor object
	function chinchilla_gutenberg_editor_object() {
		// Get Post Editor
		var gutenberg_editor = jQuery( '.edit-post-visual-editor:not(.chinchilla_inited)' ).eq( 0 );
		if ( ! gutenberg_editor.length ) {
			// Check if Full Site Editor exists
			var editor_frame = jQuery( 'iframe[name="editor-canvas"]' );
			if ( editor_frame.length ) {
				editor_frame = jQuery( editor_frame.get(0).contentDocument.body );
				if ( editor_frame.hasClass('editor-styles-wrapper') && ! editor_frame.hasClass('chinchilla_inited') ) {
					gutenberg_editor = editor_frame;
				}
			}
			// Check if Widgets Editor exists
			gutenberg_editor = jQuery( '.edit-widgets-block-editor:not(.chinchilla_inited)' ).eq( 0 );
		}
		return gutenberg_editor;
	}

	// Init on page load
	function chinchilla_gutenberg_first_init( gutenberg_editor ) {

		// Get Gutenberg editor object
		if ( ! gutenberg_editor ) {
			gutenberg_editor = chinchilla_gutenberg_editor_object();
			if ( ! gutenberg_editor.length ) {
				return;
			}
		}

		var old_GB = gutenberg_editor.hasClass( 'editor-styles-wrapper' ) && gutenberg_editor.hasClass( 'edit-post-visual-editor' ),
			widgets_GB = gutenberg_editor.hasClass( 'edit-widgets-block-editor' ),
			styles_wrapper  = old_GB || gutenberg_editor.hasClass( 'editor-styles-wrapper' )
								? gutenberg_editor
								: gutenberg_editor.find( '.editor-styles-wrapper' ),
			writing_flow    = gutenberg_editor.find( '.block-editor-writing-flow' ),
			sidebar_wrapper = old_GB
								? gutenberg_editor
								: writing_flow;

		// Add color scheme to the editor and to the wrapper '.block-editor-writing-flow' (instead '.block-editor-block-list__layout')
		styles_wrapper.addClass( 'scheme_' + CHINCHILLA_STORAGE['color_scheme'] );
		writing_flow.addClass( 'scheme_' + CHINCHILLA_STORAGE['color_scheme'] );

		if ( ! widgets_GB ) {
			// Copy post-type to the styles_wrapper
			styles_wrapper.addClass( chinchilla_get_class_by_prefix( gutenberg_editor.attr('class'), 'post-type-' ) );
		
			// Decorate sidebar placeholder
			styles_wrapper
				.addClass( 'sidebar_position_' + CHINCHILLA_STORAGE['sidebar_position'] )
				.addClass( CHINCHILLA_STORAGE['expand_content'] + '_content' );
			if ( CHINCHILLA_STORAGE['sidebar_position'] == 'left' && old_GB ) {
				sidebar_wrapper.prepend( '<div class="editor-post-sidebar-holder"></div>' );
			} else if ( CHINCHILLA_STORAGE['sidebar_position'] != 'hide' ) {
				sidebar_wrapper.append( '<div class="editor-post-sidebar-holder"></div>' );
			}
		}

		gutenberg_editor.addClass('chinchilla_inited');
		gutenberg_editor_inited = true;

		// Remove observer
		chinchilla_remove_observer( 'check_visual_editor' );
	}
} );
