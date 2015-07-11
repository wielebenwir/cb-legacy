<?php
class WP_Admin_Notice {

    public $class = '';
    public $message = '';

    function __construct( $message, $class = 'updated' ){
        $this->class = $class;
        $this->message = $message;
        add_action( 'admin_notices', array( $this, 'output' ) );
        add_action( 'network_admin_notices', array( $this, 'output' ) );
    }

    function output(){
        echo '<div id="message" class="' . $this->class .'"><p>' . $this->message . '</p></div>';
    }
}
