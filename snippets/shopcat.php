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
