<?php
/**
 * @var $property
 * @var $show_price
 */
global $post;
$es_property = null;
if ( function_exists( 'es_get_property' ) ){
	$es_property = es_get_property( $property->ID );
}

$post = $property;
setup_postdata($post);
?>
<?php if ( $property ): ?>
    <div class="sow-property-teaser">
        <div class="es-property-thumbnail">
            <a href="<?php the_permalink(); ?>">
				<?php if ( ! empty( $es_property->gallery ) ) : ?>
					<?php es_the_post_thumbnail( 'property_teaser' ); ?>
				<?php elseif ( $image = es_get_default_thumbnail( 'property_teaser' ) ) : ?>
					<?php echo $image; ?>
				<?php endif; ?>
            </a>
        </div>
        <div class="es-property-content">
	        <?php if ( ! empty( $instance['title'] ) )
		        echo $args['before_title'] . esc_html( $instance['title'] ) . $args['after_title'] ?>
			<?php es_the_title( '<h2 class="sub-title">', '</h2>' ); ?>
	        <?php es_the_address( '<div class="es-property-address"><i class="fa fa-map-marker" aria-hidden="true"></i>', '</div>' ); ?>
            <div class="es-property-excerpt">
		        <?php echo wp_trim_words( get_the_content( $property->ID ), 80 ) ?>
            </div>
 			<?php if ( $gallery = $es_property->gallery ) : ?>
                    <div class="teaser-more-info hide">
				            <?php foreach ( $gallery as $value ) : $big_img = wp_get_attachment_image_src(  $value, 'property_teaser' ); ?>
                                <div><a href="<?php echo $big_img[0];?>"><?php echo wp_get_attachment_image( $value, 'thumbnail_140x140' ); ?></a></div>
				            <?php endforeach; ?>
                    </div>
                    <a href="#" class="js-teaser-more theme-button-light"><?php _e( 'Show More', 'es-project' );?></a>
                    <a href="#" class="js-teaser-less theme-button-light hide"><?php _e( 'Hide', 'es-project' );?></a>
	            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?php wp_reset_postdata();
