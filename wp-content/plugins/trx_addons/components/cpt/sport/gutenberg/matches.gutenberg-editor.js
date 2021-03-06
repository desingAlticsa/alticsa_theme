(function(blocks, editor, i18n, element) {
	// Set up variables
	var el = element.createElement;

	// Register Block - Matches
	blocks.registerBlockType(
		'trx-addons/matches',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: i18n.__( 'Matches' ),
			icon: 'universal-access',
			category: 'trx-addons-cpt',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					sport: {
						type: 'string',
						default:  TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_sport_default']
					},
					competition: {
						type: 'string',
						default: '0'
					},
					round: {
						type: 'string',
						default: '0'
					},
					main_matches: {
						type: 'boolean',
						default: false
					},
					position: {
						type: 'string',
						default: 'top'
					},
					slider: {
						type: 'boolean',
						default: false
					}
				},
				trx_addons_gutenberg_get_param_query(),
				trx_addons_gutenberg_get_param_title(),
				trx_addons_gutenberg_get_param_button(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/matches' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'general_params': el(
							'div', {}, trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Layout
								{
									'name': 'type',
									'title': i18n.__( 'Layout' ),
									'descr': i18n.__( "Select shortcodes's layout" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['trx_sc_matches'] )
								},
								// Sport
								{
									'name': 'sport',
									'title': i18n.__( 'Sport' ),
									'descr': i18n.__( "Select Sport to display matches" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_sports_list'] ),
								},
								// Competition
								{
									'name': 'competition',
									'title': i18n.__( 'Competition' ),
									'descr': i18n.__( "Select competition to display matches" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_sport_competitions_list'][props.attributes.sport], true ),
								},
								// Round
								{
									'name': 'round',
									'title': i18n.__( 'Round' ),
									'descr': i18n.__( "Select round to display matches" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_sport_rounds_list'][props.attributes.competition], true )
								},
								// Main matches
								{
									'name': 'main_matches',
									'title': i18n.__( 'Main matches' ),
									'descr': i18n.__( "Show large items marked as main match of the round" ),
									'type': 'boolean'
								},
								// Position of the matches list
								{
									'name': 'position',
									'title': i18n.__( 'Position of the matches list' ),
									'descr': i18n.__( "Select the position of the matches list" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_sport_positions'] ),
									'dependency': {
										'main_matches': [true]
									}
								},
								// Slider
								{
									'name': 'slider',
									'title': i18n.__( "Slider" ),
									'descr': i18n.__( "Show main matches as slider (if two and more)" ),
									'type': 'boolean',
									'dependency': {
										'main_matches': [true]
									}
								}
							], 'trx-addons/matches', props ), props )
						),
						'additional_params': el(
							'div', {},
							// Query params
							trx_addons_gutenberg_add_param_query( props ),
							// Title params
							trx_addons_gutenberg_add_param_title( props, true ),
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
		'trx-addons/matches'
	) );
})( window.wp.blocks, window.wp.editor, window.wp.i18n, window.wp.element );
