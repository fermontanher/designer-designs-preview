
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
