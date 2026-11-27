<?php
/**
 * Homepage (client review 24/11/2026: "less is more elegant").
 *
 * 1. Opening: a completely black screen with a single point of light. Touching it
 *    draws the gold crack, the dark surface breaks open and the light reveals the first piece.
 * 2. Featured pieces: full-width image slider, one landscape image at a time, no text (as
 *    on the client's cake site). Featured = WooCommerce "featured" products (the star
 *    in Products), in menu order. Rotates every 7s, pauses on hover/focus, swipes on touch.
 * Client 24/11: "five of our best sellers and that's it" — full-width slider, then one row "Shop by category" (as on Cox & Cox), then the footer.
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

	<?php /* Opening (client 26/11): completely black with only the light switch. Switching it on starts the gold crack, which breaks open to reveal the banner. */ ?>
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
		<?php /* Client 26/11: all black, just the light switch and its words. Nothing else. The crack starts once the light is switched on. */ ?>
		<div class="dd-ws">
			<button type="button" class="dd-ws__switch" data-dd-curtain-open aria-label="<?php esc_attr_e( 'Switch the lights on and enter the site', 'designer-designs' ); ?>">
				<span class="dd-ws__plate" aria-hidden="true"><span class="dd-ws__rocker"><span class="dd-ws__face"><span class="dd-ws__dot"></span></span></span></span>
				<span class="dd-ws__bloom" aria-hidden="true"></span>
			</button>
			<p class="dd-ws__label" aria-hidden="true"><?php esc_html_e( 'Switch The Lights On', 'designer-designs' ); ?></p>
		</div>
	</div>

	<?php if ( $dd_feat ) : ?>
	<?php /* Client 24/11 (video): image slider like designercakesoflondon.co.uk. One landscape image at a time, no text. Dark sides, arrows, dots. */ ?>
	<section class="dd-feat" data-dd-hero data-dd-feat aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Featured pieces', 'designer-designs' ); ?>">
		<h1 class="screen-reader-text"><?php echo esc_html( get_bloginfo( 'name' ) . ' – ' . get_bloginfo( 'description' ) ); ?></h1>
		<div class="dd-feat__stage">
			<?php foreach ( $dd_feat as $dd_i => $dd_p ) : ?>
				<?php
				$dd_wide = (int) $dd_p->get_meta( '_dd_hero_wide' ); // Full-width image for desktop (expanded, product untouched).
				$dd_img  = $dd_p->get_image_id();
				?>
				<a class="dd-feat__slide<?php echo 0 === $dd_i ? ' is-current' : ''; ?>" href="<?php echo esc_url( $dd_p->get_permalink() ); ?>" data-dd-slide="<?php echo (int) $dd_i; ?>" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( sprintf( __( '%1$d of %2$d: %3$s', 'designer-designs' ), $dd_i + 1, count( $dd_feat ), $dd_p->get_name() ) ); ?>" <?php echo 0 === $dd_i ? '' : 'aria-hidden="true" tabindex="-1"'; ?>>
					<picture>
						<?php if ( $dd_wide ) : ?>
							<source media="(min-width: 901px)" srcset="<?php echo esc_attr( wp_get_attachment_image_srcset( $dd_wide, 'full' ) ?: wp_get_attachment_image_url( $dd_wide, 'full' ) ); ?>" sizes="100vw">
						<?php endif; ?>
						<?php echo wp_get_attachment_image( $dd_img, 'full', false, array( 'alt' => $dd_p->get_name(), 'sizes' => '100vw', 'loading' => 0 === $dd_i ? 'eager' : 'lazy', 'fetchpriority' => 0 === $dd_i ? 'high' : 'auto' ) ); ?>
					</picture>
				</a>
			<?php endforeach; ?>
			<div class="dd-feat__light" aria-hidden="true"></div>
			<button type="button" class="dd-feat__switch" data-dd-switch aria-pressed="false" data-on="<?php esc_attr_e( 'Lights on', 'designer-designs' ); ?>" data-off="<?php esc_attr_e( 'Lights off', 'designer-designs' ); ?>" hidden><?php echo dd_icon( 'lamp', 18 ); // phpcs:ignore ?><span><?php esc_html_e( 'Lights on', 'designer-designs' ); ?></span></button>
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

	<?php /* Home 1 · promise strip, right under the banner. */ ?>
	<section class="dd-promise-strip dd-rv" aria-label="<?php esc_attr_e( 'Our promise', 'designer-designs' ); ?>">
		<ul class="dd-wrap">
			<li><?php esc_html_e( 'Handmade in London', 'designer-designs' ); ?></li>
			<li><?php esc_html_e( 'Free UK delivery', 'designer-designs' ); ?></li>
			<li><?php esc_html_e( 'Love It Guarantee', 'designer-designs' ); ?></li>
			<li><?php esc_html_e( 'Care kit included', 'designer-designs' ); ?></li>
		</ul>
	</section>

	<?php
	/* Client 24/11 (video 7:50, Cox & Cox): after the banner, "Shop by category" as a quiet carousel. Soon categories stay visible, not clickable. */
	$dd_cats = dd_nav_categories();
	?>
	<?php if ( $dd_cats ) : ?>
	<section class="dd-shopcat" aria-labelledby="dd-shopcat-title" data-dd-rail>
		<div class="dd-wrap dd-shopcat__head">
			<h2 id="dd-shopcat-title" class="dd-shopcat__title"><?php echo wp_kses( __( 'Shop by <em>category</em>', 'designer-designs' ), array( 'em' => array() ) ); ?></h2>
			<div class="dd-shopcat__ctrl">
				<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
					<a class="dd-shopcat__all" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'All pieces', 'designer-designs' ); ?></a>
				<?php endif; ?>
				<button type="button" class="dd-shopcat__arrow" data-dd-rail-prev aria-label="<?php esc_attr_e( 'Previous categories', 'designer-designs' ); ?>" aria-controls="dd-shopcat-row" disabled>
					<svg width="10" height="16" viewBox="0 0 10 16" aria-hidden="true"><path d="M8.5 1.5 2 8l6.5 6.5" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
				</button>
				<button type="button" class="dd-shopcat__arrow" data-dd-rail-next aria-label="<?php esc_attr_e( 'Next categories', 'designer-designs' ); ?>" aria-controls="dd-shopcat-row">
					<svg width="10" height="16" viewBox="0 0 10 16" aria-hidden="true"><path d="M1.5 1.5 8 8l-6.5 6.5" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
				</button>
			</div>
		</div>
		<ul class="dd-shopcat__row" id="dd-shopcat-row" data-dd-rail-track>
			<?php foreach ( $dd_cats as $dd_cat ) : ?>
				<?php $dd_is_soon = dd_category_soon( $dd_cat ); ?>
				<li class="dd-shopcat__item<?php echo $dd_is_soon ? ' is-soon' : ''; ?>">
					<?php if ( $dd_is_soon ) : ?>
						<div class="dd-shopcat__card" aria-disabled="true">
							<span class="dd-shopcat__media"><img src="<?php echo esc_url( dd_category_image( $dd_cat, 'large' ) ); ?>" alt="" loading="lazy" width="600" height="600"><em class="dd-shopcat__soon"><?php esc_html_e( 'Coming soon', 'designer-designs' ); ?></em></span>
							<span class="dd-shopcat__name"><?php echo esc_html( $dd_cat->name ); ?></span>
						</div>
					<?php else : ?>
						<a class="dd-shopcat__card" href="<?php echo esc_url( get_term_link( $dd_cat ) ); ?>">
							<span class="dd-shopcat__media"><img src="<?php echo esc_url( dd_category_image( $dd_cat, 'large' ) ); ?>" alt="" loading="lazy" width="600" height="600"></span>
							<span class="dd-shopcat__name"><?php echo esc_html( $dd_cat->name ); ?></span>
						</a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php endif; ?>

	<?php
	/* Client ref (Cox & Cox): three tall editorial tiles, each a mood and one way in. Image = media ID, link = category slug. */
	$dd_edit = array(
		array( 'img' => 675, 'cat' => 'lighting',   'title' => __( 'Sculpted light', 'designer-designs' ),   'sub' => __( 'Lamps shaped by hand in London', 'designer-designs' ) ),
		array( 'img' => 889, 'cat' => 'sculptures', 'title' => __( 'Quiet forms', 'designer-designs' ),      'sub' => __( 'Totems that hold a room', 'designer-designs' ) ),
		array( 'img' => 921, 'cat' => 'wall-art',   'title' => __( 'Art for the wall', 'designer-designs' ), 'sub' => __( 'Textured discs, made to order', 'designer-designs' ) ),
	);
	$dd_edit = array_filter( $dd_edit, function ( $t ) {
		$term = get_term_by( 'slug', $t['cat'], 'product_cat' );
		return $term && ! dd_category_soon( $term ) && wp_get_attachment_image_url( $t['img'], 'large' );
	} );
	?>
	<?php if ( $dd_edit ) : ?>
	<section class="dd-wrap dd-edit" aria-label="<?php esc_attr_e( 'Featured collections', 'designer-designs' ); ?>">
		<ul class="dd-edit__row">
			<?php foreach ( $dd_edit as $dd_t ) : ?>
				<?php $dd_link = get_term_link( $dd_t['cat'], 'product_cat' ); ?>
				<li class="dd-edit__tile">
					<a href="<?php echo esc_url( $dd_link ); ?>">
						<?php echo wp_get_attachment_image( $dd_t['img'], 'large', false, array( 'class' => 'dd-edit__img', 'alt' => '', 'loading' => 'lazy', 'sizes' => '(max-width:900px) 80vw, 33vw' ) ); ?>
						<span class="dd-edit__copy">
							<strong class="dd-edit__title"><?php echo esc_html( $dd_t['title'] ); ?></strong>
							<span class="dd-edit__sub"><?php echo esc_html( $dd_t['sub'] ); ?></span>
							<span class="dd-edit__btn"><?php esc_html_e( 'Shop now', 'designer-designs' ); ?></span>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php endif; ?>

	<?php
	/* Home 5 · piece of the month (Customizer → Designer Designs → Home sections). Hidden when the product is not published. */
	$dd_potm = function_exists( 'wc_get_product' ) ? wc_get_product( (int) get_theme_mod( 'dd_potm', 672 ) ) : null;
	if ( $dd_potm && 'publish' === $dd_potm->get_status() ) :
		$dd_potm_img  = (int) get_post_meta( $dd_potm->get_id(), '_dd_hero_wide', true );
		$dd_potm_img  = $dd_potm_img ? $dd_potm_img : $dd_potm->get_image_id();
		$dd_potm_text = get_theme_mod( 'dd_potm_text', '' );
		$dd_potm_text = $dd_potm_text ? $dd_potm_text : wp_strip_all_tags( $dd_potm->get_short_description() );
		$dd_potm_cats = wc_get_product_terms( $dd_potm->get_id(), 'product_cat' );
		?>
	<section class="dd-potm" aria-labelledby="dd-potm-title">
		<div class="dd-potm__media dd-rv dd-rv--clip"><?php echo wp_get_attachment_image( $dd_potm_img, 'full', false, array( 'alt' => '', 'loading' => 'lazy', 'sizes' => '(max-width:900px) 100vw, 60vw' ) ); ?></div>
		<div class="dd-potm__copy">
			<p class="dd-kicker dd-rv"><?php esc_html_e( 'Piece of the month', 'designer-designs' ); ?></p>
			<h2 id="dd-potm-title" class="dd-potm__title dd-rv"><?php echo esc_html( $dd_potm->get_name() ); ?></h2>
			<?php if ( $dd_potm_cats ) : ?><p class="dd-potm__meta dd-rv"><?php echo esc_html( $dd_potm_cats[0]->name ); ?> · <?php echo wp_kses_post( $dd_potm->get_price_html() ); ?></p><?php endif; ?>
			<?php if ( $dd_potm_text ) : ?><p class="dd-potm__text dd-rv"><?php echo esc_html( implode( ' ', array_slice( preg_split( '/(?<=[.!?])\s+/', $dd_potm_text ), 0, 2 ) ) ); ?></p><?php endif; ?>
			<a class="dd-line-link dd-rv" href="<?php echo esc_url( $dd_potm->get_permalink() ); ?>"><?php esc_html_e( 'View the piece', 'designer-designs' ); ?></a>
		</div>
	</section>
	<?php endif; ?>

	<?php /* Home 2 · from the studio. Copy = Materials & process text already on the site. */ ?>
	<section class="dd-wrap dd-studio" aria-labelledby="dd-studio-title">
		<div class="dd-studio__media dd-rv dd-rv--clip"><?php echo wp_get_attachment_image( (int) get_theme_mod( 'dd_studio_img', 768 ), 'large', false, array( 'alt' => '', 'loading' => 'lazy', 'sizes' => '(max-width:900px) 100vw, 45vw' ) ); ?></div>
		<div class="dd-studio__copy">
			<p class="dd-kicker dd-rv"><?php esc_html_e( 'From the studio', 'designer-designs' ); ?></p>
			<h2 id="dd-studio-title" class="dd-studio__title dd-rv"><?php echo wp_kses( __( 'Paper, clay <em>and patience</em>', 'designer-designs' ), array( 'em' => array() ) ); ?></h2>
			<p class="dd-studio__text dd-rv"><?php esc_html_e( 'Every piece starts with a carefully shaped form, built up in layers of organic paper and our own handmade natural clay blend. Then we texture and paint it by hand in our London studio, so no two pieces are ever quite the same.', 'designer-designs' ); ?></p>
			<?php $dd_story = dd_page_url( 'our-story' ) ? dd_page_url( 'our-story' ) : dd_page_url( 'materials' ); ?>
			<?php if ( $dd_story ) : ?><a class="dd-line-link dd-rv" href="<?php echo esc_url( $dd_story ); ?>"><?php esc_html_e( 'Our story', 'designer-designs' ); ?></a><?php endif; ?>
		</div>
	</section>

	<?php /* Home 3 · made to order, three steps. */ ?>
	<section class="dd-wrap dd-steps" aria-labelledby="dd-steps-title">
		<h2 id="dd-steps-title" class="dd-steps__title dd-rv"><?php echo wp_kses( __( 'Made for you, <em>not for a shelf</em>', 'designer-designs' ), array( 'em' => array() ) ); ?></h2>
		<ol class="dd-steps__row">
			<li class="dd-rv"><span class="dd-steps__num"><svg class="dd-steps__icon" viewBox="0 0 48 48" width="52" height="52" aria-hidden="true" focusable="false"><path pathLength="1" d="M19 6h10"/><path pathLength="1" d="M20.5 6v3.5c0 2-9.5 6-9.5 16.5 0 7.5 4 12.5 7 16h12c3-3.5 7-8.5 7-16 0-10.5-9.5-14.5-9.5-16.5V6"/><path pathLength="1" d="M15 42h18"/><path pathLength="1" d="M14.5 26c6 2.5 13 2.5 19 0"/></svg></span><strong><?php esc_html_e( 'You choose', 'designer-designs' ); ?></strong><span><?php esc_html_e( 'Your piece, size and finish', 'designer-designs' ); ?></span></li>
			<li class="dd-rv"><span class="dd-steps__num"><svg class="dd-steps__icon" viewBox="0 0 48 48" width="52" height="52" aria-hidden="true" focusable="false"><path pathLength="1" d="M40 8 28.5 19.5"/><path pathLength="1" d="M26 17l5 5-3.5 3.5-5-5z"/><path pathLength="1" d="M22.5 20.5c-3.5-.5-7 1.5-8.5 5.5S11.5 34 8 38c4.5.8 10-.3 13.6-3.9S27.9 28 27.5 25.5"/></svg></span><strong><?php esc_html_e( 'Shaped by hand', 'designer-designs' ); ?></strong><span><?php esc_html_e( 'Formed, textured and painted in London', 'designer-designs' ); ?></span></li>
			<li class="dd-rv"><span class="dd-steps__num"><svg class="dd-steps__icon" viewBox="0 0 48 48" width="52" height="52" aria-hidden="true" focusable="false"><path pathLength="1" d="M10 20h28v20H10z"/><path pathLength="1" d="M8 14h32v6H8z"/><path pathLength="1" d="M24 14v26"/><path pathLength="1" d="M24 14c-2.8-5.8-10.2-6.2-8.4-1.4.9 2.2 5.2 1.4 8.4 1.4"/><path pathLength="1" d="M24 14c2.8-5.8 10.2-6.2 8.4-1.4-.9 2.2-5.2 1.4-8.4 1.4"/></svg></span><strong><?php esc_html_e( 'Delivered', 'designer-designs' ); ?></strong><span><?php esc_html_e( 'Free in the UK, with its care kit', 'designer-designs' ); ?></span></li>
		</ol>
	</section>

	<?php
	/* Home 4 · reviews. Only real reviews, typed in Customizer (one per line: quote | name | place). Hidden while empty. */
	$dd_revs = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) get_theme_mod( 'dd_home_reviews', '' ) ) as $dd_line ) {
		$dd_parts = array_map( 'trim', explode( '|', $dd_line ) );
		if ( '' !== $dd_parts[0] ) {
			$dd_revs[] = $dd_parts;
		}
	}
	?>
	<?php if ( $dd_revs ) : ?>
	<section class="dd-wrap dd-hrev dd-rv" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Reviews from our Etsy shop', 'designer-designs' ); ?>" data-dd-hrev>
		<p class="dd-hrev__stars" aria-hidden="true">★★★★★</p>
		<div class="dd-hrev__stage">
			<?php foreach ( $dd_revs as $dd_i => $dd_r ) : ?>
				<figure class="dd-hrev__item<?php echo 0 === $dd_i ? ' is-active' : ''; ?>" aria-hidden="<?php echo 0 === $dd_i ? 'false' : 'true'; ?>">
					<blockquote>“<?php echo esc_html( $dd_r[0] ); ?>”</blockquote>
					<figcaption><?php echo esc_html( implode( ' · ', array_filter( array( $dd_r[1] ?? '', $dd_r[2] ?? '' ) ) ) ); ?></figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
		<p class="dd-hrev__src"><?php esc_html_e( 'Reviews from our Etsy shop', 'designer-designs' ); ?></p>
		<?php if ( count( $dd_revs ) > 1 ) : ?>
			<div class="dd-hrev__dots">
				<?php foreach ( $dd_revs as $dd_i => $dd_r ) : ?>
					<button type="button" class="<?php echo 0 === $dd_i ? 'is-active' : ''; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Review %d', 'designer-designs' ), $dd_i + 1 ) ); ?>"><span></span></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</section>
	<?php endif; ?>

</main>
<?php
get_footer();
