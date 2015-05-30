<?php

/*

 */

/**
 * The Class.
 */
class Commons_Booking_Items_Metabox {

  /**
   * Hook into the appropriate actions when the class is constructed.
   */
  public function __construct() {

    add_action('add_meta_boxes', array($this, 'cb_items_add_meta_box'));
    add_action('save_post', array($this, 'save'));
    add_action('the_content', array($this, 'custom_message'));
  }

  /**
   * Adds the meta box container.
   */
  public function cb_items_add_meta_box( $post_type ) {
    $post_types = array( 'cb_items' );

    //limit meta box to certain post types
    if (in_array($post_type, $post_types)) {
      add_meta_box('cs-meta',
      'Add Custom Message',
      array($this, 'cb_items_meta_box_function'),
      $post_type,
      'normal',
      'high');
    }
  }

  /**
   * Render Meta Box content.
   *
   * @param WP_Post $post The post object.
   */

  public function cb_items_meta_box_function($post) {

    // Add an nonce field so we can check for it later.
    wp_nonce_field('cs_nonce_check', 'cs_nonce_check_value');

    // Use get_post_meta to retrieve an existing value from the database.
    $custom_message = get_post_meta($post -> ID, '_cs_custom_message', true);

    // Display the form, using the current value.
    echo '<div style="margin: 10px 100px; text-align: center">';
    echo '<label for="custom_message">';
    echo '<strong><p>Add custom message to post</p></strong>';
    echo '</label>';
    echo '<textarea rows="3" cols="50" name="cs_custom_message">';
    echo esc_attr($custom_message);
    echo '</textarea>';
    echo '</div>';
  }

  /**
   * Save the meta when the post is saved.
   *
   * @param int $post_id The ID of the post being saved.
   */
  public function save($post_id) {

    /*
     * We need to verify this came from the our screen and with 
     * proper authorization,
     * because save_post can be triggered at other times.
     */

    // Check if our nonce is set.
    if (!isset($_POST['cs_nonce_check_value']))
      return $post_id;

    $nonce = $_POST['cs_nonce_check_value'];

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($nonce, 'cs_nonce_check'))
      return $post_id;

    // If this is an autosave, our form has not been submitted,
    //     so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
      return $post_id;

    // Check the user's permissions.
    if ('page' == $_POST['post_type']) {

      if (!current_user_can('edit_page', $post_id))
        return $post_id;

    } else {

      if (!current_user_can('edit_post', $post_id))
        return $post_id;
    }

    /* OK, its safe for us to save the data now. */

    // Sanitize the user input.
    $data = sanitize_text_field($_POST['cs_custom_message']);

    // Update the meta field.
    update_post_meta($post_id, '_cs_custom_message', $data);
  }

  public function custom_message($content) {
    global $post;
    //retrieve the metadata values if they exist
    $data = get_post_meta($post -> ID, '_cs_custom_message', true);
    if (!empty($data)) {
      $custom_message = "<div style='background-color: #FFEBE8;border-color: #C00;padding: 2px;margin:2px;font-weight:bold;text-align:center'>";
      $custom_message .= $data;
      $custom_message .= "</div>";
      $content = $custom_message . $content;
    }

    return $content;
  }

}