<?php
/*
Widget Name: Theme Property Teaser widget
Description: Widget with teaser of one property.
Author: Estatik
Author URI: http://estatik.net
*/

if ( class_exists('SiteOrigin_Widget') ){
	class Property_Teaser_Widget extends SiteOrigin_Widget {
		function __construct() {
			parent::__construct(
				'property-teaser-widget',
				__('Theme Property Teaser Widget', 'es-project'),
				array(
					'description' => __('Widget with teaser of one property.', 'es-project'),
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
				'settings' => array(
					'type' => 'section',
					'label' => __( 'Settings', 'es-project' ),
					'fields' => array(
						'property_id' => array(
							'type'    => 'text',
							'label'   => '',
							'prompt'  => __( 'Insert Property ID', 'es-project' ),
						)
					)
				)
			);
		}

		function get_template_variables( $instance, $args ){
			$property = null;
			$property_id = !empty( $instance['settings']['property_id'] ) ? $instance['settings']['property_id'] : array();

			if( $property_id ) {
				$property = get_post( $property_id );
			}

			return array(
				'property' => $property,
			);
		}

		function initialize(){

			$this->register_frontend_scripts( array(
				array(
					'sow-gallery',
					ES_PROJECT_WIDGETS_URL . 'property-teaser/js/scripts.js',
					array( 'jquery' ),
					null,
					true
				)
			) );
		}
	}
}

siteorigin_widget_register('property-teaser-widget', __FILE__, 'Property_Teaser_Widget');