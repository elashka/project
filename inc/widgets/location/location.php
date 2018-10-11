<?php
/*
Widget Name: Theme Location widget
Description: Widget with contact info and map.
Author: Estatik
Author URI: http://estatik.net
*/

if ( class_exists('SiteOrigin_Widget') ){
	class Location_Widget extends SiteOrigin_Widget {
		function __construct() {
			parent::__construct(
				'location-widget',
				__('Theme Location Widget', 'es-project'),
				array(
					'description' => __('Widget with contact info and map.', 'es-project'),
                    'has_preview' => false
				),
				array(
				),
				false,
				ES_PROJECT_DIR
			);
		}

		function get_widget_form(){
			return array(
				'title' => array(
					'type' => 'text',
					'label' => __('Title', 'es-project'),
				),
				'addresses' => array(
					'type' => 'repeater',
					'label' => __( 'Addresses', 'es-project' ),
					'item_name'  => __( 'Address', 'es-project' ),
					'item_label' => array(
						'selector'     => "[id*='address-name']",
						'update_event' => 'change',
						'value_method' => 'val'
					),
					'fields' => array(
						'address_text' => array(
							'type' => 'text',
							'label' => __('Address', 'es-project'),
							'description' => __('The address of the location.', 'es-project'),
						),
					)
				),
				'phones' => array(
					'type' => 'repeater',
					'label' => __( 'Phones', 'es-project' ),
					'item_name'  => __( 'Phone', 'es-project' ),
					'item_label' => array(
						'selector'     => "[id*='phone-name']",
						'update_event' => 'change',
						'value_method' => 'val'
					),
					'fields' => array(
						'phone' => array(
							'type' => 'text',
							'label' => __('Phone', 'es-project'),
							'description' => __('The contact phone.', 'es-project'),
						),
					)
				),
				'emails' => array(
					'type' => 'repeater',
					'label' => __( 'Emails', 'es-project' ),
					'item_name'  => __( 'Email', 'es-project' ),
					'item_label' => array(
						'selector'     => "[id*='email-name']",
						'update_event' => 'change',
						'value_method' => 'val'
					),
					'fields' => array(
						'email' => array(
							'type' => 'text',
							'label' => __('Email', 'es-project'),
							'description' => __('The contact email.', 'es-project'),
						),
					)
				),
				'api_key_section' => array(
					'type'   => 'section',
					'label'  => __( 'API key', 'es-project' ),
					'hide'   => false,
					'fields' => array(
						'api_key' => array(
							'type'        => 'text',
							'label'       => __( 'API key', 'es-project' ),
							'required'    => true,
							'description' => sprintf(
								__( 'Enter your %sAPI key%s. Your map may not function correctly without one.', 'es-project' ),
								'<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">',
								'</a>'
							)
						)
					)
				),
				'header_settings' => array(
					'type'        => 'section',
					'label'       => __( 'Header Settings', 'es-project' ),
					'hide'        => FALSE,
					'description' => __( 'Set header display options.', 'es-project' ),
					'fields'      => array(
						'header_size'     => array(
							'type'     => 'select',
							'label'    => '',
							'multiple' => FALSE,
							'default'  => 'small',
							'options'  => array(
								'small' => __( 'Small', 'es-project' ),
								'large' => __( 'Large', 'es-project' )
							)
						),
						'header_position' => array(
							'type'     => 'select',
							'label'    => '',
							'multiple' => FALSE,
							'default'  => 'left',
							'options'  => array(
								'left'   => __( 'Left', 'es-project' ),
								'center' => __( 'Center', 'es-project' )
							)
						),
						'text_padding'    => array(
							'type'    => 'checkbox',
							'default' => FALSE,
							'label'   => __( 'Add padding to the text block', 'es-project' ),
							'hidden'  => TRUE
						),
					)
				),
				'settings'        => array(
					'type'        => 'section',
					'label'       => __( 'Settings', 'es-project' ),
					'hide'        => false,
					'description' => __( 'Set map display options.', 'es-project' ),
					'fields'      => array(
						'height'      => array(
							'type'    => 'text',
							'default' => 480,
							'label'   => __( 'Height', 'es-project' )
						),
						'destination_url' => array(
							'type' => 'link',
							'label' => __( 'Destination URL', 'es-project' ),
							'hidden'     => true,
							'state_handler' => array(
								'map_type[static]' => array('show'),
								'_else[map_type]' => array('hide'),
							),
						),
						'new_window' => array(
							'type' => 'checkbox',
							'default' => false,
							'label' => __( 'Open in a new window', 'es-project' ),
							'hidden'     => true,
							'state_handler' => array(
								'map_type[static]' => array('show'),
								'_else[map_type]' => array('hide'),
							),
						),
						'zoom'        => array(
							'type'        => 'slider',
							'label'       => __( 'Zoom level', 'es-project' ),
							'description' => __( 'A value from 0 (the world) to 21 (street level).', 'es-project' ),
							'min'         => 0,
							'max'         => 21,
							'default'     => 12,
							'integer'     => true,

						),
						'scroll_zoom' => array(
							'type'        => 'checkbox',
							'default'     => true,
							'state_handler' => array(
								'map_type[interactive]' => array('show'),
								'_else[map_type]' => array('hide'),
							),
							'label'       => __( 'Scroll to zoom', 'es-project' ),
							'description' => __( 'Allow scrolling over the map to zoom in or out.', 'es-project' )
						),
						'draggable'   => array(
							'type'        => 'checkbox',
							'default'     => true,
							'state_handler' => array(
								'map_type[interactive]' => array('show'),
								'_else[map_type]' => array('hide'),
							),
							'label'       => __( 'Draggable', 'es-project' ),
							'description' => __( 'Allow dragging the map to move it around.', 'es-project' )
						),
						'disable_default_ui' => array(
							'type' => 'checkbox',
							'default' => false,
							'state_handler' => array(
								'map_type[interactive]' => array('show'),
								'_else[map_type]' => array('hide'),
							),
							'label'       => __( 'Disable default UI', 'es-project' ),
							'description' => __( 'Hides the default Google Maps controls.', 'es-project' )
						),
						'keep_centered' => array(
							'type' => 'checkbox',
							'default' => false,
							'state_handler' => array(
								'map_type[interactive]' => array('show'),
								'_else[map_type]' => array('hide'),
							),
							'label'       => __( 'Keep map centered', 'es-project' ),
							'description' => __( 'Keeps the map centered when it\'s container is resized.', 'es-project' )
						),
						'fallback_image' => array(
							'type' => 'media',
							'label' => __( 'Fallback Image', 'es-project' ),
							'description' => __( 'This image will be displayed if there are any problems with displaying the specified map.', 'es-project' ),
							'library' => 'image',
						),
						'fallback_image_size' => array(
							'type' => 'image-size',
							'label' => __( 'Fallback Image Size', 'es-project' ),
						),
					)
				),
				'markers'         => array(
					'type'        => 'section',
					'label'       => __( 'Markers', 'es-project' ),
					'hide'        => true,
					'description' => __( 'Use markers to identify points of interest on the map.', 'es-project' ),
					'fields'      => array(
						'marker_at_center'  => array(
							'type'    => 'checkbox',
							'default' => true,
							'label'   => __( 'Show marker at map center', 'es-project' )
						),
						'marker_icon'       => array(
							'type'        => 'media',
							'default'     => '',
							'label'       => __( 'Marker icon', 'es-project' ),
							'description' => __( 'Replaces the default map marker with your own image.', 'es-project' )
						),
						'markers_draggable' => array(
							'type'       => 'checkbox',
							'default'    => false,
							'state_handler' => array(
								'map_type[interactive]' => array('show'),
								'_else[map_type]' => array('hide'),
							),
							'label'      => __( 'Draggable markers', 'es-project' )
						),
						'info_display' => array(
							'type' => 'radio',
							'label' => __( 'When should Info Windows be displayed?', 'es-project' ),
							'default' => 'click',
							'options' => array(
								'click'   => __( 'Click', 'es-project' ),
								'mouseover'   => __( 'Mouse over', 'es-project' ),
								'always' => __( 'Always', 'es-project' ),
							)
						),
						'info_multiple' => array(
							'type' => 'checkbox',
							'label' => __( 'Allow multiple simultaneous Info Windows?', 'es-project' ),
							'default' => true,
							'description' => __( 'This setting is ignored when Info Windows are set to always display.' )
						),
					)
				),
			);
		}

		function get_template_variables( $instance, $args ) {
			if ( empty( $instance ) ) {
				return array();
			}

			$settings = $instance['settings'];

			$mrkr_src = wp_get_attachment_image_src( $instance['markers']['marker_icon'], 'map_icon' );

			$fallback_image = '';
			if ( ! empty ( $instance['settings']['fallback_image'] ) ) {
				$fallback_image = siteorigin_widgets_get_attachment_image(
					$instance['settings']['fallback_image'],
					$instance['settings']['fallback_image_size'],
					FALSE );
			}
			$markers    = $instance['markers'];

			if ( !empty( $instance['addresses'] ) ){
				$i = 0;
				foreach ($instance['addresses'] as $address) {
					$markerpos[$i]['place'] = $address['address_text'];
					$markerpos[$i]['info'] = $address['address_text'];
					$i++;
				}
			}

			$map_data = siteorigin_widgets_underscores_to_camel_case( array(
				'address'              => $instance['addresses'][0]['address_text'],
				'zoom'                 => $settings['zoom'],
				'scroll_zoom'          => $settings['scroll_zoom'],
				'draggable'            => $settings['draggable'],
				'disable_ui'           => $settings['disable_default_ui'],
				'keep_centered'        => $settings['keep_centered'],
				'marker_icon'          => ! empty( $mrkr_src ) ? $mrkr_src[0] : ES_PROJECT_WIDGETS_URL . 'location/images/map_default_icon.png',
				'markers_draggable'    => isset( $markers['markers_draggable'] ) ? $markers['markers_draggable'] : '',
				'marker_at_center'     => ! empty( $markers['marker_at_center'] ),
				'marker_info_display'  => $markers['info_display'],
				'marker_info_multiple' => $markers['info_multiple'],
				'marker_positions'     => ! empty( $markerpos ) ? $markerpos : '',
				'api_key'              => $instance['api_key_section']['api_key'],
			) );

			return array(
				'map_id'              => md5( $instance['map_center'] ),
				'height'              => $settings['height'],
				'map_data'            => $map_data,
				'fallback_image_data' => array( 'img' => $fallback_image ),
				'addresses' => ! empty( $instance['addresses'] ) ? $instance['addresses'] : array(),
				'phones'    => ! empty( $instance['phones'] ) ? $instance['phones'] : array(),
				'emails'    => ! empty( $instance['emails'] ) ? $instance['emails'] : array(),
				'text_padding'    => ! empty( $instance['header_settings']['text_padding'] ) ? $instance['header_settings']['text_padding'] : false,
				'header_size'    => ! empty( $instance['header_settings']['header_size'] ) ? $instance['header_settings']['header_size'] : 'small',
				'header_position'    => ! empty( $instance['header_settings']['header_position'] ) ? $instance['header_settings']['header_position'] : 'left',
			);
		}

		function initialize(){

			$this->register_frontend_scripts( array(
				array(
					'sow-location',
					ES_PROJECT_WIDGETS_URL . 'location/js/script.js',
					array( 'jquery' )
				)
			) );

			wp_localize_script(
				'sow-location',
				'soWidgetsLocationGoogleMap',
				array(
					'geocode' => array(
						'noResults' => __( 'There were no results for the place you entered. Please try another.', 'so-widgets-bundle' ),
					),
				)
			);

		}
	}
}

siteorigin_widget_register('location-widget', __FILE__, 'Location_Widget');