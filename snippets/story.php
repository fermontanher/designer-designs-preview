
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
			<?php if ( $dd_potm_text ) : ?><p class="dd-potm__text dd-rv"><?php echo esc_html( wp_trim_words( $dd_potm_text, 38 ) ); ?></p><?php endif; ?>
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
			<li class="dd-rv"><span class="dd-steps__num" aria-hidden="true">I</span><strong><?php esc_html_e( 'You choose', 'designer-designs' ); ?></strong><span><?php esc_html_e( 'Your piece, size and finish', 'designer-designs' ); ?></span></li>
			<li class="dd-rv"><span class="dd-steps__num" aria-hidden="true">II</span><strong><?php esc_html_e( 'Shaped by hand', 'designer-designs' ); ?></strong><span><?php esc_html_e( 'Formed, textured and painted in London', 'designer-designs' ); ?></span></li>
			<li class="dd-rv"><span class="dd-steps__num" aria-hidden="true">III</span><strong><?php esc_html_e( 'Delivered', 'designer-designs' ); ?></strong><span><?php esc_html_e( 'Free in the UK, with its care kit', 'designer-designs' ); ?></span></li>
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
