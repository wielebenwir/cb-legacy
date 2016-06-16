<?php 

/**
 * Commons Booking Booking Comments
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@wielebenwir.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de

 * @copyright 2015 wielebenwir
 */

class CB_Booking_Comments {

  public $booking_hash; 
  public $post_id;
  public $form_args;
  public $comments; 
  public $url;
  public $table_name;

  public function __construct( ) {

    global $wpdb;
    $this->table_name = $wpdb->prefix . 'cb_bookings'; 

  }


  public function set_booking_hash( $booking_hash ) {
    $this->booking_hash = $booking_hash;
  }  

  public function set_post_id( $post_id ) {
    $this->post_id = $post_id;
  }

  public function get_booking_comments( $item_id ) {

    global $wpdb;
    $return = array();
    $commentmeta_table = $wpdb->prefix . "commentmeta";
    $comments_table = $wpdb->prefix . "comments";

    // get booking_code-id from codes database
     $sqlresult = $wpdb->get_results($wpdb->prepare(
        "
        SELECT $this->table_name.hash, comment_content 
        FROM $this->table_name
        INNER JOIN $commentmeta_table
          ON $commentmeta_table.meta_value=$this->table_name.hash 
        INNER JOIN $comments_table
          ON $comments_table.comment_ID= $commentmeta_table.comment_id 
        WHERE comment_approved = 1 AND item_id = %s
        ", 
        $item_id), ARRAY_A); // get dates from 

     foreach ($sqlresult as $comments) {
       $return[ $comments['hash'] ] = $comments['comment_content'];
     }
     return $return;


  }

  public function render_comments_form() {

    $comments = $this->get_comments();

      ob_start(); 


    if ( $comments ) { // there is already a comment for this item
      
      $args = array (
        'comment' => $comments[0]->comment_content
        );
      cb_get_template_part('booking-review-booking-comment', $args);

    } else { // No comment, show form

      $this->url = add_query_arg( 'booking', $this->booking_hash, get_the_permalink() );

      $hash_field = '<input id="hash" name="hash" type="hidden" value="'.  $this->booking_hash. '" size="30" />';
      $redirect_field = '<input type="hidden" name="redirect_to" value="'. $this->url.'">';

      $comments_args = array(
          // change the title of send button 
          'label_submit'=>'Send',
          'logged_in_as' =>'', // Hide the logged in message
          // change the title of the reply section
          'title_reply'=> __('Leave a comment', 'commons-booking'),
          'title_reply_before' => '<div class="cb-headline">',
          'title_reply_after' => '</div>',
          'class_form' => 'cb-booking-comment',
          'id_submit'      => 'wp-submit-comment',
          // remove "Text or HTML to be displayed after the set of comment fields"
          'comment_notes_after' => '',
          // redefine your own textarea (the comment body)
          'comment_field' => '<textarea id="cb-comment-form" name="comment" aria-required="true"></textarea>' . $hash_field . $redirect_field,
      );

       comment_form( $comments_args, $this->post_id );

    }
      $content = ob_get_contents();
      ob_end_clean();

    return $content;


  }

  public function after_comment_redirect( $location ) {
      if ( isset( $_POST['my_redirect_to'] ) ) // Don't use "redirect_to", internal WP var
          $location = $_POST['my_redirect_to'];

      return $location;
  }



  public function add_comment_fields( $fields ) {

    // echo  $this->booking_hash .
    // '<input id="hash" name="hash" type="text" value="'.  $this->booking_hash. '" size="30" />';
  }

  public function get_comments( ) {
    $args = array(
      'meta_query' => array(
        array(
          'key'   => 'booking_hash',
          'value' => $this->booking_hash
        )
      )
    );
     
    $comments_query = new WP_Comment_Query;
    $comments       = $comments_query->query( $args );
     
    if( $comments ) :
      return $comments;
    endif;
  }


  public function save_comment_meta_data( $comment_id ) {

    if ( isset( $_POST['hash']) ) {
      add_comment_meta( $comment_id, 'booking_hash', $_POST['hash'], true );
    }
  
  }
}
  ?>