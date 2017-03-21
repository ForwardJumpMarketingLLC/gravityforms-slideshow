<?php
/**
 * To override this template, create a folder in your (child) theme called 'gf-slideshow' and copy this file into that folder.
 * Ensure that the file name remains the same, otherwise this plugin will not be able to find it.
 *
 * @var $attachments
 * @var $image_size
 * @var $show_captions
 */
?>

<div class="gf-slideshow">
    <div class="slider-for">
		<?php foreach ( $attachments as $attachment ) :
			$image = wp_get_attachment_image( $attachment->ID, $image_size );
			if ( ! $image ) {
				continue;
			} ?>

            <div class="slide">
				<?php echo $image;
				if ( $show_captions && $attachment->post_excerpt ) : ?>
                    <h4 class="slide-caption"><?php echo esc_html( $attachment->post_excerpt ); ?></h4>
				<?php endif; ?>
            </div>

		<?php endforeach; ?>
    </div>

    <div class="slider-nav">
		<?php foreach ( $attachments as $attachment ) :

			$image = wp_get_attachment_image( $attachment->ID, $image_size );
			if ( ! $image ) {
				continue;
			} ?>

            <div class="slide">
				<?php echo $image; ?>
            </div>

		<?php endforeach; ?>
    </div>
</div>
