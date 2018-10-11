<?php
/**
 * @var $properties
 * @var $show_price
 */

?>
<?php if ( ! empty( $instance['title'] ) )
	echo $args['before_title'] . esc_html( $instance['title'] ) . $args['after_title'] ?>
<?php if ( $properties->have_posts() ): ?>
    <div class="sow-properties<?php if ($properties->query['posts_per_page'] < 4 ){ echo ' columns-' . $properties->query['posts_per_page']; }?>">
		<?php while ( $properties->have_posts() ): $properties->the_post(); ?>
			<?php
			if ( function_exists( 'es_get_property' ) ):
				$es_property = es_get_property( get_the_ID() );
				$city        = ES_Address_Components::get_property_component( get_the_ID(), 'locality' )->long_name;
				$country     = ES_Address_Components::get_property_component( get_the_ID(), 'country' )->short_name;
				if ( ! empty( $city ) || ( ! empty( $country ) ) ) {
					$address = array(
						$city,
						$country
					);
				}
			?>
            <div id="post-<?php the_ID(); ?>">
                <div class="es-property-inner">
                    <div class="es-property-thumbnail">

                        <a href="<?php the_permalink(); ?>">

							<?php if ( ! empty( $es_property->gallery ) ) : ?>
								<?php es_the_post_thumbnail( 'property-loop' ); ?>
							<?php elseif ( $image = es_get_default_thumbnail( 'property-loop' ) ) : ?>
								<?php echo $image; ?>
							<?php else: ?>
                                <div class="es-thumbnail-none">
									<?php if ( ! $es_property->get_labels_list() ) : ?>
										<?php _e( 'No image', 'es-plugin' ); ?>
									<?php endif; ?>
                                </div>
							<?php endif; ?>
                            <div class="es-thumbnail__container">
								<?php es_the_title( '<h2>', '</h2>' ); ?>
								<?php if ( ! empty( $address ) ): ?>
                                    <p> <i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo implode( ', ', $address ); ?></p>
								<?php endif; ?>
								<?php if ( $show_price ): ?>
									<?php es_the_formatted_price(); ?>
								<?php endif; ?>
                            </div>
                        </a>
                    </div>
                    <div class="es-property-excerpt">
                        <?php echo wp_trim_words( get_the_content(), 18);?>
                    </div>
                </div>
            </div>
            <?php endif;?>
		<?php endwhile; ?>
    </div>
<?php endif; ?>