<?php
/**
 * Small helpers shared by templates.
 *
 * @package designer-designs
 */

defined( 'ABSPATH' ) || exit;

/** Theme image URL. */
function dd_img( $file ) {
	return esc_url( DD_URI . '/assets/img/' . $file );
}

/** Free-delivery threshold in shop currency (Customizer, default 150). */
function dd_free_threshold() {
	return (float) get_theme_mod( 'dd_free_threshold', 150 );
}

/** Inline SVG icons (stroke = currentColor). */
function dd_icon( $name, $size = 22 ) {
	$paths = array(
		'search'  => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'heart'   => '<path d="M12 20s-7-4.4-9.2-9A5 5 0 0 1 12 6a5 5 0 0 1 9.2 5c-2.2 4.6-9.2 9-9.2 9z"/>',
		'bag'     => '<path d="M5 8h14l-1 12H6L5 8z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>',
		'user'    => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
		'menu'    => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'   => '<path d="M6 6l12 12M18 6 6 18"/>',
		'plus'    => '<path d="M12 5v14M5 12h14"/>',
		'minus'   => '<path d="M5 12h14"/>',
		'left'    => '<path d="m15 6-6 6 6 6"/>',
		'right'   => '<path d="m9 6 6 6-6 6"/>',
		'arrow'   => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'lamp'    => '<path d="M8 3h8l3 8H5l3-8z"/><path d="M12 11v9M8 21h8"/>',
		'clock'   => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'truck'   => '<path d="M3 6h11v10H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg aria-hidden="true" focusable="false" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">%2$s</svg>',
		(int) $size,
		$paths[ $name ]
	);
}

/**
 * Top-level product categories for the navigation (Uncategorised is skipped).
 * Order follows the term meta "order" that WooCommerce uses for drag-and-drop sorting.
 *
 * @return WP_Term[]
 */
function dd_nav_categories() {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}
	$terms = get_terms( array(
		'taxonomy'   => 'product_cat',
		'parent'     => 0,
		'hide_empty' => false,
		'orderby'    => 'meta_value_num',
		'meta_key'   => 'order', // phpcs:ignore WordPress.DB.SlowDBQuery
		'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
	) );
	$cache = is_wp_error( $terms ) ? array() : $terms;
	return $cache;
}

/** Children of a category. */
function dd_sub_categories( $term_id ) {
	$terms = get_terms( array(
		'taxonomy'   => 'product_cat',
		'parent'     => (int) $term_id,
		'hide_empty' => false,
		'orderby'    => 'meta_value_num',
		'meta_key'   => 'order', // phpcs:ignore WordPress.DB.SlowDBQuery
	) );
	return is_wp_error( $terms ) ? array() : $terms;
}

/** A few products for a category, best sellers first. */
function dd_category_products( $slug, $limit = 4 ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}
	return wc_get_products( array(
		'status'   => 'publish',
		'limit'    => $limit,
		'category' => array( $slug ),
		'orderby'  => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	) );
}

/** Category image URL (falls back to the first product image). */
function dd_category_image( $term, $size = 'dd-card' ) {
	$thumb = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
	if ( $thumb ) {
		return wp_get_attachment_image_url( $thumb, $size );
	}
	$products = dd_category_products( $term->slug, 1 );
	if ( $products && $products[0]->get_image_id() ) {
		return wp_get_attachment_image_url( $products[0]->get_image_id(), $size );
	}
	return dd_img( 'gold-vein-dark.jpg' );
}

/** Is this category flagged "coming soon" (term meta dd_soon = 1)? */
function dd_category_soon( $term ) {
	static $cache = array();
	if ( ! isset( $cache[ $term->term_id ] ) ) {
		$cache[ $term->term_id ] = (bool) get_term_meta( $term->term_id, 'dd_soon', true ) || ! dd_category_products( $term->slug, 1 );
	}
	return $cache[ $term->term_id ];
}

/** Short note shown under a category tile (term meta dd_note, else description). */
function dd_category_note( $term ) {
	$note = get_term_meta( $term->term_id, 'dd_note', true );
	return $note ? $note : wp_trim_words( wp_strip_all_tags( $term->description ), 6, '' );
}

/**
 * "Add a little extra" suggestions.
 * Order: cross-sells of the given products → products tagged "little-extra" → lowest priced in stock.
 *
 * @param int[] $for_ids  Products the suggestions are for.
 * @param int[] $exclude  Products to skip (already in basket).
 * @param int   $limit    How many.
 * @return WC_Product[]
 */
function dd_extras( $for_ids = array(), $exclude = array(), $limit = 3 ) {
	if ( ! function_exists( 'wc_get_product' ) ) {
		return array();
	}
	$ids = array();
	foreach ( (array) $for_ids as $id ) {
		$p = wc_get_product( $id );
		if ( $p ) {
			$parent = $p->get_parent_id() ? wc_get_product( $p->get_parent_id() ) : $p;
			$ids    = array_merge( $ids, $parent ? $parent->get_cross_sell_ids() : array() );
		}
	}
	$exclude = array_map( 'intval', (array) $exclude );
	$ids     = array_values( array_diff( array_unique( array_map( 'intval', $ids ) ), $exclude ) );

	if ( count( $ids ) < $limit ) {
		$tagged = wc_get_products( array( 'status' => 'publish', 'limit' => 8, 'tag' => array( 'little-extra' ), 'return' => 'ids', 'type' => array( 'simple', 'variable' ) ) );
		$ids    = array_values( array_unique( array_merge( $ids, array_diff( $tagged, $exclude ) ) ) );
	}
	if ( count( $ids ) < $limit ) {
		$cheap = wc_get_products( array( 'status' => 'publish', 'limit' => 8, 'orderby' => 'price', 'order' => 'ASC', 'return' => 'ids', 'type' => array( 'simple', 'variable' ) ) );
		$ids   = array_values( array_unique( array_merge( $ids, array_diff( $cheap, $exclude ) ) ) );
	}

	$out = array();
	foreach ( $ids as $id ) {
		$p = wc_get_product( $id );
		if ( $p && $p->is_purchasable() && $p->is_in_stock() && $p->is_visible() ) {
			$out[] = $p;
		}
		if ( count( $out ) >= $limit ) {
			break;
		}
	}
	return $out;
}

/**
 * The variation an "Add" button should put in the basket: the product's default size
 * (Medium), so small extras stay one tap even though every piece comes in three sizes.
 */
function dd_quick_variation( WC_Product $p ) {
	if ( ! $p->is_type( 'variable' ) ) {
		return null;
	}
	$want = $p->get_default_attributes();
	$best = null;
	foreach ( $p->get_children() as $vid ) {
		$v = wc_get_product( $vid );
		if ( ! $v || ! $v->is_purchasable() || ! $v->is_in_stock() ) {
			continue;
		}
		$attrs = $v->get_attributes();
		$match = true;
		foreach ( $want as $k => $val ) {
			if ( isset( $attrs[ $k ] ) && '' !== $attrs[ $k ] && $attrs[ $k ] !== $val ) {
				$match = false;
			}
		}
		if ( $match ) {
			return $v;
		}
		$best = $best ? $best : $v;
	}
	return $best;
}

/** Number of sizes a product comes in (0 when it has no size attribute). */
function dd_size_count( WC_Product $p ) {
	foreach ( $p->get_attributes() as $name => $attr ) {
		if ( false !== strpos( $name, 'size' ) ) {
			return count( $attr->get_options() );
		}
	}
	return 0;
}

/** Render one small "extra" card with an AJAX add button (default size for sized pieces). */
function dd_extra_card( WC_Product $p ) {
	$img   = $p->get_image_id() ? wp_get_attachment_image_url( $p->get_image_id(), 'dd-card' ) : wc_placeholder_img_src();
	$v     = dd_quick_variation( $p );
	$buy   = $v ? $v : $p;
	$label = $v ? wc_get_formatted_variation( $v, true, false, false ) : '';
	?>
	<div class="dd-extra">
		<a href="<?php echo esc_url( $p->get_permalink() ); ?>"><img src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy" width="320" height="400"></a>
		<a href="<?php echo esc_url( $p->get_permalink() ); ?>" style="color:inherit;text-decoration:none"><?php echo esc_html( $p->get_name() ); ?></a>
		<span class="price"><?php echo wp_kses_post( $buy->get_price_html() ); ?><?php echo $label ? ' <small class="dd-muted">· ' . esc_html( $label ) . '</small>' : ''; ?></span>
		<a href="<?php echo esc_url( $v ? $p->get_permalink() : $p->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $buy->get_id() ); ?>" data-product_sku="<?php echo esc_attr( $buy->get_sku() ); ?>" class="button add_to_cart_button ajax_add_to_cart dd-extra__add" aria-label="<?php echo esc_attr( sprintf( __( 'Add %s to your basket', 'designer-designs' ), trim( $p->get_name() . ' ' . $label ) ) ); ?>" rel="nofollow"><?php esc_html_e( 'Add', 'designer-designs' ); ?></a>
	</div>
	<?php
}

/** Page URL by slug, or '' when the page doesn't exist. */
function dd_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return ( $page && 'publish' === $page->post_status ) ? get_permalink( $page ) : '';
}

/** Average rating across all approved product reviews (for the "4.9" block). */
function dd_store_rating() {
	$override = trim( (string) dd_mod( 'dd_rating_override' ) );
	if ( '' !== $override ) {
		return array( (float) $override, (int) dd_mod( 'dd_rating_count' ), dd_mod( 'dd_rating_source' ) );
	}
	global $wpdb;
	$row = $wpdb->get_row( "SELECT AVG(m.meta_value) avg, COUNT(*) n FROM {$wpdb->comments} c JOIN {$wpdb->commentmeta} m ON m.comment_id=c.comment_ID AND m.meta_key='rating' WHERE c.comment_approved='1' AND c.comment_type='review'" ); // phpcs:ignore
	return array( $row && $row->n ? round( (float) $row->avg, 1 ) : 0, $row ? (int) $row->n : 0, '' );
}

/**
 * Footer/help links: the assigned menu, or the starter pages that exist.
 *
 * @param string $location Menu location.
 * @param bool   $inline   Bare links (mobile nav) instead of a list.
 */
function dd_footer_links( $location, $inline = false, $prepend = array() ) {
	$fallback = array(
		'footer-help'   => array( 'delivery' => 'Delivery & lead times', 'returns' => 'Returns & refunds', 'size-guide' => 'Size guide', 'care' => 'Care guides', 'faq' => 'FAQ', 'contact' => 'Contact us' ),
		'footer-studio' => array( 'our-story' => 'Our story', 'materials' => 'Materials & process', 'commissions' => 'Custom commissions', 'giving-back' => 'Giving back', 'journal' => 'Journal', 'press' => 'Press' ),
		'footer-account' => array( 'trade' => 'Trade accounts', 'gift-cards' => 'Gift cards' ),
		'footer-legal'  => array( 'privacy-policy' => 'Privacy', 'terms' => 'Terms', 'cookies' => 'Cookies' ),
	);
	$links = $prepend;
	$locs  = get_nav_menu_locations();
	if ( ! empty( $locs[ $location ] ) ) {
		foreach ( (array) wp_get_nav_menu_items( $locs[ $location ] ) as $item ) {
			$links[] = array( $item->url, $item->title );
		}
	} else {
		foreach ( $fallback[ $location ] ?? array() as $slug => $label ) {
			$page = get_page_by_path( $slug );
			if ( $page && 'publish' === $page->post_status ) {
				$links[] = array( get_permalink( $page ), __( $label, 'designer-designs' ) ); // phpcs:ignore WordPress.WP.I18n
			}
		}
	}
	if ( $inline ) {
		foreach ( $links as $l ) {
			printf( '<a href="%s">%s</a>', esc_url( $l[0] ), esc_html( $l[1] ) );
		}
		return;
	}
	echo '<ul>';
	foreach ( $links as $l ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $l[0] ), esc_html( $l[1] ) );
	}
	echo '</ul>';
}

/**
 * Social links for the footer, from the Customizer. Empty links are skipped.
 * Icons are simple outline glyphs drawn at 20px in currentColor.
 */
function dd_social_links() {
	$icons = array(
		'dd_instagram' => array( 'Instagram', '<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r=".9" fill="currentColor" stroke="none"/>' ),
		'dd_etsy'      => array( 'Etsy', '<path d="M8 5.5h8.5l.3 3M8 5.5v13M8 12h6M8 18.5h8.8l.4-3"/>' ),
		'dd_pinterest' => array( 'Pinterest', '<circle cx="12" cy="12" r="8.5"/><path d="M11.2 9.2c1.8-1.4 4.6-.4 4.3 2-.3 2.6-2.6 3.4-3.9 2.3M11.8 9.6 9.6 20"/>' ),
		'dd_facebook'  => array( 'Facebook', '<path d="M14.5 20.5v-7.3h2.4l.4-2.8h-2.8V8.6c0-.8.3-1.4 1.4-1.4h1.5V4.7c-.3 0-1.2-.1-2.2-.1-2.2 0-3.6 1.3-3.6 3.7v2.1H9.2v2.8h2.4v7.3"/>' ),
		'dd_tiktok'    => array( 'TikTok', '<path d="M14 4v10.5a3.5 3.5 0 1 1-3.5-3.5M14 4c.4 2.4 2 4 4.5 4.2"/>' ),
	);
	$out = '';
	foreach ( $icons as $key => $icon ) {
		$url = dd_mod( $key );
		if ( ! $url || in_array( untrailingslashit( $url ), array( 'https://www.instagram.com', 'https://instagram.com' ), true ) ) {
			continue; // Skip empty links and the bare placeholder.
		}
		$out .= sprintf(
			'<li><a href="%1$s" rel="noopener" target="_blank" aria-label="%2$s"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%3$s</svg></a></li>',
			esc_url( $url ),
			/* translators: %s: social network name */
			esc_attr( sprintf( __( 'Designer Designs on %s (opens in a new tab)', 'designer-designs' ), $icon[0] ) ),
			$icon[1]
		);
	}
	return $out ? '<ul class="dd-social">' . $out . '</ul>' : '';
}
