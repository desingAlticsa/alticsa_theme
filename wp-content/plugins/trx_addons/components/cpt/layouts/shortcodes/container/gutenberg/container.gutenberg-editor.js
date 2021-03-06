(function(blocks, editor, i18n, element) {
	// Set up variables
	var el = element.createElement;

	// Register Block - Container
	blocks.registerBlockType(
		'trx-addons/layouts-container',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: i18n.__( 'Container' ),
			description: i18n.__( 'Container for other blocks in layouts' ),
			icon: 'schedule',
			category: 'trx-addons-layouts',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					align: {
						type: 'string',
						default: ''
					},
					content: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_hide(true),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/layouts-container' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'parent': true,
						'allowedblocks': TRX_ADDONS_STORAGE['gutenberg_allowed_blocks'],
						'general_params': el(
							'div', {}, trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Layout
								{
									'name': 'type',
									'title': i18n.__( 'Layout' ),
									'descr': i18n.__( "Select layout's type" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_cart'] ),
								},
								// Content alignment
								{
									'name': 'align',
									'title': i18n.__( 'Content alignment' ),
									'descr': i18n.__( "Select alignment of the inner content in this block" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( {
										'inherit': i18n.__( 'Inherit' ),
										'left': i18n.__( 'Left' ),
										'center': i18n.__( 'Center' ),
										'right': i18n.__( 'Right' ),
									} )
								}
							], 'trx-addons/layouts-container', props ), props )
						),
						'additional_params': el(
							'div', {},
							// Hide on devices params
							trx_addons_gutenberg_add_param_hide( props, true ),
							// ID, Class, CSS params
							trx_addons_gutenberg_add_param_id( props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( wp.editor.InnerBlocks.Content, {} );
			}
		},
		'trx-addons/layouts-container'
	) );
})( window.wp.blocks, window.wp.editor, window.wp.i18n, window.wp.element );
