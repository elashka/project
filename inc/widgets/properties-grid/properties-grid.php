<?php
/*
Widget Name: Theme Properties Grid widget
Description: Widget with grid of properties.
Author: Estatik
Author URI: http://estatik.net
*/

if ( class_exists('SiteOrigin_Widget') ){
	class Properties_Grid_Widget extends SiteOrigin_Widget {
		function __construct() {
			parent::__construct(
				'properties-grid-widget',
				__('Theme Properties Grid Widget', 'es-project'),
				array(
					'description' => __('Widget with  grid of properties.', 'es-project'),
                    'has_preview' => false

				),
				array(
				),
				false,
				ES_PROJECT_DIR
			);
		}

		function get_widget_form(){
			$filters = array();
			$filters = $this->get_filter_fields_data();

			return array(
				'title' => array(
					'type' => 'text',
					'label' => __('Title', 'es-project'),
				),
				'settings' => array(
					'type' => 'section',
					'label' => __( 'Settings', 'es-project' ),
					'fields' => array(
						'show_price' => array(
							'type' => 'checkbox',
							'default' => false,
							'label' => __('Show price', 'es-project'),
						),
					)
				),
				'properties' => array(
					'type' => 'section',
					'label' => __( 'Properties', 'es-project' ),
					'fields' => array(
						'filter_data' => array(
							'type'    => 'select',
							'label'   => '',
							'prompt'  => __( '-- Select parameters for filtering --', 'es-project' ),
							'multiple' => true,
							'options' => $filters
						),
						'prop_id' => array(
							'type' => 'text',
							'label' => __('Listings IDs', 'es-project'),
							'description' => __('The IDs of the listings.', 'es-project'),
						),
						'limit' => array(
							'type' => 'slider',
							'label' => __('Limit', 'es-project'),
							'integer' => true,
							'default' => 4,
							'max' => 8,
							'min' => 1,
						)
					)
				)
			);
		}

		function get_template_variables( $instance, $args ){
			$filters = !empty( $instance['properties'] ) ? $instance['properties'] : array();

			$properties = $this->get_properties( $filters );

			return array(
				'properties' => $properties,
				'show_price' => !empty( $instance['settings']['show_price'] ) ? $instance['settings']['show_price'] : null,
			);
		}

		function get_filter_fields_data() {
			/** @var Es_Settings_Container $es_settings */
			global $es_settings;

			$data = array();
			$filters_data = array();

			if ( ! empty($es_settings) ) {
				$taxonomies = $es_settings::get_setting_values( 'taxonomies' );

				if ( ! empty( $taxonomies ) ) {
					foreach ( $taxonomies as $name => $taxonomy ) {
						if ( taxonomy_exists( $name ) ) {
							$taxonomy = get_taxonomy( $name );
							$data[ $taxonomy->label ] = get_terms( array( 'taxonomy' => $taxonomy->name, 'hide_empty' => false ) );
						}
					}
				}
			}

			foreach ( $data as $group => $items ){
				if ( empty( $items ) ) continue;
				foreach ( $items as $value => $item ){
					if ( $item instanceof WP_Term) {
						$filters_data[$item->term_id] = $item->name;
					}
					else{
						$filters_data[$value] = $item;
					}
				}
			}

			return apply_filters( 'es_project_get_property_grid_fields_data', $filters_data );
		}

		function get_properties( $filters ) {
			$data_filter = array();
			$query_args = array(
				'post_type'      => 'properties',
				'post_status'    => 'publish',
				'posts_per_page' => $filters['limit'],
				'orderby'        => 'post_date',
				'order'          => 'DESC'
			);

			if ( ! empty( $filters['filter_data'] ) ) {
				if ( is_array( $filters['filter_data'] ) ) {
					foreach ( $filters['filter_data'] as $tid ) {
						$term = get_term( $tid );
						if ( $term && strlen( $term->taxonomy ) > 3 ) {
							$data_filter[$term->taxonomy][] = $term->term_id;
						}
					}
				}
				else{
					$term = get_term( $filters['filter_data'] );
					if ( $term && strlen( $term->taxonomy ) > 3 ) {
						$data_filter[$term->taxonomy][] = $term->term_id;
					}
				}
			}

			foreach ( $data_filter as $key => $value ) {
				$tax_name = $key;
				if ( taxonomy_exists( $tax_name ) ) {
					if ( ! empty( $value ) ) {
						if ( $tax_name == 'es_labels' ) {
							$value = $value;

							if ( $value ) {
								foreach ( $value as $name ) {
									$term = get_term_by( 'id', $name, $tax_name );
									$query_args['meta_query'][] = array(
										'compare' => '=',
										'key'     => 'es_property_' . $term->slug,
										'value'   => 1
									);
								}
							}
						} else {
							$query_args['tax_query'][] = array(
								'taxonomy' => $tax_name,
								'field'    => 'id',
								'terms'    => $value
							);
						}
					}
				}
			}

			if ( ! empty( $filters['prop_id'] ) ) {
				if ( is_array( $filters['prop_id'] ) ) {
					$query_args['post__in'] = $filters['prop_id'];
				} else {
					$query_args['post__in'] = array_map( 'trim', explode( ',', $filters['prop_id'] ) );
				}
			}
			if ( ! empty( $query_args ) ) {
				$query = new WP_Query( $query_args );
			}

			return $query;
		}
	}
}

siteorigin_widget_register('properties-grid-widget', __FILE__, 'Properties_Grid_Widget');