<?php
/**
 * Designer Designs theme functions.
 *
 * @package designer-designs
 */

defined( 'ABSPATH' ) || exit;

define( 'DD_VERSION', '0.11.9' );
define( 'DD_URI', get_template_directory_uri() );
define( 'DD_DIR', get_template_directory() );

require DD_DIR . '/inc/helpers.php';
require DD_DIR . '/inc/customizer.php';
require DD_DIR . '/inc/woocommerce.php';

/* ------------------------------------------------------------------ */
/* Setup                                                               */
/* ------------------------------------------------------------------ */
add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'designer-designs', DD_DIR . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-logo', array( 'height' => 92, 'width' => 280, 'flex-width' => true, 'flex-height' => true ) );

	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 640,
		'single_image_width'    => 1200,
		'product_grid'          => array( 'default_columns' => 4, 'default_rows' => 3 ),
	) );

	add_image_size( 'dd-card', 640, 800, true );
	add_image_size( 'dd-thumb', 168, 210, true );
	add_image_size( 'dd-large', 1400, 1750, true );

	register_nav_menus( array(
		'footer-help'   => __( 'Footer: Customer care', 'designer-designs' ),
		'footer-studio' => __( 'Footer: The studio', 'designer-designs' ),
		'footer-account' => __( 'Footer: Your account', 'designer-designs' ),
		'footer-legal'  => __( 'Footer: Legal', 'designer-designs' ),
	) );
} );

/* ------------------------------------------------------------------ */
/* Assets                                                              */
/* ------------------------------------------------------------------ */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'designer-designs', get_stylesheet_uri(), array(), DD_VERSION );

	// WooCommerce layout styles fight the custom grid; keep the general ones (icons, notices).
	wp_dequeue_style( 'woocommerce-layout' );
	wp_dequeue_style( 'woocommerce-smallscreen' );

	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_script( 'wc-add-to-cart' );
		wp_enqueue_script( 'wc-cart-fragments' );
	}
	wp_enqueue_script( 'designer-designs', DD_URI . '/assets/js/theme.js', array( 'jquery' ), DD_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_localize_script( 'designer-designs', 'DD', array(
		'freeThreshold' => dd_free_threshold(),
		'currency'      => function_exists( 'get_woocommerce_currency_symbol' ) ? html_entity_decode( get_woocommerce_currency_symbol() ) : '£',
		'klarna'        => (bool) get_theme_mod( 'dd_klarna', true ),
		'offerDelay'    => (int) get_theme_mod( 'dd_offer_delay', 12 ),
		'offerOn'       => (bool) get_theme_mod( 'dd_offer_on', true ),
		'i18n'          => array(
			'added'      => __( 'added to your basket', 'designer-designs' ),
			'add'        => __( 'Add', 'designer-designs' ),
			'addedShort' => __( 'Added', 'designer-designs' ),
			'lampOn'     => __( 'Switch the lamp on', 'designer-designs' ),
			'lampOff'    => __( 'Switch the lamp off', 'designer-designs' ),
			'klarna'     => __( 'or 3 interest-free payments of %s with Klarna', 'designer-designs' ),
			'choose'     => __( 'Choose a size and colour', 'designer-designs' ),
		),
	) );
} );

add_action( 'wp_head', function () {
	// Preload the two fonts used above the fold.
	printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( DD_URI . '/assets/fonts/gentium-italic.woff2' ) );
	printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( DD_URI . '/assets/fonts/raleway-variable.woff2' ) );
	echo '<meta name="theme-color" content="#000000">' . "\n";
}, 1 );

/* ------------------------------------------------------------------ */
/* Analytics (milestone 4): GA4 measurement ID set in the Customizer   */
/* ------------------------------------------------------------------ */
add_action( 'wp_head', function () {
	$id = trim( (string) get_theme_mod( 'dd_ga4_id', '' ) );
	if ( ! $id || is_user_logged_in() && current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $id ); ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?php echo esc_js( $id ); ?>');</script>
	<?php
}, 5 );

/* Body classes */
add_filter( 'body_class', function ( $classes ) {
	$classes[] = 'dd';
	return $classes;
} );

/* Excerpt length for short descriptions in cards */
add_filter( 'excerpt_length', fn() => 24 );

/* ------------------------------------------------------------------ */
/* Newsletter sign-ups (stored locally until a mail service is chosen) */
/* ------------------------------------------------------------------ */
function dd_handle_subscribe() {
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	if ( ! isset( $_POST['dd_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['dd_nonce'] ), 'dd_subscribe' ) ) {
		wp_safe_redirect( $back );
		exit;
	}
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( is_email( $email ) ) {
		$list           = (array) get_option( 'dd_subscribers', array() );
		$list[ $email ] = current_time( 'mysql' );
		update_option( 'dd_subscribers', $list, false );
	}
	wp_safe_redirect( add_query_arg( 'subscribed', 'yes', $back ) . '#dd-news-email' );
	exit;
}
add_action( 'admin_post_nopriv_dd_subscribe', 'dd_handle_subscribe' );
add_action( 'admin_post_dd_subscribe', 'dd_handle_subscribe' );

add_action( 'admin_menu', function () {
	add_submenu_page( 'woocommerce', __( 'Newsletter sign-ups', 'designer-designs' ), __( 'Newsletter sign-ups', 'designer-designs' ), 'manage_woocommerce', 'dd-subscribers', function () {
		$list = (array) get_option( 'dd_subscribers', array() );
		echo '<div class="wrap"><h1>' . esc_html__( 'Newsletter sign-ups', 'designer-designs' ) . '</h1>';
		echo '<p>' . esc_html__( 'Copy these into your email tool (Mailchimp, Klaviyo…).', 'designer-designs' ) . '</p>';
		echo '<textarea readonly rows="12" style="width:100%;max-width:640px">' . esc_textarea( implode( "\n", array_keys( $list ) ) ) . '</textarea>';
		echo '<p>' . esc_html( sprintf( _n( '%d person', '%d people', count( $list ), 'designer-designs' ), count( $list ) ) ) . '</p></div>';
	} );
} );

/* ------------------------------------------------------------------ */
/* Client review 24/11/2026: calmer, more elegant shop                 */
/* ------------------------------------------------------------------ */

// No "In stock" labels: every piece is made to order, and stock lines read as unsold items.
add_filter( 'woocommerce_get_stock_html', function ( $html, $product ) {
	return $product && ! $product->is_in_stock() ? $html : '';
}, 20, 2 );
add_filter( 'woocommerce_get_availability_text', function ( $text, $product ) {
	return $product && $product->is_in_stock() ? '' : $text;
}, 20, 2 );

// Three similar pieces under the product ("add-on sales"), not four.
add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
	$args['posts_per_page'] = 3;
	$args['columns']        = 3;
	return $args;
}, 20 );

// The promotional pop-up is switched off (the client finds boxes like this distracting).
add_filter( 'theme_mod_dd_offer_on', '__return_false' );

// Product grid: two or three pieces per row (Customizer > Designer Designs > Shop layout).
add_action( 'customize_register', function ( $wp_customize ) {
	$wp_customize->add_section( 'dd_s_grid', array(
		'title'    => __( 'Shop layout', 'designer-designs' ),
		'panel'    => 'dd_panel',
		'priority' => 25,
	) );
	$wp_customize->add_setting( 'dd_shop_cols', array( 'default' => '3', 'sanitize_callback' => fn( $v ) => in_array( (string) $v, array( '2', '3' ), true ) ? (string) $v : '3' ) );
	$wp_customize->add_control( 'dd_shop_cols', array(
		'label'   => __( 'Pieces per row on desktop', 'designer-designs' ),
		'section' => 'dd_s_grid',
		'type'    => 'radio',
		'choices' => array( '2' => __( 'Two, larger images', 'designer-designs' ), '3' => __( 'Three', 'designer-designs' ) ),
	) );
}, 20 );
add_filter( 'body_class', function ( $classes ) {
	$cols = isset( $_GET['dd_cols'] ) && in_array( $_GET['dd_cols'], array( '2', '3' ), true ) ? $_GET['dd_cols'] : get_theme_mod( 'dd_shop_cols', '3' ); // phpcs:ignore -- preview switch only changes layout.
	$classes[] = 'dd-cols-' . ( '2' === (string) $cols ? '2' : '3' );
	return $classes;
} );

// Always three similar pieces: top up from the rest of the shop when a category is still small.
add_filter( 'woocommerce_related_products', function ( $ids, $product_id ) {
	$ids = array_values( array_diff( array_map( 'intval', (array) $ids ), array( (int) $product_id ) ) );
	if ( count( $ids ) >= 3 ) {
		return array_slice( $ids, 0, 3 );
	}
	$more = wc_get_products( array( 'status' => 'publish', 'limit' => 6, 'return' => 'ids', 'exclude' => array_merge( $ids, array( (int) $product_id ) ), 'orderby' => 'rand' ) );
	return array_slice( array_merge( $ids, $more ), 0, 3 );
}, 20, 2 );

// One-off (24/11): platters became their own collection, so the bowls note no longer mentions them.
add_action( 'init', function () {
	if ( get_option( 'dd_0910_notes' ) ) {
		return;
	}
	$bowls = get_term_by( 'slug', 'bowls-platters', 'product_cat' );
	if ( $bowls ) {
		update_term_meta( $bowls->term_id, 'dd_note', 'Sculptural display bowls' );
	}
	update_option( 'dd_0910_notes', 1, false );
} );

// Client 24/11: the three similar pieces sit beside the basket ("Add a little extra", as on Cox & Cox), so no second grid at the bottom. (dd_no_bottom_related)
add_action( 'wp', function () {
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
} );

/* 0.11.0 Home sections: piece of the month, studio image, reviews (real Etsy reviews only). */
add_action( 'customize_register', function ( WP_Customize_Manager $c ) {
	$c->add_section( 'dd_home_sections', array( 'title' => __( 'Home sections', 'designer-designs' ), 'panel' => 'dd_panel', 'priority' => 5 ) );
	$c->add_setting( 'dd_potm', array( 'default' => 672, 'sanitize_callback' => 'absint' ) );
	$c->add_control( 'dd_potm', array( 'label' => __( 'Piece of the month (product ID)', 'designer-designs' ), 'section' => 'dd_home_sections', 'type' => 'number' ) );
	$c->add_setting( 'dd_potm_text', array( 'default' => '', 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$c->add_control( 'dd_potm_text', array( 'label' => __( 'Piece of the month text (empty = product short description)', 'designer-designs' ), 'section' => 'dd_home_sections', 'type' => 'textarea' ) );
	$c->add_setting( 'dd_studio_img', array( 'default' => 768, 'sanitize_callback' => 'absint' ) );
	$c->add_control( new WP_Customize_Media_Control( $c, 'dd_studio_img', array( 'label' => __( 'From the studio image', 'designer-designs' ), 'section' => 'dd_home_sections', 'mime_type' => 'image' ) ) );
	$c->add_setting( 'dd_home_reviews', array( 'default' => '', 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$c->add_control( 'dd_home_reviews', array( 'label' => __( 'Reviews (real Etsy reviews only)', 'designer-designs' ), 'description' => __( 'One per line: quote | name | place. The section stays hidden while this is empty.', 'designer-designs' ), 'section' => 'dd_home_sections', 'type' => 'textarea' ) );
}, 30 );
