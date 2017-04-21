<?php
/**
 * Edit Form
 */

/**
 * Form page handler checks is there some data posted and tries to save it
 * Also it renders basic wrapper in which we are callin meta box render
 */
function cb_timeframes_table_form_page_handler( )
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cb_timeframes'; 

    $message = '';
    $notice = '';

    // this is default $item which will be used for new records
    $default = array(
        'id' => 0,
        'timeframe_title' => '',
        'item_id' => null,
        'location_id' => '',
        'date_start' => '',
        'date_end' => '',
    );

    // here we are verifying does this request is post back and have correct nonce
        if ( isset( $_REQUEST['timeframe_nonce']) && wp_verify_nonce( $_REQUEST['timeframe_nonce'], 'edit-timeframe' )) { // if nonce is correct
            // combine our default item with request params
            $item = shortcode_atts($default, $_REQUEST);
            // validate data, and if all ok save item to database
            // if id is zero insert otherwise update
            $item_valid = cb_timeframes_table_validate_entry( $item );
            if ($item_valid === true) {
                if ($item['id'] == 0) {
                    $result = $wpdb->insert($table_name, $item);
                    $item['id'] = $wpdb->insert_id;
                    if ($result) {
                        new Admin_Table_Message ( __('Item saved', 'commons-booking'), 'updated' );
                        $codes = new Commons_Booking_Codes_Generate;
                        $codes->generate_codes( $item['id'] );

                    } else {
                        new Admin_Table_Message ( __('There was an error while saving item', 'commons-booking'), 'error' );
                    }
                } else {
                    $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                    if ($result) {
                        new Admin_Table_Message( __('Timeframe updated.', 'commons-booking'), 'updated' );
                        $codes = new Commons_Booking_Codes_Generate;
                        $codes->generate_codes( $item['id'] );

                    } else { // nothing changed (-> Codes were already generated and no form-field input was changed.). We didn´t do anything, but send a happy message. 
                         new Admin_Table_Message ( __('Nothing changed.', 'commons-booking'), 'updated' );
                    }
                }
            } else {
                // if $item_valid not true it contains error message(s)
                $notice = $item_valid;
            }
        }
        else {
            // if this is not post back we load item to edit or give new one to create
            $item = $default;
            if (isset($_REQUEST['id'])) {
                $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
                if (!$item) {
                    $item = $default;
                    $notice = __('Item not found', 'commons-booking');
                }
            }
        }

    // Editing Form
    add_meta_box(
        'timeframes_form_meta_box', 
        __( 'Edit timeframe', 'commons-booking' ), 
        'cb_timeframes_table_form_meta_box_handler', 
        'timeframes_form_meta_box', 
        'normal', 
        'default');
    ?>
<div class="wrap">
<style type="text/css" media="print">
    .metabox-holder,
    .hidden-if-print,
    #adminmenumain,
    #wpfooter
     {display: none !important;}
    #wpcontent {
        margin-left: 0;
        display: block;
    }
    td {
        border-top: 1px solid #000;
    }
    
</style>
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>

    <?php
    if (isset($_REQUEST['id'])) { ?>  
        <h2 class="hidden-if-print"><?php echo  __('Edit Timeframe', 'commons-booking'); ?><a class="add-new-h2" href="<?php echo get_edit_post_link($item['item_id']); ?>"><?php printf ( __('Return to %s', 'commons-booking'), get_the_title($item['item_id'] ) ); ?></a> <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=cb_timeframes');?>"><?php _e('All timeframes', 'commons-booking')?></a>  
        </h2>
        <h1><?php printf ( '%s at %s',
            get_the_title( $item['item_id'] ),
            get_the_title( $item['location_id'], 'commons-booking' )
            ); ?></h1>        
        <h2><?php printf ( '%s %s – %s',
            $item['timeframe_title'],
            date_i18n( get_option( 'date_format' ), strtotime( $item['date_start'] ) ),
            date_i18n( get_option( 'date_format' ), strtotime( $item['date_end'] ) )
            ); ?></h2>


    <?php } else { ?>
        <h2><?php _e('Add new Timeframe', 'commons-booking')?> <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=cb_timeframes');?>"><?php _e('All timeframes', 'commons-booking')?></a>
        </h2>
    <?php } ?>
    <?php 

    // Display Messages if any
    if (!empty($notice)) {
        new Admin_Table_Message ( $notice, 'error' );
    }
    if (!empty($message)) {
        new Admin_Table_Message ( $message, 'updated' );
    }
    ?>
    <form id="timeframes-edit" method="POST">
        <?php wp_nonce_field( 'edit-timeframe', 'timeframe_nonce' ); ?>
        <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <?php /* And here we call our custom meta box */ ?>
                    <?php do_meta_boxes('timeframes_form_meta_box', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('Save & generate codes', 'commons-booking')?>" id="submit" class="button-primary" name="submit">
                    <input type="button" value="<?php _e('Print codes', 'commons-booking'); ?>"  onClick="window.print()" id="print" class="button-primary">
                </div>
            </div>
        </div>
    </form>

    <?php 
        if ( isset($_REQUEST['id']) ) {
            cb_timeframes_table_form_render_codes($item);
        }
         ?>
</div>
<?php
}

/**
 * This function renders codes meta box 
 * $item is row
 *
 * @param $item
 */
function cb_timeframes_table_form_render_codes($timeframe)
{
    
    // define date start
    if ( isset($_REQUEST['date_start'])) {
        $date_start = $_REQUEST['date_start'];
    } else {
        $date_start = $timeframe['date_start'];
    }


    // define date end
    if ( isset($_REQUEST['date_end'])) {
        $date_end = $_REQUEST['date_end'];
    } else {
        $date_end = $timeframe['date_end'];
    }


    // $codes = new Commons_Booking_Codes ( $timeframe['id'], $timeframe['item_id'], $date_start, $date_end);
    $codes = new Commons_Booking_Codes ( $timeframe['item_id'] );
    $codes->set_timeframe( $timeframe['id'], $date_start, $date_end );

    $codes->compare();
    $codes->render();

}


/**
 * This function renders our custom meta box
 * $item is row
 *
 * @param $item
 */
function cb_timeframes_table_form_meta_box_handler($item)
{
    ?>


<table cellspacing="2" cellpadding="5" class="form-table cmb_metabox">
    <tbody class="cmb2-wrap">
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="item_id"><?php _e('Item', 'commons-booking')?></label>
        </th>
        <td>
          <?php 
            // if you came here from the item edit screen, pre-populate the item value
            $setItem =  esc_attr($item['item_id']);
            if ( isset( $_GET['new'] )) {
                 $setItem = $_GET['item_id'];
                } 
          cb_timeframes_table_edit_dropdown( 'cb_items', 'item_id', $setItem ); 

          ?>
        </td>
    </tr>    
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="location_id"><?php _e('Location', 'commons-booking')?></label>
        </th>
        <td>
          <?php cb_timeframes_table_edit_dropdown( 'cb_locations', 'location_id', esc_attr($item['location_id']) ); ?>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="date_start"><?php _e('Start Date', 'commons-booking')?></label>
        </th>
        <td>
            <input id="date_start" name="date_start" type="text"  style="width: 95%" value="<?php echo esc_attr($item['date_start'])?>"
                   size="50" class="cb-datepicker" placeholder="<?php _e('Start Date', 'commons-booking')?>" required>
        </td>
    </tr>    
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="date_end"><?php _e('End Date', 'commons-booking')?></label>
        </th>
        <td>
            <input id="date_end" name="date_end" type="text" style="width: 95%" value="<?php echo esc_attr($item['date_end'])?>"
                   size="50" class="cb-datepicker" placeholder="<?php _e('End Date', 'commons-booking')?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="timeframe_title"><?php _e('Timeframe Title', 'commons-booking')?></label>
        </th>
        <td>
            <input id="timeframe_title" name="timeframe_title" type="text" style="width: 95%" value="<?php echo esc_attr($item['timeframe_title'])?>"
                   size="50" class="code" placeholder="<?php _e('Timeframe title', 'commons-booking')?>">
        </td>
    </tr>
    </tbody>
</table>
<?php
}

/**
 * Simple function that validates data and retrieve bool on success
 * and error message(s) on error
 *
 * @param $item
 * @return bool|string
 */
function cb_timeframes_table_validate_entry($item)
{

    $messages = array();

    if ( $item['location_id'] == '-1' ) $messages[] = __('Location is required' );
    if ( $item['item_id'] == '-1' ) $messages[] = __('Item is required' );
    if ( !is_valid_date( $item['date_start']) ) $messages[] = __('Start Date is not valid' );
    if ( !is_valid_date( $item['date_end']) ) $messages[] = __('End Date is not valid' );


    if (empty($messages)) return true;
    return implode('<br />', $messages);
}


/**
* Renders a dropdown menu for items and locations
*
* @param @TODO
* @return html dropdown
*/
function cb_timeframes_table_edit_dropdown( $posttype, $fieldname, $selected ) {

  $args = array( 'posts_per_page' => -1, 'post_type' => $posttype );
  $the_query = new WP_Query( $args );

  if ( $the_query->have_posts() ) {
    echo '<select name="' . $fieldname .'" size="1" class="'. $fieldname .'">';
    if (!$selected) { $new = "selected disabled"; } else { $new = ""; } // if new entry, set pre-selected 
    while ( $the_query->have_posts() ) {
      $the_query->the_post();
      $id = get_the_ID(); 
      if ( $id == $selected ) { $s = ' selected'; } else { $s = ''; }
      echo '<option value="' . $id . '"' . $s .' >' . get_the_title() . '</option>';
    }
    echo '</select>';
  } else {
   echo __( 'None found. Please create one.', 'commons-booking'); 
  }
  /* Restore original Post Data */
  wp_reset_postdata();
}

?>
