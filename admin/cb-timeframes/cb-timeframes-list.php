<?php
/**
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@wielebenwir.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

/**
 * Extend the Wordpress Table
 *
 * @package Commons_Booking_Timeframes_Table
 * @author  Florian Egermann <florian@wielebenwir.de>
 */

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Commons_Booking_Timeframes_Table class that will display our custom table
 * records in nice table
 */
class Commons_Booking_Timeframes_Table extends WP_List_Table
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
            'commons-booking'
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
            'edit' => sprintf('<a href="?page=cb_timeframes_edit&id=%s" class="button" style="visibility:visible">%s</a>', $item['id'], __('Edit', 'cb_timeframes_table')),
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
            'location_id' => __('Location', 'commons-booking'),
            'item_id' => __('Item', 'commons-booking'),
            'date_start' => __('Starting Date', 'commons-booking'),
            'date_end' => __('End Date', 'commons-booking'),
            'timeframe_title' => __('Note', 'commons-booking'),
            'edit_actions' => __('Edit', 'commons-booking'),
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

        $per_page = 30; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // get filters
        $filters = $this->get_selected_Filters(); 
        $sqlfilter = "";

        // construct Query 
        if (count($filters) > 0) { 
            // set query 
            $sqlfilter = 'WHERE ' . implode (' AND ', $filters);
        }

        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name $sqlfilter");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';
 
        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name $sqlfilter ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged * $per_page), ARRAY_A);

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
        echo '<select size=1 disabled></select>';
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
            echo __( 'Filter by: ', 'commons-booking' );  
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

    $table = new Commons_Booking_Timeframes_Table();
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {
        $message =  sprintf(__('Items deleted: %d', 'commons-booking'), count($_REQUEST['id']));
    }
    ?>

<div class="wrap">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php echo get_admin_page_title(); ?> <a class="add-new-h2"
                                 href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=cb_timeframes_edit');?>"><?php _e('Add new Timeframe', 'commons-booking')?></a>
    </h2>

    <?php new Admin_Table_Message ( $message, 'updated' ); ?>

    <form id="timeframes-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table->display(); ?>
    </form>
</div>

<?php } // end cb_timeframes_table_page_handler  ?>