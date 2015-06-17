<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @TODO: change DB prefix to cb_ 
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@macht-medien.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */


function pages_dropdown() {
	// dropdown for page select
	$pages = get_pages();
	$dropdown = array();
	
	foreach ( $pages as $page ) {
		$dropdown[$page->ID] = $page->post_title;
	}
	return $dropdown;
}
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<div id="tabs">
		<ul>
			<li><a href="#tabs-main"><?php _e( 'Main Settings', $this->plugin_slug  ); ?></a></li>
			<li><a href="#tabs-display"><?php _e( 'Appearance', $this->plugin_slug  ); ?></a></li>
			<li><a href="#tabs-bookingsettings"><?php _e( 'Booking Settings', $this->plugin_slug  ); ?></a></li>
			<li><a href="#tabs-codes"><?php _e( 'Codes', $this->plugin_slug ); ?></a></li>
			<li><a href="#tabs-mail"><?php _e( 'Mail Settings', $this->plugin_slug ); ?></a></li>
			<li><a href="#tabs-importexport"><?php _e( 'Import/Export', $this->plugin_slug ); ?></a></li>
		</ul>

		<div id="tabs-main">
			<?php

			$option_fields = array(
				'id' => $this->plugin_slug . '_options',
				'show_on' => array( 'key' => 'options-page', 'value' => array( $this->plugin_slug ), ),
				'show_names' => true,
				'fields' => array(
					array(
						'name' => __( 'Text', $this->plugin_slug ),
						'desc' => __( 'field description (optional)', $this->plugin_slug ),
						'id' => $this->plugin_slug . '_text',
						'type' => 'text',
					),
					array(
						'name' => __( 'Color Picker', $this->plugin_slug ),
						'desc' => __( 'field description (optional)', $this->plugin_slug ),
						'id' => $this->plugin_slug . '_colorpicker',
						'type' => 'colorpicker',
						'default' => '#ffffff'
					),
				),
			);

			cmb2_metabox_form( $option_fields, $this->plugin_slug . '-settings' );
			?>
		</div>
		<div id="tabs-display">
			<?php

			$option_fields = array(
				'id' => $this->plugin_slug . '_options_display',
				'show_on' => array( 'key' => 'options-page', 'value' => array( $this->plugin_slug ), ),
				'show_names' => true,
				'fields' => array(
					array(
				    'name'             => __( 'Items Page', $this->plugin_slug ),
				    'desc'             => __( 'Display list of items on this page', $this->plugin_slug ),
				    'id'               => $this->plugin_slug . '_item_page_select',
				    'type'             => 'select',
				    'show_option_none' => true,
				    'default'          => 'none',
				    'options'          => pages_dropdown(),
					),						
					array(
				    'name'             => __( 'Locations Page', $this->plugin_slug ),
				    'desc'             => __( 'Display list of Locations on this page', $this->plugin_slug ),
				    'id'               => $this->plugin_slug . '_location_page_select',
				    'type'             => 'select',
				    'show_option_none' => true,
				    'default'          => 'none',
				    'options'          => pages_dropdown(),
					),						
					array(
				    'name'             => __( 'Booking Review Page', $this->plugin_slug ),
				    'desc'             => __( 'Once you click "Book, you will be forwarded to this page', $this->plugin_slug ),
				    'id'               => $this->plugin_slug . '_bookingsubmit_page_select',
				    'type'             => 'select',
				    'show_option_none' => true,
				    'default'          => 'none',
				    'options'          => pages_dropdown(),
					),					
					array(
				    'name'             => __( 'Show support', $this->plugin_slug ),
				    'desc'             => __( 'Add a link to the Commons Booking Website.', $this->plugin_slug ),
				    'id'               => $this->plugin_slug . '_showsupport',
 						'type'  					 => 'checkbox',
					),
				),
			);

			cmb2_metabox_form( $option_fields, $this->plugin_slug . '-settings-display' );
			?>
		</div>
		<div id="tabs-bookingsettings">
			<?php

			$option_fields = array(
				'id' => $this->plugin_slug . '_options_bookingsettings',
				'show_on' => array( 'key' => 'options-page', 'value' => array( $this->plugin_slug ), ),
				'show_names' => true,
				'fields' => array(
					array(
						'name' => __( 'Maximum booking days', $this->plugin_slug ),
						'desc' => __( 'Users can only book this many days at once', $this->plugin_slug ),
						'id' => $this->plugin_slug . '_bookingsettings_maxdays',
						'type' => 'text_small',
						'default' => "3",
					),					
					array(
						'name' => __( 'Allow booking over closed days', $this->plugin_slug ),
						'desc' => __( 'Any number of closed days just counts as one booked day. <br>E.g. If you have a weekend specified as "closed" in the location editor, user will still be able book from friday till monday.', $this->plugin_slug ),
						'id' => $this->plugin_slug . '_bookingsettings_allowclosed',
						'type' => 'checkbox',
						'default' => '',
					),
				),
			);

			cmb2_metabox_form( $option_fields, $this->plugin_slug . '-settings-bookings' );
			?>
		</div>
		<div id="tabs-codes">
			<?php

			$option_fields_second = array(
				'id' => $this->plugin_slug . '_options-codes',
				'show_on' => array( 'key' => 'options-page', 'value' => array( $this->plugin_slug ), ),
				'show_names' => true,
				'fields' => array(
					array(
						'name' => __( 'Codes', $this->plugin_slug ),
						'desc' => __( 'Enter comma-seperated Codes here', $this->plugin_slug ),
						'id' => $this->plugin_slug . '_codes_pool',
						'type' => 'textarea',
					),
				),
			);

			cmb2_metabox_form( $option_fields_second, $this->plugin_slug . '-settings-codes' );
			?>

		</div>		
		<div id="tabs-mail">
			<?php

			$option_fields_second = array(
				'id' => $this->plugin_slug . '_options-mail',
				'show_on' => array( 'key' => 'options-page', 'value' => array( $this->plugin_slug ), ),
				'show_names' => true,
				'fields' => array(
					array(
						'name' => __( 'Email address', $this->plugin_slug ),
						'desc' => __( 'The reply to address (Make sure this exists)', $this->plugin_slug ),
						'id' => $this->plugin_slug . '_mail_confirmation_sender',
						'default' => __( 'recipient@domain.com', $this->plugin_slug ),
						'type' => 'text',
					),					
					array(
						'name' => __( 'Confirmation email subject', $this->plugin_slug ),
						'desc' => __( 'The subject of the confirmation Email. ', $this->plugin_slug ),
						'id' => $this->plugin_slug . '_mail_confirmation_subject',
						'default' => __( 'Your booking', $this->plugin_slug ),
						'type' => 'text',
					),						
					array(
						'name' => __( 'Confirmation email body', $this->plugin_slug ),
						'desc' => __( 'Write a nice introduction. The booking page will be attached. ', $this->plugin_slug ),
						'id' => $this->plugin_slug . '_mail_confirmation_body',
						'default' => __( 'Hello %username%, and thanks for booking. <br> ', $this->plugin_slug ),

						'type' => 'textarea',
					),
				),
			);

			cmb2_metabox_form( $option_fields_second, $this->plugin_slug . '-settings-mail' );
			?>

		</div>
		<div id="tabs-importexport" class="metabox-holder">
			<div class="postbox">
				<h3 class="hndle"><span><?php _e( 'Export Settings', $this->plugin_slug ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Export the plugin settings for this site as a .json file. This allows you to easily import the configuration into another site.', $this->plugin_slug ); ?></p>
					<form method="post">
						<p><input type="hidden" name="pn_action" value="export_settings" /></p>
						<p>
							<?php wp_nonce_field( 'pn_export_nonce', 'pn_export_nonce' ); ?>
							<?php submit_button( __( 'Export' ), 'secondary', 'submit', false ); ?>
						</p>
					</form>
				</div>
			</div>

			<div class="postbox">
				<h3 class="hndle"><span><?php _e( 'Import Settings', $this->plugin_slug ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Import the plugin settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', $this->plugin_slug ); ?></p>
					<form method="post" enctype="multipart/form-data">
						<p>
							<input type="file" name="pn_import_file"/>
						</p>
						<p>
							<input type="hidden" name="pn_action" value="import_settings" />
							<?php wp_nonce_field( 'pn_import_nonce', 'pn_import_nonce' ); ?>
							<?php submit_button( __( 'Import' ), 'secondary', 'submit', false ); ?>
						</p>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
