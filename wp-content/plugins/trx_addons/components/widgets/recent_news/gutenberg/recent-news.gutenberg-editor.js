(function(blocks, editor, i18n, element) {
	// Set up variables
	var el = element.createElement;

	// Register Block - Recent News
	blocks.registerBlockType(
		'trx-addons/recent-news',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: i18n.__( 'Widget: Recent News' ),
			description: i18n.__( "Insert recent posts list with thumbs, post's meta and category" ),
			icon: 'list-view',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{				
					style: {
						type: 'string',
						default: 'news-magazine'
					},
					count: {
						type: 'number',
						default: 3
					},
					featured: {
						type: 'number',
						default: 3
					},
					columns: {
						type: 'number',
						default: 3
					},
					ids: {
						type: 'string',
						default: ''
					},
					category: {
						type: 'string',
						default: '0'
					},
					offset: {
						type: 'number',
						default: 0
					},
					orderby: {
						type: 'string',
						default: 'date'
					},
					order: {
						type: 'string',
						default: 'desc'
					},
					widget_title: {
						type: 'string',
						default: ''
					},
					title: {
						type: 'string',
						default: ''
					},
					subtitle: {
						type: 'string',
						default: ''
					},
					show_categories: {
						type: 'boolean',
						default: false
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/recent-news' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'general_params': el(
							'div', {}, trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Widget title
								{
									'name': 'widget_title',
									'title': i18n.__( 'Widget title' ),
									'descr': i18n.__( "Title of the widget" ),
									'type': 'text',
								},
								// Title
								{
									'name': 'title',
									'title': i18n.__( 'Widget title' ),
									'descr': i18n.__( "Title of the block" ),
									'type': 'text',
								},
								// Subtitle
								{
									'name': 'subtitle',
									'title': i18n.__( 'Subtitle' ),
									'descr': i18n.__( "Subtitle of the block" ),
									'type': 'text',
								},
								// List style
								{
									'name': 'style',
									'title': i18n.__( 'List stylew' ),
									'descr': i18n.__( "Select style to display news list" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_recent_news'] )
								},
								// Show categories
								{
									'name': 'show_categories',
									'title': i18n.__( "Show categories" ),
									'descr': i18n.__( "Show categories in the shortcode's header" ),
									'type': 'boolean'
								},
								// List IDs
								{
									'name': 'ids',
									'title': i18n.__( "List IDs" ),
									'descr': i18n.__( "Comma separated list of IDs list to display. If not empty, parameters 'cat', 'offset' and 'count' are ignored!" ),
									'type': 'text'
								},
								// Category
								{
									'name': 'category',
									'title': i18n.__( "Category" ),
									'descr': i18n.__( "Select a category to display. If empty - select news from any category or from the IDs list." ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['list_categories'] ),
									'dependency': {
										'ids': ['']
									}
								},
								// Total posts
								{
									'name': 'count',
									'title': i18n.__( "Total posts" ),
									'descr': i18n.__( "The number of displayed posts. If IDs are used, this parameter is ignored." ),
									'type': 'number',
									'min': 1,
									'dependency': {
										'ids': ['']
									}
								},
								// Columns
								{
									'name': 'columns',
									'title': i18n.__( "Columns" ),
									'descr': i18n.__( "How many columns use to show news list" ),
									'type': 'number',
									'min': 1,
									'dependency': {
										'style': ['news-magazine', 'news-portfolio']
									}
								},
								// Offset before select posts
								{
									'name': 'offset',
									'title': i18n.__( "Offset before select posts" ),
									'descr': i18n.__( "Skip posts before select next part" ),
									'type': 'number',
									'min': 0,
									'dependency': {
										'ids': ['']
									}
								},
								// How many posts will be displayed as featured?
								{
									'name': 'featured',
									'title': i18n.__( "Featured posts" ),
									'descr': i18n.__( "How many posts will be displayed as featured?" ),
									'type': 'number',
									'min': 0,
									'dependency': {
										'style': ['news-magazine']
									}
								},
								// Order by
								{
									'name': 'orderby',
									'title': i18n.__( "Order by" ),
									'descr': i18n.__( "Select how to sort the posts" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_query_orderby'] )
								},
								// Order
								{
									'name': 'order',
									'title': i18n.__( "Order" ),
									'descr': i18n.__( "Select sort order" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_query_orders'] )
								}
							], 'trx-addons/recent-news', props ), props )
						),
						'additional_params': el(
							'div', {},
							// ID, Class, CSS params
							trx_addons_gutenberg_add_param_id( props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/recent-news'
	) );
})( window.wp.blocks, window.wp.editor, window.wp.i18n, window.wp.element );
