<?php /* Client 24/11: short reassurance list (bold lead, normal text), in place of the lead-time box and the trust line. No stock labels. */ ?>
			<ul class="dd-promise">
				<li><?php echo dd_icon( 'clock', 20 ); // phpcs:ignore ?><span><strong><?php esc_html_e( 'Handmade to order.', 'designer-designs' ); ?></strong> <?php echo esc_html( ( $dd_lead && preg_match( '/\d/', $dd_lead ) ) ? $dd_lead : __( 'Formed and painted by hand in our London studio, so no two pieces are the same.', 'designer-designs' ) ); ?></span></li>
				<li><?php echo dd_icon( 'truck', 20 ); // phpcs:ignore ?><span><strong><?php esc_html_e( 'Free UK delivery.', 'designer-designs' ); ?></strong> <?php esc_html_e( 'On every order, carefully packed. For delivery outside the UK, please contact us.', 'designer-designs' ); ?></span></li>
				<li><?php echo dd_icon( 'heart', 20 ); // phpcs:ignore ?><span><strong><?php esc_html_e( 'The Love It Guarantee.', 'designer-designs' ); ?></strong> <?php esc_html_e( '30 days to be sure it feels right in your home.', 'designer-designs' ); ?></span></li>
				<li><?php echo dd_icon( 'bag', 20 ); // phpcs:ignore ?><span><strong><?php esc_html_e( 'Care kit included.', 'designer-designs' ); ?></strong> <?php esc_html_e( 'A detail brush, a soft cloth and a care card in every box.', 'designer-designs' ); ?></span></li>
			</ul>
