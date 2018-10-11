<?php
/**
 * @var $addresses
 * @var $phones
 * @var $emails
 */

?>
<div class="widget__location">
	<div class="info-wrapper<?php echo !empty( $text_padding ) ? ' with-padding': '';?>">
		<?php if( !empty( $instance['title'] ) ):?>
            <h3 class="widget-title-<?php echo $header_size;?> widget-title-<?php echo $header_position;?>"><?php echo esc_html($instance['title']); ?></h3>
        <?php endif;?>
		<?php if ( ! empty( $addresses ) ): ?>
			<div class="widget__location-address">
                <i class="fa fa-map-marker" aria-hidden="true"></i>
				<?php foreach ( $addresses as $address ): ?>
					<p><?php echo $address['address_text']; ?></p>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $phones ) ): ?>
			<div class="widget__location-phone">
                <i class="fa fa-phone" aria-hidden="true"></i>
				<?php foreach ( $phones as $phone ): ?>
                    <p><a href="tel:<?php echo $phone['phone']; ?>"><?php echo $phone['phone']; ?></a></p>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $emails ) ): ?>
			<div class="widget__location-email">
                <i class="fa fa-envelope" aria-hidden="true"></i>
				<?php foreach ( $emails as $email ): ?>
					<p><a href="mailto:<?php echo $email['email']; ?>"><?php echo $email['email']; ?></a></p>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="sow-location-map-canvas"
	     style="height:<?php echo intval( $height ) ?>px;"
	     id="map-canvas-<?php echo esc_attr( $map_id ) ?>"
	     data-options="<?php echo esc_attr( json_encode( $map_data ) ) ?>"
	     data-fallback-image="<?php echo esc_attr( json_encode( $fallback_image_data ) ); ?>">
    </div>
</div>
