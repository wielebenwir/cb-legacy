<?php
/**
 * Edit Form
 */

/**
 * Form page handler checks is there some data posted and tries to save it
 * Also it renders basic wrapper in which we are callin meta box render
 */
function cb_timeframes_table_form_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cb_timeframes'; // do not forget about tables prefix

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
        if ( wp_verify_nonce( $_REQUEST['nonce'], 'edit-timeframe' )) { // if nonce is correct
            // combine our default item with request params
            $item = shortcode_atts($default, $_REQUEST);
            // validate data, and if all ok save item to database
            // if id is zero insert otherwise update
            $item_valid = cb_timeframes_table_validate_entry($item);
            if ($item_valid === true) {
                if ($item['id'] == 0) {
                    $result = $wpdb->insert($table_name, $item);
                    $item['id'] = $wpdb->insert_id;
                    if ($result) {
                        $message = __('Item was successfully saved', 'cb_timeframes_table');
                    } else {
                        $notice = __('There was an error while saving item', 'cb_timeframes_table');
                    }
                } else {
                    $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                    if ($result) {
                        $message = __('Item was successfully updated', 'cb_timeframes_table');
                    } else {
                        $notice = __('There was an error while updating item', 'cb_timeframes_table');
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
                    $notice = __('Item not found', 'cb_timeframes_table');
                }
            }
        }

    // Editing Form
    add_meta_box('timeframes_form_meta_box', __('Edit'), 'cb_timeframes_table_form_meta_box_handler', 'timeframes_form_meta_box', 'normal', 'default');
    ?>
<div class="wrap">
       <?php new WP_Admin_Notice( __( 'Error Messages' ), 'error' ); ?>


    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <?php if (isset($_REQUEST['id'])) { ?> 
    <h2><?php echo ( '<strong>' . get_the_title($item['item_id']) . '</strong>: ' . __('Edit Timeframe', 'cb_timeframes_table') ); ?> <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=timeframes');?>"><?php _e('back to list', 'cb_timeframes_table')?></a>
    </h2>
    <?php } else { ?>
    <h2><?php _e('Add new Timeframe', 'cb_timeframes_table')?> <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=timeframes');?>"><?php _e('back to list', 'cb_timeframes_table')?></a>
    </h2>
    <?php } ?>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>
    <form id="timeframes-edit" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('edit-timeframe')?>"/>
        <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <?php /* And here we call our custom meta box */ ?>
                    <?php do_meta_boxes('timeframes_form_meta_box', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('Save', 'cb_timeframes_table')?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>

    <?php 
        if ($item['item_id']) {
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
function cb_timeframes_table_form_render_codes($item)
{

    $date_start = $item['date_start'];
    $date_end = $item['date_end'];
    $codes = new Commons_Booking_Codes_CSV ( $item['id'], $item['item_id'], $date_start, $date_end);

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


<table cellspacing="2" cellpadding="5" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="item_id"><?php _e('Item', 'cb_timeframes_table')?></label>
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
            <label for="location_id"><?php _e('Location', 'cb_timeframes_table')?></label>
        </th>
        <td>
          <?php cb_timeframes_table_edit_dropdown( 'cb_locations', 'location_id', esc_attr($item['location_id']) ); ?>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="date_start"><?php _e('Start Date', 'cb_timeframes_table')?></label>
        </th>
        <td>
            <input id="date_start" name="date_start" type="date" style="width: 95%" value="<?php echo esc_attr($item['date_start'])?>"
                   size="50" class="date" placeholder="<?php _e('Start Date', 'cb_timeframes_table')?>" required>
        </td>
    </tr>    
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="date_end"><?php _e('End Date', 'cb_timeframes_table')?></label>
        </th>
        <td>
            <input id="date_end" name="date_end" type="date" style="width: 95%" value="<?php echo esc_attr($item['date_end'])?>"
                   size="50" class="date" placeholder="<?php _e('End Date', 'cb_timeframes_table')?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="timeframe_title"><?php _e('Note', 'cb_timeframes_table')?></label>
        </th>
        <td>
            <input id="timeframe_title" name="timeframe_title" type="text" style="width: 95%" value="<?php echo esc_attr($item['timeframe_title'])?>"
                   size="50" class="code" placeholder="<?php _e('Note', 'cb_timeframes_table')?>" required>
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

    // @TODO validation
    $messages = array();

    // if (empty($item['name'])) $messages[] = __('Name is required', 'cb_timeframes_table');
    // if (!empty($item['email']) && !is_email($item['email'])) $messages[] = __('E-Mail is in wrong format', 'cb_timeframes_table');
    // if (!ctype_digit($item['age'])) $messages[] = __('Age in wrong format', 'cb_timeframes_table');
    //if(!empty($item['age']) && !absint(intval($item['age'])))  $messages[] = __('Age can not be less than zero');
    //if(!empty($item['age']) && !preg_match('/[0-9]+/', $item['age'])) $messages[] = __('Age must be number');
    //...

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
    echo '<option '. $new  . '>'. __(" – Please select – ") . '</option>';
    while ( $the_query->have_posts() ) {
      $the_query->the_post();
      $id = get_the_ID(); 
      if ( $id == $selected ) { $s = ' selected'; } else { $s = ''; }
      echo '<option value=' . $id . '"' . $s .' >' . get_the_title() . '</option>';
    }
    echo '</select>';
  } else {
   echo __( 'Something went wrong', $plugin_slug);
  }
  /* Restore original Post Data */
  wp_reset_postdata();
}

?>
