<?php
/**
 * Product card in loops (client 24/11: clean, no stickers): photo (second photo on hover), name, price, "View the piece".
 *
 * @package designer-designs
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
$dd_gallery = $product->get_gallery_image_ids();
$dd_alt_img = $dd_gallery ? $dd_gallery[0] : 0;
$dd_meta    = $product->get_meta( '_dd_card_meta' );
$dd_sizes   = dd_size_count( $product );
if ( ! $dd_meta && $product->is_type( 'variable' ) ) {
	$dd_attrs = $product->get_variation_attributes();
	foreach ( $dd_attrs as $dd_a => $dd_opts ) {
		if ( false !== strpos( $dd_a, 'colour' ) || false !== strpos( $dd_a, 'color' ) ) {
			/* translators: %d: number of colours */
			$dd_meta = sprintf( _n( '%d colour', '%d colours', count( $dd_opts ), 'designer-designs' ), count( $dd_opts ) );
			if ( $dd_sizes > 1 ) {
				$dd_meta = sprintf( __( '%1$d sizes · %2$s', 'designer-designs' ), $dd_sizes, $dd_meta );
			}
		}
	}
}
?>
<li <?php wc_product_class( 'dd-card', $product ); ?>>
	<div class="dd-card__media">
		<a href="<?php echo esc_url( $product->get_permalink() ); ?>" tabindex="-1" aria-hidden="true">
			<?php echo $product->get_image( 'dd-card', array( 'alt' => '', 'loading' => 'lazy', 'style' => 'view-transition-name:dd-p' . $product->get_id() ) ); // phpcs:ignore ?>
			<?php if ( $dd_alt_img ) : ?>
				<?php echo wp_get_attachment_image( $dd_alt_img, 'dd-card', false, array( 'class' => 'dd-card__alt', 'alt' => '', 'loading' => 'lazy' ) ); ?>
			<?php endif; ?>
		</a>
		<?php if ( $product->is_on_sale() ) : ?>
			<span class="onsale" style="position:absolute;right:12px;top:12px"><?php esc_html_e( 'Offer', 'designer-designs' ); ?></span>
		<?php endif; ?>
	</div>
	<div class="dd-card__row">
		<h3 class="woocommerce-loop-product__title"><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
		<span class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
	</div>
	<a class="dd-card__view" href="<?php echo esc_url( $product->get_permalink() ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View the piece: %s', 'designer-designs' ), $product->get_name() ) ); ?>"><?php esc_html_e( 'View the piece', 'designer-designs' ); ?><span aria-hidden="true">→</span></a>
</li>
