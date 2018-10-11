<?php
/**
 * @var $properties
 */
?>
<?php if ( ! empty( $instance['title'] ) )
	echo $args['before_title'] . esc_html( $instance['title'] ) . $args['after_title'] ?>
<?php if ( $properties->have_posts() ): ?>
    <div class="horizontal-properties-slider">
    <a href="#" class="slick-prev"></a>
    <div class="sow-properties-slider">

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
								<?php es_the_post_thumbnail( 'property-archive-thm' ); ?>
							<?php elseif ( $image = es_get_default_thumbnail( 'property-archive-thm' ) ) : ?>
								<?php echo $image; ?>
							<?php endif; ?>
                        </a>
                    </div>
                    <div class="es-property-excerpt">
		                    <?php es_the_title( '<h3>', '</h3>' ); ?>
		                    <?php if ( ! empty( $address ) ): ?>
                                <p> <i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo implode( ', ', $address ); ?></p>
		                    <?php endif; ?>
                        <?php echo wp_trim_words( get_the_content(), 18);?>
                        <a class="theme-button-light" href="<?php the_permalink(); ?>"><?php _e( 'Learn More', 'es-project' );?></a>
                    </div>
                </div>
            </div>
            <?php endif;?>
		<?php endwhile; ?>

    </div>
    <a href="#" class="slick-next"></a>
    </div>
<?php endif; ?>