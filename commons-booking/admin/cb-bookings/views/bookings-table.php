<?php 
/**
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@macht-medien.de
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

/**
 * Handles the display of the Bookings Table
 *
 * @package Commons_Booking_Booking_Table
 * @author  Florian Egermann <florian@wielebenwir.de>
 */


global $wpdb;

$table = new Commons_Booking_Bookings_Table();
$table->prepare_items();

$message = '';
if ('delete' === $table->current_action()) {
    $message =  sprintf(__('Items deleted: %d', 'cb_timeframes_table'), count($_REQUEST['id']));
}
?>

<div class="wrap">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php echo get_admin_page_title(); ?></h2>

    <?php new Admin_Table_Message ( $message, 'updated' ); ?>

    <form id="timeframes-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table->display(); ?>
    </form>
</div>