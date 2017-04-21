<?php
/**
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@macht-medien.de
 * @author    Christian Wenzel <christian@wielebenwir.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

/**
 * Admin messages
 *
 * @package Commons Booking Admin Messages
 * @author  Florian Egermann <florian@wielebenwir.de>
 */


class Admin_Table_Message {

    public $class = '';
    public $message = '';

    function __construct( $message, $class = 'updated' ){
        $this->class = $class;
        $this->message = $message;
        if ( !empty ( $this->message ) ) {
          $this->output();
        }
    }

    function output(){
        echo '<div id="message" class="' . $this->class .'"><p>' . $this->message . '</p></div>';
    }
}
?>