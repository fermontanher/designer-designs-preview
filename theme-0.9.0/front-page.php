<?php
/**
 * Homepage (client review 24/11/2026: "less is more elegant").
 *
 * 1. Opening: a completely black screen with a single point of light. Touching it
 *    draws the gold crack, the dark surface breaks open and the light reveals the first piece.
 * 2. Featured pieces: full-width image slider, one landscape image at a time, no text (as
 *    on the client's cake site). Featured = WooCommerce "featured" products (the star
 *    in Products), in menu order. Rotates every 7s, pauses on hover/focus, swipes on touch.
 * Client 24/11: "five of our best sellers and that's it" — nothing else on the homepage.
 *
 * @package designer-designs
 */

defined( 'ABSPATH' ) || exit;
get_header();

$dd_shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$dd_feat = function_exists( 'wc_get_products' ) ? wc_get_products( array( 'status' => 'publish', 'featured' => true, 'limit' => 6, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ) ) ) : array();
$dd_feat = array_values( array_filter( $dd_feat, fn( $p ) => $p->get_image_id() ) );
if ( ! $dd_feat && function_exists( 'wc_get_products' ) ) {
	$dd_feat = wc_get_products( array( 'status' => 'publish', 'limit' => 5, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
}
?>
<main id="main" class="dd-home">

	<?php /* Opening: black until the visitor lets the light in. The four shards are the dark surface that breaks open. */ ?>
	<div class="dd-intro-curtain" data-dd-curtain data-crack="<?php echo esc_attr( '[[52, -2], [51.68, 4.31], [50.87, 12.51], [51.13, 19.04], [50.81, 24.28], [52.4, 31.3], [50.3, 36.17], [52.18, 41.04], [48.48, 48.31], [51.99, 56.74], [52.03, 63.85], [48.22, 68.98], [46.02, 75.6], [45.76, 80.86], [47.02, 85.48], [50.25, 91.74], [51.06, 98.32], [51.73, 102]]' ); ?>" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" hidden>
		<div class="dd-curtain__flood" aria-hidden="true"></div>
		<?php foreach ( array( 'lt' => 'polygon(-2% -2%, 52% -2%, 51.68% 4.31%, 50.87% 12.51%, 51.13% 19.04%, 50.81% 24.28%, 52.4% 31.3%, 50.3% 36.17%, 52.18% 41.04%, 48.48% 48.31%, 51.99% 56.74%, 42.99% 58.1%, 33.99% 58.85%, 25.0% 63.56%, 16.0% 65.09%, 7.0% 65.95%, -2% 66%)', 'lb' => 'polygon(-2% 66%, 7.0% 65.95%, 16.0% 65.09%, 25.0% 63.56%, 33.99% 58.85%, 42.99% 58.1%, 51.99% 56.74%, 52.03% 63.85%, 48.22% 68.98%, 46.02% 75.6%, 45.76% 80.86%, 47.02% 85.48%, 50.25% 91.74%, 51.06% 98.32%, 51.73% 102%, -2% 102%)', 'rt' => 'polygon(52% -2%, 51.68% 4.31%, 50.87% 12.51%, 51.13% 19.04%, 50.81% 24.28%, 52.4% 31.3%, 50.3% 36.17%, 58.92% 35.72%, 67.53% 32.63%, 76.15% 30.9%, 84.77% 29.8%, 93.38% 27.47%, 102% 28%, 102% -2%)', 'rb' => 'polygon(102% 28%, 93.38% 27.47%, 84.77% 29.8%, 76.15% 30.9%, 67.53% 32.63%, 58.92% 35.72%, 50.3% 36.17%, 52.18% 41.04%, 48.48% 48.31%, 51.99% 56.74%, 52.03% 63.85%, 48.22% 68.98%, 46.02% 75.6%, 45.76% 80.86%, 47.02% 85.48%, 50.25% 91.74%, 51.06% 98.32%, 51.73% 102%, 102% 102%)' ) as $dd_side => $dd_clip ) : ?>
			<div class="dd-curtain__half dd-curtain__shard dd-curtain__shard--<?php echo esc_attr( $dd_side ); ?>" style="clip-path:<?php echo esc_attr( $dd_clip ); ?>" aria-hidden="true"></div>
		<?php endforeach; ?>
		<div class="dd-curtain__seamwrap" aria-hidden="true">
			<div class="dd-curtain__seam dd-curtain__seam--c" style="clip-path:polygon(51.83% -2%, 51.51% 4.31%, 50.699999999999996% 12.51%, 50.96% 19.04%, 50.64% 24.28%, 52.23% 31.3%, 50.129999999999995% 36.17%, 52.01% 41.04%, 48.309999999999995% 48.31%, 51.82% 56.74%, 51.86% 63.85%, 48.05% 68.98%, 45.85% 75.6%, 45.589999999999996% 80.86%, 46.85% 85.48%, 50.08% 91.74%, 50.89% 98.32%, 51.559999999999995% 102%, 51.9% 102%, 51.230000000000004% 98.32%, 50.42% 91.74%, 47.190000000000005% 85.48%, 45.93% 80.86%, 46.190000000000005% 75.6%, 48.39% 68.98%, 52.2% 63.85%, 52.160000000000004% 56.74%, 48.65% 48.31%, 52.35% 41.04%, 50.47% 36.17%, 52.57% 31.3%, 50.980000000000004% 24.28%, 51.300000000000004% 19.04%, 51.04% 12.51%, 51.85% 4.31%, 52.17% -2%)"></div>
			<div class="dd-curtain__seam dd-curtain__seam--b" style="clip-path:polygon(51.99% 56.580000000000005%, 42.99% 57.940000000000005%, 33.99% 58.690000000000005%, 25.0% 63.400000000000006%, 16.0% 64.93%, 7.0% 65.79%, -2% 65.84%, -2% 66.16%, 7.0% 66.11%, 16.0% 65.25%, 25.0% 63.72%, 33.99% 59.01%, 42.99% 58.26%, 51.99% 56.9%)"></div>
			<div class="dd-curtain__seam dd-curtain__seam--b" style="clip-path:polygon(50.3% 36.010000000000005%, 58.92% 35.56%, 67.53% 32.470000000000006%, 76.15% 30.74%, 84.77% 29.64%, 93.38% 27.31%, 102% 27.84%, 102% 28.16%, 93.38% 27.63%, 84.77% 29.96%, 76.15% 31.06%, 67.53% 32.79%, 58.92% 35.879999999999995%, 50.3% 36.33%)"></div>
		</div>
		<div class="dd-curtain__spark" aria-hidden="true"></div>
		<div class="dd-curtain__ui">
			<p class="dd-whisper" data-dd-whisper><?php esc_html_e( 'Every piece begins in the dark.', 'designer-designs' ); ?></p>
			<button type="button" class="dd-ember" data-dd-curtain-open>
				<span class="dd-ember__light" aria-hidden="true"></span>
				<span class="dd-ember__label"><?php esc_html_e( 'Let the light in', 'designer-designs' ); ?></span>
			</button>
		</div>
		<button type="button" class="dd-curtain__skip" data-dd-curtain-skip><?php esc_html_e( 'Enter', 'designer-designs' ); ?></button>
	</div>

	<?php if ( $dd_feat ) : ?>
	<?php /* Client 24/11 (video): image slider like designercakesoflondon.co.uk. One landscape image at a time, no text. Dark sides, arrows, dots. */ ?>
	<section class="dd-feat" data-dd-hero data-dd-feat aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Featured pieces', 'designer-designs' ); ?>">
		<h1 class="screen-reader-text"><?php echo esc_html( get_bloginfo( 'name' ) . ' – ' . get_bloginfo( 'description' ) ); ?></h1>
		<div class="dd-feat__stage">
			<?php foreach ( $dd_feat as $dd_i => $dd_p ) : ?>
				<?php $dd_src = wp_get_attachment_image_url( $dd_p->get_image_id(), 'medium' ); ?>
				<a class="dd-feat__slide<?php echo 0 === $dd_i ? ' is-current' : ''; ?>" href="<?php echo esc_url( $dd_p->get_permalink() ); ?>" style="--dd-bg:url('<?php echo esc_url( $dd_src ); ?>')" data-dd-slide="<?php echo (int) $dd_i; ?>" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( sprintf( __( '%1$d of %2$d: %3$s', 'designer-designs' ), $dd_i + 1, count( $dd_feat ), $dd_p->get_name() ) ); ?>" <?php echo 0 === $dd_i ? '' : 'aria-hidden="true" tabindex="-1"'; ?>>
					<?php echo wp_get_attachment_image( $dd_p->get_image_id(), 'full', false, array( 'alt' => $dd_p->get_name(), 'sizes' => '100vw', 'loading' => 0 === $dd_i ? 'eager' : 'lazy', 'fetchpriority' => 0 === $dd_i ? 'high' : 'auto' ) ); ?>
				</a>
			<?php endforeach; ?>
			<?php if ( count( $dd_feat ) > 1 ) : ?>
				<button type="button" class="dd-feat__arrow dd-feat__arrow--prev" data-dd-prev aria-label="<?php esc_attr_e( 'Previous piece', 'designer-designs' ); ?>"><?php echo dd_icon( 'left', 22 ); // phpcs:ignore ?></button>
				<button type="button" class="dd-feat__arrow dd-feat__arrow--next" data-dd-next aria-label="<?php esc_attr_e( 'Next piece', 'designer-designs' ); ?>"><?php echo dd_icon( 'right', 22 ); // phpcs:ignore ?></button>
				<div class="dd-feat__dots" role="tablist" aria-label="<?php esc_attr_e( 'Choose a piece', 'designer-designs' ); ?>">
					<?php foreach ( $dd_feat as $dd_i => $dd_p ) : ?>
						<button type="button" data-dd-go="<?php echo (int) $dd_i; ?>" aria-current="<?php echo 0 === $dd_i ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( $dd_p->get_name() ); ?>"></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php endif; ?>

</main>
<?php
get_footer();
