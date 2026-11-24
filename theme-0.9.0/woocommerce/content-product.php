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
	<?php
	/* Client 24/11: card text as on monoaurel.com — pale category line, name, one small line, then price and "View the piece" on one row. */
	$dd_terms = get_the_terms( $product->get_id(), 'product_cat' );
	$dd_kick  = '';
	if ( $dd_terms && ! is_wp_error( $dd_terms ) ) {
		usort( $dd_terms, fn( $a, $b ) => $b->parent <=> $a->parent );
		$dd_kick = $dd_terms[0]->name;
	}
	?>
	<div class="dd-card__text">
		<?php if ( $dd_kick ) : ?>
			<p class="dd-card__kicker"><?php echo esc_html( $dd_kick ); ?></p>
		<?php endif; ?>
		<h3 class="woocommerce-loop-product__title"><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
		<p class="dd-card__made"><?php esc_html_e( 'Made to order · Handmade in London', 'designer-designs' ); ?></p>
		<div class="dd-card__foot">
			<span class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
			<a class="dd-card__view" href="<?php echo esc_url( $product->get_permalink() ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View the piece: %s', 'designer-designs' ), $product->get_name() ) ); ?>"><?php esc_html_e( 'View the piece', 'designer-designs' ); ?><span aria-hidden="true">→</span></a>
		</div>
	</div>
</li>
