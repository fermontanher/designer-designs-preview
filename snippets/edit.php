
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
