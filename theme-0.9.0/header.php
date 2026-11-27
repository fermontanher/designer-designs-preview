<?php
/**
 * Header: announcement bar, products-only navigation with photo mega menu.
 *
 * @package designer-designs
 */

defined( 'ABSPATH' ) || exit;
$dd_cats  = dd_nav_categories();
$dd_count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
$dd_logo  = get_theme_mod( 'custom_logo' ) ? wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' ) : dd_img( 'logo-secondary-gold.png' );
$dd_now   = get_queried_object();
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'designer-designs' ); ?></a>

<div class="dd-announce" data-dd-announce aria-label="<?php esc_attr_e( 'Shop news', 'designer-designs' ); ?>">
	<?php
	foreach ( array( 'dd_announce_1', 'dd_announce_2', 'dd_announce_3' ) as $dd_k ) {
		$dd_msg = trim( (string) dd_mod( $dd_k ) );
		if ( $dd_msg ) {
			echo '<span>' . esc_html( $dd_msg ) . '</span>';
		}
	}
	?>
</div>

<header class="dd-header" data-dd-header>
	<div class="dd-wrap dd-header__bar">
		<a class="dd-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' — ' . __( 'home', 'designer-designs' ) ); ?>">
			<img src="<?php echo esc_url( $dd_logo ); ?>" alt="" width="190" height="46">
		</a>

		<nav class="dd-nav dd-desk" aria-label="<?php esc_attr_e( 'Shop', 'designer-designs' ); ?>">
			<?php foreach ( $dd_cats as $dd_cat ) : ?>
				<?php $dd_cur = ( $dd_now instanceof WP_Term && ( $dd_now->term_id === $dd_cat->term_id || $dd_now->parent === $dd_cat->term_id ) ); ?>
				<?php if ( dd_category_soon( $dd_cat ) ) : ?>
				<span class="dd-nav__btn is-soon" aria-disabled="true"><?php echo esc_html( $dd_cat->name ); ?> <sup><?php esc_html_e( 'Soon', 'designer-designs' ); ?></sup></span>
				<?php continue; endif; ?>
				<button type="button" class="dd-nav__btn<?php echo $dd_cur ? ' is-current' : ''; ?>" aria-expanded="false" aria-controls="dd-mega-<?php echo esc_attr( $dd_cat->slug ); ?>" data-dd-mega="<?php echo esc_attr( $dd_cat->slug ); ?>" data-href="<?php echo esc_url( get_term_link( $dd_cat ) ); ?>">
					<?php echo esc_html( $dd_cat->name ); ?>
				</button>
			<?php endforeach; ?>
			<?php if ( dd_page_url( 'size-guide' ) ) : ?>
				<a class="dd-nav__btn dd-nav__sizes<?php echo is_page( 'size-guide' ) ? ' is-current' : ''; ?>" href="<?php echo esc_url( dd_page_url( 'size-guide' ) ); ?>"><?php esc_html_e( 'Sizes', 'designer-designs' ); ?></a>
			<?php endif; ?>
		</nav>

		<div class="dd-tools">
			<button type="button" class="dd-icon-btn" data-dd-search-toggle aria-expanded="false" aria-controls="dd-search" aria-label="<?php esc_attr_e( 'Search', 'designer-designs' ); ?>"><?php echo dd_icon( 'search' ); // phpcs:ignore ?></button>
			<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
				<a class="dd-icon-btn dd-desk" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" aria-label="<?php esc_attr_e( 'Your account', 'designer-designs' ); ?>"><?php echo dd_icon( 'user' ); // phpcs:ignore ?></a>
			<?php endif; ?>
			<button type="button" class="dd-icon-btn" data-dd-drawer-open aria-controls="dd-drawer" aria-label="<?php esc_attr_e( 'Open basket', 'designer-designs' ); ?>">
				<?php echo dd_icon( 'bag' ); // phpcs:ignore ?>
				<span class="dd-count" data-count="<?php echo esc_attr( $dd_count ); ?>"><?php echo $dd_count ? esc_html( $dd_count ) : ''; ?></span>
			</button>
			<button type="button" class="dd-icon-btn dd-mob" data-dd-mobnav aria-expanded="false" aria-controls="dd-mobnav" aria-label="<?php esc_attr_e( 'Menu', 'designer-designs' ); ?>"><?php echo dd_icon( 'menu' ); // phpcs:ignore ?></button>
		</div>
	</div>

	<div class="dd-search" id="dd-search" hidden>
		<form class="dd-wrap" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="display:flex;gap:16px;align-items:center;padding-top:8px;padding-bottom:20px">
			<label class="screen-reader-text" for="dd-s"><?php esc_html_e( 'Search the shop', 'designer-designs' ); ?></label>
			<input class="dd-field" id="dd-s" type="search" name="s" placeholder="<?php esc_attr_e( 'Search lamps, vases, sculptures…', 'designer-designs' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
			<input type="hidden" name="post_type" value="product">
			<button class="dd-btn" type="submit"><?php esc_html_e( 'Search', 'designer-designs' ); ?></button>
		</form>
	</div>

	<?php /* Mega menus: one panel per category, sub-categories on the left, product photos on the right. */ ?>
	<?php foreach ( $dd_cats as $dd_cat ) : ?>
		<?php
		$dd_subs  = dd_sub_categories( $dd_cat->term_id );
		$dd_items = dd_category_products( $dd_cat->slug, 4 );
		$dd_soon  = dd_category_soon( $dd_cat ) || ! $dd_items;
		if ( $dd_soon ) {
			continue;
		}
		?>
		<div class="dd-mega dd-desk" id="dd-mega-<?php echo esc_attr( $dd_cat->slug ); ?>" data-dd-panel="<?php echo esc_attr( $dd_cat->slug ); ?>">
			<div class="dd-wrap dd-mega__inner">
				<div>
					<a class="dd-mega__title" href="<?php echo esc_url( get_term_link( $dd_cat ) ); ?>" style="text-decoration:none"><?php echo esc_html( $dd_cat->name ); ?></a>
					<div class="dd-mega__subs">
						<?php foreach ( $dd_subs as $dd_sub ) : ?>
							<a href="<?php echo esc_url( get_term_link( $dd_sub ) ); ?>"><?php echo esc_html( $dd_sub->name ); ?></a>
						<?php endforeach; ?>
					</div>
					<?php if ( ! $dd_soon ) : ?>
						<a class="dd-mega__all" href="<?php echo esc_url( get_term_link( $dd_cat ) ); ?>" style="display:inline-block"><?php echo esc_html( sprintf( __( 'Shop all %s', 'designer-designs' ), strtolower( $dd_cat->name ) ) ); ?></a>
					<?php endif; ?>
				</div>
				<?php if ( $dd_soon ) : ?>
					<div class="dd-mega__soon">
						<img src="<?php echo dd_img( 'gold-vein-dark.jpg' ); // phpcs:ignore ?>" alt="" loading="lazy">
						<div>
							<strong class="dd-italic" style="font-size:32px;font-weight:400"><?php echo esc_html( sprintf( __( '%s are on the workbench', 'designer-designs' ), $dd_cat->name ) ); ?></strong>
							<p class="dd-muted"><?php echo esc_html( $dd_cat->description ? wp_strip_all_tags( $dd_cat->description ) : __( 'New pieces in the same hand-worked finishes. Leave your email in the footer and we will tell you first.', 'designer-designs' ) ); ?></p>
						</div>
					</div>
				<?php else : ?>
					<div class="dd-mega__grid">
						<?php foreach ( $dd_items as $dd_p ) : ?>
							<a class="dd-mega__item" href="<?php echo esc_url( $dd_p->get_permalink() ); ?>">
								<?php echo $dd_p->get_image( 'dd-card', array( 'loading' => 'lazy', 'alt' => '' ) ); // phpcs:ignore ?>
								<span class="name"><?php echo esc_html( $dd_p->get_name() ); ?></span>
								<?php /* Client 26/11: no prices on the menu pictures. */ ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>

	<nav class="dd-mobnav" id="dd-mobnav" aria-label="<?php esc_attr_e( 'Shop', 'designer-designs' ); ?>">
		<?php foreach ( $dd_cats as $dd_cat ) : ?>
			<?php if ( dd_category_soon( $dd_cat ) ) : ?>
			<div class="cat is-soon" aria-disabled="true">
				<span><?php echo esc_html( $dd_cat->name ); ?> <small><?php esc_html_e( 'Soon', 'designer-designs' ); ?></small></span>
			</div>
			<?php continue; endif; ?>
			<a class="cat" href="<?php echo esc_url( get_term_link( $dd_cat ) ); ?>">
				<img src="<?php echo esc_url( dd_category_image( $dd_cat, 'dd-thumb' ) ); ?>" alt="" width="56" height="70" loading="lazy">
				<span><?php echo esc_html( $dd_cat->name ); ?></span>
			</a>
		<?php endforeach; ?>
		<?php if ( dd_page_url( 'size-guide' ) ) : ?>
			<a class="cat cat--sizes" href="<?php echo esc_url( dd_page_url( 'size-guide' ) ); ?>"><span class="dd-mob-sizes" aria-hidden="true">S M L</span><span><?php esc_html_e( 'Find your size', 'designer-designs' ); ?></span></a>
		<?php endif; ?>
		<div class="more">
			<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Your account', 'designer-designs' ); ?></a>
			<?php endif; ?>
			<?php dd_footer_links( 'footer-help', true ); ?>
		</div>
	</nav>
</header>
