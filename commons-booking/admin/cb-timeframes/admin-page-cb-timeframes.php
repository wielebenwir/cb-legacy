<?php
/**
 * PART 1. Defining Custom Database Table
 * ============================================================================
 *
 * In this part you are going to define custom database table,
 * create it, update, and fill with some dummy data
 *
 * http://codex.wordpress.org/Creating_Tables_with_Plugins
 *
 * In case your are developing and want to check plugin use:
 *
 * DROP TABLE IF EXISTS wp_cte;
 * DELETE FROM wp_options WHERE option_name = 'cb_timeframes_table_install_data';
 *
 * to drop table and option
 * @TODO move table creation to install
 */



/**
 * $cb_timeframes_table_db_version - holds current database version
 * and used on plugin update to sync database tables
 */
global $cb_timeframes_table_db_version;
$cb_timeframes_table_db_version = '0.1'; // version changed from 1.0 to 0.1

/**
 * register_activation_hook implementation
 *
 * will be called when user activates plugin first time
 * must create needed database tables
 */
function cb_timeframes_table_install()
{
    global $wpdb;
    global $cb_timeframes_table_db_version;

    $table_name = $wpdb->prefix . 'cb_timeframes'; // do not forget about tables prefix

    // sql to create your table
    // NOTICE that:
    // 1. each field MUST be in separate line
    // 2. There must be two spaces between PRIMARY KEY and its name
    //    Like this: PRIMARY KEY[space][space](id)
    // otherwise dbDelta will not work
    $sql = "CREATE TABLE " . $table_name . " (
      id int(11) unsigned NOT NULL AUTO_INCREMENT,
      timeframe_title text,
      item_id int(11) DEFAULT NULL,
      location_id int(11) DEFAULT NULL,
      date_start date DEFAULT NULL,
      date_end date DEFAULT NULL,
      PRIMARY KEY (id)
    );";

    // we do not execute sql directly
    // we are calling dbDelta which cant migrate database
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // save current database version for later use (on upgrade)
    add_option('cb_timeframes_table_db_version', $cb_timeframes_table_db_version);

    /**
     * [OPTIONAL] Example of updating to 0.1 version
     *
     * If you develop new version of plugin
     * just increment $cb_timeframes_table_db_version variable
     * and add following block of code
     *
     * must be repeated for each new version
     * in version 0.1 we change email field
     * to contain 200 chars rather 100 in version 1.0
     * and again we are not executing sql
     * we are using dbDelta to migrate table changes
     */
    $installed_ver = get_option('cb_timeframes_table_db_version');
    if ($installed_ver != $cb_timeframes_table_db_version) {
        $sql = "CREATE TABLE " . $table_name . " (
          id int(11) unsigned NOT NULL AUTO_INCREMENT,
          timeframe_title text,
          item_id int(11) DEFAULT NULL,
          location_id int(11) DEFAULT NULL,
          date_start date DEFAULT NULL,
          date_end date DEFAULT NULL,
          PRIMARY KEY (id)
        );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // notice that we are updating option, rather than adding it
        update_option('cb_timeframes_table_db_version', $cb_timeframes_table_db_version);
    }
}

register_activation_hook(__FILE__, 'cb_timeframes_table_install');

/**
 * register_activation_hook implementation
 *
 * [OPTIONAL]
 * additional implementation of register_activation_hook
 * to insert some dummy data
 */
function cb_timeframes_table_install_data()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'cb_timeframes'; // do not forget about tables prefix

    $wpdb->insert($table_name, array(
        'timeframe_title' => 'Test'
    ));
}

register_activation_hook(__FILE__, 'cb_timeframes_table_install_data');

/**
 * Trick to update plugin database, see docs
 */
function cb_timeframes_table_update_db_check()
{
    global $cb_timeframes_table_db_version;
    if (get_site_option('cb_timeframes_table_db_version') != $cb_timeframes_table_db_version) {
        cb_timeframes_table_install();
    }
}

add_action('plugins_loaded', 'cb_timeframes_table_update_db_check');

/**
 * PART 2. Defining Custom Table List
 * ============================================================================
 *
 * In this part you are going to define custom table list class,
 * that will display your database records in nice looking table
 *
 * http://codex.wordpress.org/Class_Reference/WP_List_Table
 * http://wordpress.org/extend/plugins/custom-list-table-example/
 */

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * cb_timeframes_table_List_Table class that will display our custom table
 * records in nice table
 */
class cb_timeframes_table_List_Table extends WP_List_Table
{
    /**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => __( 'Timeframe' ),
            'plural' => __( 'Timeframes' ),

            $this->plugin_slug
        ));
    }

    /**
     * [REQUIRED] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    /**
     * [OPTIONAL] this is example, how to render specific column
     *
     * method name must be like this: "column_[column_name]"
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_location_id($item)
    {
      return '<strong> ' . get_the_title( $item['location_id'] ) . '</strong>';
    }    
    function column_item_id($item)
    {
      return '<strong>' .get_the_title( $item['item_id'] ). '</strong>';
    }

    /**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_edit_actions($item)
    {
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &person=2
        $actions = array(
            'edit' => sprintf('<a href="?page=timeframes_form&id=%s" class="button" style="visibility:visible">%s</a>', $item['id'], __('Edit', 'cb_timeframes_table')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s" class="button" style="visibility:visible">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'cb_timeframes_table')),
        );

        return $this->row_actions($actions);
    }

    /**
     * [REQUIRED] this is how checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    /**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'location_id' => __('Location', 'cb_timeframes_table'),
            'item_id' => __('Item', 'cb_timeframes_table'),
            'date_start' => __('Starting Date', 'cb_timeframes_table'),
            'date_end' => __('End Date', 'cb_timeframes_table'),
            'timeframe_title' => __('Note', 'cb_timeframes_table'),
            'edit_actions' => __('Edit', 'cb_timeframes_table'),
        );
        return $columns;
    }

    /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     */
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'location_id' => array('location_id', true),
            'item_id' => array('item_id', false),
            'date_start' => array('date_start', false),
            'date_end' => array('date_end', false),
            'id' => array('ID', false),
            'timeframe_title' => array('timeframe_title', false),
        );
        return $sortable_columns;
    }

    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    /**
     * [OPTIONAL] This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cb_timeframes'; // do not forget about tables prefix

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }
    /**
     * Define the filters 
     * @return array
     */
    public function filterDefinition() 
    {
     $filterDefinition = array ( 
        array ( 'name' => 'Items', 
                'filter' =>'item-filter', 
                'id' => 'item_id',
                'posttype' => 'cb_items'
                ),
        array ( 'name' => 'Locations', 
                'filter' =>'location-filter', 
                'id' => 'location_id',
                'posttype' => 'cb_locations'
                )
        );
     return $filterDefinition;
    }

    /**
     * Get the selected IDs 
     * @return array
     */
    public function get_selected_IDs() 
    {
        $fd = $this->filterDefinition();
        $selectedIDs = array();
        // check if defined, remove if not 
        foreach ($fd as $key => $subArray) {
            if (isset($_REQUEST[($subArray['filter'])]) && !empty($_REQUEST[($subArray['filter'])]) ) { // if $_REQUEST and Variable
                array_push ($selectedIDs, $_REQUEST[($subArray['filter'])]); 
            } 
        }
        return $selectedIDs;
    }
    /**
     * Get the Filters  
     * @return array
     */
    public function get_selected_Filters() 
    {
        $fd = $this->filterDefinition();
        $sqlfilter = '';
        $filterQuery = array();
        // check if defined, remove if not 
        foreach ($fd as $key => $subArray) {
            if (isset($_REQUEST[($subArray['filter'])]) && !empty($_REQUEST[($subArray['filter'])]) ) { // if $_REQUEST and Variable
                array_push ($filterQuery, $subArray['id'] . "=" .$_REQUEST[($subArray['filter'])]); 
            } 
        }   
        return $filterQuery;
    }

    /**
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     */
    public function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cb_timeframes'; // do not forget about tables prefix

        $per_page = 5; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';
 
        // get filters
        $filters = $this->get_selected_Filters(); 
        $sqlfilter = "";

        // construct Query 
        if (count($filters) > 0) { 
            // set query 
            $sqlfilter = 'WHERE ' . implode (' AND ', $filters);
        }


        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name $sqlfilter ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));


    } //  prepare_items()


    /**
    * Renders a dropdown filter menu
    *
    * @param Post Type
    * @return html dropdown
    */
    public function filterDropDown( $posttype ) {

        $selected = $this->get_selected_IDs();
        $filters = $this->filterDefinition(); 

        $key = array_search( $posttype, array_column( $filters, 'filter' ));

        $type = $filters[ $key ][ 'posttype' ];
        $name = $filters[ $key ][ 'name' ];
        $filter = $filters[ $key ][ 'filter' ];
        $args = array( 'posts_per_page' => -1, 'post_type' => $type );
        $the_query = new WP_Query( $args );
        if ( $the_query->have_posts() ) {

            echo '<select name="filterby-' . $name .'" size="1" class="filterby-'. $name .'">';
            echo '<option value="">-- ' . $name . ' --</option>';
         
            while ( $the_query->have_posts() ) {

            $the_query->the_post();
            $id = get_the_ID(); 

            if ( in_array( $id, $selected )) {  $s = ' selected '; } else { $s = ''; }
            echo '<option value="&' . $filter . "=". $id . '"' . $s .' >' . get_the_title() . '</option>';
        }
        echo '</select>';
      } else {
       echo __( 'Something went wrong', $plugin_slug);
      }
      /* Restore original Post Data */
      wp_reset_postdata();

    }


    /**
    * Overwrite Table Navigation 
    *
    * @param $tableNav
    * @return html dropdown
    */    

    function extra_tablenav( $which ) {

        global $wpdb;
        // echo( "<h2>--- ".$this->$testing."</h2>" );

        if ( $which == "top" ){
            $filters = $this->filterDefinition();
            echo (' <div class="tablefilters">' );
            echo __( 'Filter by: ');  
            $this->filterDropDown( 'location-filter' ); 
            $this->filterDropDown( 'item-filter' );
            echo ( '</div>' );
        }     
        if ( $which == "bottom" ){
            //The code that goes after the table is there
         } 
    }
    
    function add_tablenav( $var ) {
        // return $this->$myvar;
        echo ($var);

    }




}

/**
 * PART 3. Admin page
 * ============================================================================
 *
 * In this part you are going to add admin page for custom table
 *
 * http://codex.wordpress.org/Administration_Menus
 */

/**
 * admin_menu hook implementation, will add pages to list timeframes and to add new one 
 * @TODO: move menu creation to init/ restructure the menu 
 */
function cb_timeframes_table_admin_menu()
{
    add_submenu_page('cb_menu', __('Timeframes', 'cb_timeframes_table'), __('Timeframes', 'cb_timeframes_table'), 'activate_plugins', 'timeframes', 'cb_timeframes_table_page_handler');
    // add new will be described in next part
    add_submenu_page('timeframes', __('Add new', 'cb_timeframes_table'), __('Add new', 'cb_timeframes_table'), 'activate_plugins', 'timeframes_form', 'cb_timeframes_table_form_page_handler');
}

add_action('admin_menu', 'cb_timeframes_table_admin_menu');

/**
 * List page handler
 *
 * This function renders our custom table
 * Notice how we display message about successfull deletion
 * Actualy this is very easy, and you can add as many features
 * as you want.
 *
 * Look into /wp-admin/includes/class-wp-*-list-table.php for examples
 */
function cb_timeframes_table_page_handler()
{
    global $wpdb;

    $table = new cb_timeframes_table_List_Table();
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'cb_timeframes_table'), count($_REQUEST['id'])) . '</p></div>';
    }
    ?>
<div class="wrap">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('timeframes', 'cb_timeframes_table')?> <a class="add-new-h2"
                                 href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=timeframes_form');?>"><?php _e('Add new Timeframe', 'cb_timeframes_table')?></a>
    </h2>
    <?php echo $message; ?>

    <form id="timeframes-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table->display() ?>
    </form>

</div>
<?php
}

/**
 * PART 4. Form for adding andor editing row
 * ============================================================================
 *
 * In this part you are going to add admin page for adding andor editing items
 * You cant put all form into this function, but in this example form will
 * be placed into meta box, and if you want you can split your form into
 * as many meta boxes as you want
 *
 * http://codex.wordpress.org/Data_Validation
 * http://codex.wordpress.org/Function_Reference/selected
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
    if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        // combine our default item with request params
        $item = shortcode_atts($default, $_REQUEST);
        // validate data, and if all ok save item to database
        // if id is zero insert otherwise update
        $item_valid = cb_timeframes_table_validate_enty($item);
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

    // here we adding our custom meta box
    add_meta_box('timeframes_form_meta_box', __('Edit'), 'cb_timeframes_table_form_meta_box_handler', 'timeframes_form_meta_box', 'normal', 'default');

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Edit Timeframe', 'cb_timeframes_table')?> <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=timeframes');?>"><?php _e('back to list', 'cb_timeframes_table')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
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
</div>
<?php
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


<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="item_id"><?php _e('Item', 'cb_timeframes_table')?></label>
        </th>
        <td>
          <?php cb_timeframes_table_edit_dropdown( 'cb_items', 'item_id', esc_attr($item['item_id']) ); ?>
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
function cb_timeframes_table_validate_enty($item)
{
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
 * Do not forget about translating your plugin, use __('english string', 'your_uniq_plugin_name') to retrieve translated string
 * and _e('english string', 'your_uniq_plugin_name') to echo it
 * in this example plugin your_uniq_plugin_name == cb_timeframes_table
 *
 * to create translation file, use poedit FileNew catalog...
 * Fill name of project, add "." to path (ENSURE that it was added - must be in list)
 * and on last tab add "__" and "_e"
 *
 * Name your file like this: [my_plugin]-[ru_RU].po
 *
 * http://codex.wordpress.org/Writing_a_Plugin#Internationalizing_Your_Plugin
 * http://codex.wordpress.org/I18n_for_WordPress_Developers
 */
function cb_timeframes_table_languages()
{
    load_plugin_textdomain('cb_timeframes_table', false, dirname(plugin_basename(__FILE__)));
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

add_action('init', 'cb_timeframes_table_languages');
?>
