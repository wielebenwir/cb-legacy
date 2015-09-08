<?php

/**
 * Commons Booking
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@wielebenwir.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

/**
 * *
 * @package Commons_Booking
 * @author    Florian Egermann <florian@wielebenwir.de>
 */
class Commons_Booking {

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   0.0.1
     *
     * @var     string
     */
    const VERSION = '0.3';

    /**
     *
     *
     * The variable name is used as the text domain when internationalizing strings
     * of text. Its value should match the Text Domain file header in the main
     * plugin file.
     *
     * @since    0.0.1
     *
     * @var      string
     */
    static $plugin_slug = 'commons-booking';    
    /**
     *
     * Unique identifier for your plugin.
     *
     *
     * @since    0.0.1
     *
     * @var      string
     */
    protected static $Commons_Booking = 'Commons Booking';

    /**
     * Instance of this class.
     *
     * @since    0.0.1
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * Array of cpts of the plugin
     *
     * @since    0.0.1
     *
     * @var      object
     */
    protected $cpts = array( 'cb_items', 'cb_locations' );

    /**
     * Array of capabilities by roles
     * 
     * @since 0.0.1
     * 
     * @var array
     */
    protected static $plugin_roles = array( // @TODO: User Roles 
        // 'editor' => array(
        //     'edit_bookings' => true,
        //     'edit_others_bookings' => true,            
        //     'edit_items' => true,
        //     'edit_others_items' => true,            
        //     'edit_locations' => true,
        //     'edit_others_locations' => true,
        // ),
        // 'author' => array(
        //     'edit_bookings' => true,
        //     'edit_others_bookings' => false,
        // ),
        // 'subscriber' => array(
        //     'edit_bookings' => false,
        //     'edit_others_bookings' => false,
        // ),
    );

    /**
     * Initialize the plugin by setting localization and loading public scripts
     * and styles.
     *
     * @since     0.0.1
     */
    private function __construct() {
        // Load plugin text domain
        add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

        // Activate plugin when new blog is added
        add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );
        

        // Create all needed custom post types defined in class-commons-booking-cpt.php @TODO: find better place for this
        $type_items = new Commons_Booking_Items_CPT( $this->get_plugin_slug() );
        $type_items->register_taxonomy();
        $type_locations = new Commons_Booking_Locations_CPT( $this->get_plugin_slug() );

        $items = new Commons_Booking_Public_Items();
        $this->users = new Commons_Booking_Users();
        $this->settings = new Commons_Booking_Admin_Settings;

        // add CSS class
        add_filter( 'body_class', array( $this, 'add_cb_class' ), 10, 3 );

        add_filter( 'registration_redirect', 'cb_registration_redirect' );
        add_filter( 'register_url', array( $this, 'cb_register_url' ) );
        add_filter( 'login_url', array( $this, 'cb_user_url' ) );

        // show admin bar only for admins and editors
        if (!current_user_can('edit_posts')) {
            add_filter('show_admin_bar', '__return_false');
        }

        // Load public-facing style sheet and JavaScript.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_calendar_js_vars' ) );

        add_shortcode( 'cb_items', array( $this, 'item_shortcode' ) );


        /* 
         * Filter: Overwrite pages.
         */
        add_action( 'the_content', array( $this, 'cb_content' ) );  
    }

    /**
     *   Add main items list to page selected in settings
     *   Add bookings review to page selected in settings.
     * Add main plugin overview output to page selected in settings.
     *
     * @since    0.0.1
     *
     * @return    Mixed 
     */
    public function cb_content( $page_content ) {

        $settings_display = $this->settings->get('pages');

            if ( !empty( $settings_display[ 'item_page_select' ] ) && ( is_page( $settings_display[ 'item_page_select' ] ) ) ) {
                

                $items = new Commons_Booking_Public_Items;
                return  $items->items_render();
            
            } elseif ( !empty( $settings_display[ 'bookingconfirm_page_select' ] ) && ( is_page( $settings_display[ 'bookingconfirm_page_select' ] ) ) ) {

                $bookingpage = new Commons_Booking_Booking;
                return $bookingpage->render_bookingreview();

            } elseif ( !empty( $settings_display[ 'user_page_select' ] ) && ( is_page( $settings_display[ 'user_page_select' ] ) ) ) {

                $cb_user = new Commons_Booking_Users;
                return $cb_user->page_user();            

            } elseif ( !empty( $settings_display[ 'registration_page_select' ] ) && ( is_page( $settings_display[ 'registration_page_select' ] ) ) ) {

                $cb_user = new Commons_Booking_Users;
                // return $cb_user->custom_registration_function();
                return $cb_user->custom_registration_function();

            } elseif (  is_singular( 'cb_items' ) ) {                             

        $item_id = get_the_ID();
        $timeframes = new Commons_Booking_Data();
        return $page_content . $timeframes->render_item_single_timeframes($item_id) . $timeframes->show_booking_bar(); ;

            } else { 
                return $page_content;
            }
        }    

    public function show_dings( $c ) {


    }

    /**
     * Return the plugin slug.
     *
     * @since    0.0.1
     *
     * @return    Plugin slug variable.
     */
    public function get_plugin_slug() {
        return self::$plugin_slug;
    }    

    /**
     * Return the plugin name.
     *
     * @since    0.0.1
     *
     * @return    Plugin name variable.
     */
    public function get_Commons_Booking() {
        return self::$Commons_Booking;
    }

    /**
     * Return the version
     *
     * @since    0.0.1
     *
     * @return    Version const.
     */
    public function get_plugin_version() {
        return self::VERSION;
    }

    /**
     * Return the cpts
     *
     * @since    0.0.1
     *
     * @return    Cpts array
     */
    public function get_cpts() {
        return $this->cpts;
    }

    /**
     * Return an instance of this class.
     *
     * @since     0.0.1
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }
    /**
     * Return path to plugin base.
     *
     * @since     0.4.5
     *
     * @return    string    
     */
    public function get_plugin_dir() {

        $path = plugin_dir_path( __FILE__ ) . '../';
        return $path;
    }


    /**
     * Fired when the plugin is activated.
     *
     * @since    0.0.1
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Activate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       activated on an individual blog.
     */
    public static function activate( $network_wide ) {

        if ( function_exists( 'is_multisite' ) && is_multisite() ) {

            if ( $network_wide ) {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ( $blog_ids as $blog_id ) {

                    switch_to_blog( $blog_id );
                    self::single_activate();

                    restore_current_blog();
                }
            } else {
                self::single_activate();
            }
        } else {
            self::single_activate();
        }
    }

    /**
     * Fired when the plugin is deactivated.
     *
     * @since    0.0.1
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Deactivate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       deactivated on an individual blog.
     */
    public static function deactivate( $network_wide ) {

        if ( function_exists( 'is_multisite' ) && is_multisite() ) {

            if ( $network_wide ) {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ( $blog_ids as $blog_id ) {

                    switch_to_blog( $blog_id );
                    self::single_deactivate();

                    restore_current_blog();
                }
            } else {
                self::single_deactivate();
            }
        } else {
            self::single_deactivate();
        }
    }

    /**
     * Fired when a new site is activated with a WPMU environment.
     *
     * @since    0.0.1
     *
     * @param    int    $blog_id    ID of the new blog.
     */
    public function activate_new_site( $blog_id ) {

        if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
            return;
        }

        switch_to_blog( $blog_id );
        self::single_activate();
        restore_current_blog();
    }    

    /**
     * Get all blog ids of blogs in the current network that are:
     * - not archived
     * - not spam
     * - not deleted
     *
     * @since    0.0.1
     *
     * @return   array|false    The blog ids, false if no matches.
     */
    private static function get_blog_ids() {

        global $wpdb;

        // get an array of blog ids
        $sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

        return $wpdb->get_col( $sql );
    }

    /**
     * Fired for each blog when the plugin is activated.
     *
     * @since    0.0.1
     */
    public static function single_activate() {
        //Requirements Detection System - read the doc in the library file
        require_once( plugin_dir_path( __FILE__ ) . 'includes/requirements.php' );
        new Plugin_Requirements( self::$Commons_Booking, self::$plugin_slug, array(
            'WP' => new WordPress_Requirement( '3.9.0' ),
                ) );

        // install the database tables 
        $timeframe_table = new Commons_Booking_Timeframes_Setup;
        $timeframe_table->install();        
       
        $codes_table = new Commons_Booking_Codes_Setup;
        $codes_table->install();
       
        $bookings_table = new Commons_Booking_Bookings_Setup;
        $bookings_table->install();

        $settings = new Commons_Booking_Admin_Settings; 

        $p = self::$plugin_slug;

        // create the necessary pages 
        $item_page = create_page(__( 'Items', $p ), $p.'_item_page_select');
        $user_page = create_page(__( 'User Page', $p ), $p.'_user_page_select');
        $user_reg_page = create_page(__( 'User Registration', $p ), $p.'_registration_page_select');
        $user_login_page = create_page(__( 'Login', $p ), $p.'_login_page_select');
        $booking_confirm_page = create_page(__( 'Booking', $p ), $p.'_bookingconfirm_page_select');


        // insert the default settings array

        $defaults = array(
            $p. '-settings-pages' => array(
              $p.'_item_page_select' => $item_page,
              $p.'_user_page_select' => $user_page,
              $p.'_login_page_select' => $user_login_page,
              $p.'_registration_page_select' => $user_reg_page,
              $p.'_bookingconfirm_page_select' => $booking_confirm_page,
            ),
            $p.'-settings-bookings' => array(
              $p.'_bookingsettings_maxdays' => 3,
              $p.'bookingsettings_daystoshow' => 30,
              $p.'_bookingsettings_allowclosed' => ''
            ),   
             $p.'-settings-codes' => array(
          $p.'_codes_pool' => 'Til I Die, (I Saw Santa) Rockin Around the Christmas Tree, 409, 409, 4th of July, A Casual Look, A Day in the Life of a Tree, A Thing or Two, A Time to Live in Dreams, A Young Man Is Gone, Add Some Music to Your Day, Airplane, All Alone, All Dressed Up for School, All I Wanna Do, All I Want to Do, All I Want to Do, All Summer Long, All This Is That, Alley Oop, Amusement Parks U.S.A., And Your Dream Comes True, Angel Come Home, Anna Lee, the Healer, Arent You Glad, Arent You Glad, At My Window, Auld Lang Syne, Baby Blue, Back Home, Ballad of Ole Betsy, Barbara Ann, Barbara Ann, Barbara, Barbie, Barnyard Blues, Be Here in the Mornin, Be Still, Be True to Your School, Be True to Your School, Be with Me, Beach Boys Stomp, Beaches In Mind, Belles of Paris, Bells of Christmas, Better Get Back in Bed, Blue Christmas, Blueberry Hill, Bluebirds over the Mountain, Bluebirds over the Mountain, Boogie Woodie, Break Away, Brians Back, Bull Session with the Big Daddy, Busy Doin Nothin, Cabinessence, California Calling, California Dreamin, California Feelin, California Girls, California Girls, California Saga: Big Sur, California Saga: California, California Saga: The Beaks of Eagles, Cant Wait Too Long, Car Crazy Cutie, Carls Big Chance, Caroline, No, Caroline, No, Cassius Love vs. Sonny Wilson, Catch a Wave, Celebrate the News, Chasin the Sky, Cherry, Cherry Coupe, Child of Winter, Christmas Day, Christmas Time Is Here Again, Chug-A-Lug, Cindy, Oh Cindy, Come Go with Me, Cool, Cool Water, Cotton Fields (The Cotton Song), Country Air, County Fair, Crack at Your Love, Crocodile Rock, Cuckoo Clock, Cuddle Up, Custom Machine, Dance, Dance, Dance, Dance, Dance, Dance, Darlin, Darlin, Daybreak Over The Ocean, Deirdre, Dennys Drums, Devoted to You, Diamond Head, Ding Dang, Disney Girls, Do It Again, Do It Again, Do You Like Worms, Do You Remember?, Do You Wanna Dance?, Dont Back Down, Dont Go Near the Water, Dont Hurt My Little Sister, Dont Talk (Put Your Head on My Shoulder), Donâ€™t Worry Baby, Donâ€™t Worry Baby, Drive-In, East Meets West, Endless Harmony, Everyones in Love with You, Farmers Daughter, Feel Flows, Finders Keepers, Forever, Forever, Friends, Friends, From There To Back Again, Frosty the Snowman, Full Sail, Fun, Fun, Fun, Fun, Fun, Fun, Funky Pretty, Funky Pretty, Games Two Can Play, Getcha Back, Gettin Hungry, Girl Dont Tell Me, Girls on the Beach, God Only Knows, God Only Knows, Goin On, Goin South, Goin To The Beach, Good Time, Good Timin, Good to My Baby, Good Vibrations, Good Vibrations, Got to Know the Woman, Graduation Day, Guess Im Dumb, H. E. L. P. Is on the Way, Had to Phone Ya, Hang On to Your Ego, Happy Endings, Hawaii, Hawaii, He Come Down, Heads You Win - Tails I Lose, Help Me, Rhonda, Help Me, Rhonda, Help Me, Ronda, Here Comes the Night, Here Comes the Night, Here She Comes, Here Today, Heroes and Villains, Heroes and Villains, Hey, Little Tomboy, Hold On Dear Brother, Honkin Down the Highway, Hot Fun in the Summertime, How She Boogalooed It, Hully Gully, Hushabye, Hushabye, I Can Hear Music, I Can Hear Music, I Do Love You, I Do, I Get Around, I Get Around, I Just Got My Pay, I Just Wasnt Made for These Times, I Know Theres an Answer, I Love To Say Da-Da, I Should Have Known Better, I Wanna Pick You Up, I Was Made to Love Her, I Went to Sleep, Id Love Just Once to See You, Ill Be Home for Christmas, Ill Bet Hes Nice, Im Bugged at My Ol Man, Im So Lonely, Im So Young, Im the Pied Piper (instrumental), Im the Pied Piper, Im Waiting for the Day, In My Car, In My Room, In My Room, In the Back of My Mind, In the Parkin Lot, In the Still of the Night, Island Fever, Island Girl, Isnt It Time, Its a Beautiful Day, Its About Time, Its About Time, Its Gettin Late, Its Just a Matter of Time, Its OK, Its Over Now, Johnny B. Goode, Johnny Carson, Judy, Just Once in My Life, Keep an Eye on Summer, Keepin the Summer Alive, Keepin the Summer Alive, Kiss Me, Baby, Kokomo, Kokomo, Kona Coast, Lady Lynda, Lady Lynda, Lady, Lahaina Aloha, Lana, Land Ahoy, Lavender, Leaving This Town, Leaving This Town, Let Him Run Wild, Let the Wind Blow, Let the Wind Blow, Let Us Go on This Way, Lets Go Away For Awhile, Lets Go Trippin, Lets Go Trippin, Lets Put Our Hearts Together, Little Bird, Little Bird, Little Deuce Coupe, Little Deuce Coupe, Little Girl (Youre My Miss America), Little Honda, Little Pad, Little Saint Nick, Little Surfer Girl, Livin with a Heartache, Lonely Days, Lonely Sea, Long Promised Road, Long Promised Road, Long, Tall Texan, Lookin at Tomorrow (A Welfare Song), Loop De Loop (Flip Flop Flyin In An Aeroplane), Louie, Louie, Love Is a Woman, Love Surrounds Me, Luau, Magic Transistor Radio, Make It Big, Make It Good, Male Ego, Mama Says, Marcella, Marcella, Match Point of Our Love, Maybe I Dont Know, Meant for You, Melekalikimaka, Merry Christmas, Baby, Misirlou, Mona Kona, Mona, Monster Mash, Moon Dawg, Morning Christmas, Mother May I, Mountain of Love, Mt. Vernon and Fairway (Theme), My Diane, My Love Lives On, Never Learn Not to Love, No-Go Showboat, Noble Surfer, Oh Darlin, Old Folks at Home (Swanee River), Only with You, Only With You, Our Car Club, Our Favorite Recording Sessions, Our Prayer, Our Sweet Love, Our Team, Pacific Coast Highway, Palisades Park, Papa-Oom-Mow-Mow, Papa-Oom-Mow-Mow, Passing By, Passing Friend, Peggy Sue, Pet Sounds, Pitter Patter, Please Let Me Wonder, Pom Pom Play Girl, Private Life Of Bill And Sue, Problem Child, Punchline, Radio King Dom, Rock n Roll to the Rescue, Rock and Roll Music, Rock and Roll Music, Roller Skating Child, Ruby Baby, Runaway, Sail On, Sailor, Sail On, Sailor, Sail Plane Song, Salt Lake City, San Miguel, Santa Ana Winds, Santa Claus Is Comin To Town, Santas Beard, Santas Got an Airplane, School Day (Ring! Ring! Goes The Bell), School Days, Sea Cruise, She Believes in Love Again, She Knows Me Too Well, Shes Goin Bald, Shes Got Rhythm, Shelter, Sherry She Needs Me, Shortenin Bread, Shut Down, Shut Down, Shut Down, Part II, Side Two, Slip On Through, Sloop John B, Sloop John B, Slow Summer Dancing (One Summer Night), Solar System, Some of Your Love, Somewhere Near Japan, Soul Searchin, Soulful Old Man Sunshine, Sounds of Free, South Bay Surfer, Spirit of America, Spring Vacation, Steamboat, Still Cruisin, Still I Dream of It, Still Surfin, Stoked, Strange Things Happen, Strange World, Student Demonstration Time, Sumahama, Summer in Paradies, Summer in Paradise, Summer Means New Love, Summer of Love, Summers Gone, Summertime Blues, Sunshine, Surf Jam, Surfs Up, Surfer Girl, Surfer Girl, Surfers Rule, Surfin Safari, Surfin U.S.A., Surfin U.S.A., Surfin, Surfin, Susie Cincinnati, Sweet Sunday Kinda Love, T M Song, Take a Load Off Your Feet, Talk to Me, Tears in the Morning, Tell Me Why, Ten Little Indians, That Same Song, Thats Not Me, Thats Why God Made the Radio, The Baker Man, The Girl from New York City, The Letter (The Box Tops song), The Letter, The Little Girl I Once Knew, The Little Old Lady from Pasadena, The Lords Prayer, The Monkeys Uncle, The Surfer Moon, The Times They Are A-Changin, The Trader, The Trader, The Wanderer, The Warmth of the Sun, Their Hearts Were Full of Spring, Their Hearts Were Full of Spring, Then I Kissed Her, Theres No Other (Like My Baby), Things We Did Last Summer, Think About The Days, This Car of Mine, This Whole World, Time to Get Alone, Transcendental Meditation, Trombone Dixie, Tune X, Under the Boardwalk, Unreleased Backgrounds, Vegetables, Vegetables, Wake the World, Wake the World, Walk On By, We Got Love, We Three Kings of Orient Are, Well Run Away, Were Together Again, Wendy, What Is a Young Girl Made Of?, Whatd I Say, When a Man Needs a Woman, When Girls Get Together, When I Grow Up (To Be a Man), Where I Belong, Where Is She?, Whistle In, White Christmas, Why Do Fools Fall In Love, Why Dont They Let Us Fall in Love, Why, Wild Honey, Wild Honey, Wind Chimes, Winds of Change, Winter Symphony, Wipe Out, With a Little Help from My Friends, With Me Tonight, Wonderful, Wonderful, Wontcha Come Out Tonight, Wouldnt It Be Nice To Live Again, Wouldnt It Be Nice, You Need a Mess of Help to Stand Alone, You Still Believe in Me, Youre So Good to Me, Youre Still A Mystery, Youre Welcome, Youre with Me Tonight, Youve Got to Hide Your Love Away, Youve Lost That Lovin Feelin, Your Summer Dream, Chapel of Love, Da Doo Ron Ron, Fall Breaks and Back to Winter (Woody Woodpecker Symphony), Graduation Day, Honky Tonk, Lady Liberty,'
        ),
        $p.'-settings-messages' => array(
          $p.'_messages_booking_pleaseconfirm' => __( '<p> Please review your booking and click "confirm".</p>', $p ),
          $p.'_messages_booking_confirmed' => __( '<h2>Congratulations, {{USER_NAME}}!</h2> <p>You´ve successfully booked {{ITEM_NAME}}. An email has been sent to your address {{USER_EMAIL}}. </p>', $p ),
          $p.'_messages_booking_canceled' => __( '<h2>Your booking has been canceled!</h2><p>Thanks for letting us know.</p>', $p ),
        ),         
        $p.'-settings-mail' => array(
          $p.'_mail_confirmation_sender' => 'recipient@domain.com',
          $p.'_mail_confirmation_subject' => __( 'Your booking', $p ),
          $p.'_mail_confirmation_body' => __('<h2>Hi {{USER_NAME}}, thanks and for booking {{ITEM_NAME}}!</h2>

              <p>Click here to see or cancel you booking: {{URL}}.</p>

              <p>Here´s your booking code: <strong>{{CODE}}</strong></p>

              <h3>Pick up information</h3>

              <em>Please make sure you are on time.</em>

              <p>Pick up {{ITEM_NAME}} at {{LOCATION_NAME}} on {{DATE_START}}.<br>
              Return it there on {{DATE_END}}.<br>
              Address: {{LOCATION_ADDRESS}}<br>
              Opening hours: {{LOCATION_OPENINGHOURS}}.</p>

              <h3>Your information</h3>

              <em>Please make sure you have entered the correct name and adress from your ID - otherwise you will not be able to pick up the item</em>

              <p>Name: {{USER_NAME}}.<br>
                Address: {{USER_ADDRESS}}</p>

              <p>Thanks, the Team. </p>
            ', $p ),
          $p.'_mail_registration_subject' => __( 'Welcome, {{USER_NAME}} – here´s your account information.', $p ),
          $p.'_mail_registration_body' => __( '<h2>Hi {{USER_NAME}}, thanks for registering!</h2>

              <p>You can sign in with the following: </p>

              <p>Username: <strong>{{USER_NAME}}</strong></p>
              <p>Password: <strong>{{PASSWORD}}</strong></p>

              <h3>Your information</h3>

              <p>Name: {{FIRST_NAME}} {{LAST_NAME}}</p>
              <p>Address: {{ADDRESS}}</p>
              <p>Phone: {{PHONE}}</p>

              <p>Thanks, the Team. </p>
            ', $p ),
          ),
        );


        // check if setting is set, otherwise set it. 
        $settings->set_defaults( $defaults );


        //Clear the permalinks
        flush_rewrite_rules();
    }

    /**
     * Fired for each blog when the plugin is deactivated.
     *
     * @since    0.0.1
     */
    private static function single_deactivate() {
        
        //Clear the permalinks
        flush_rewrite_rules();
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    0.0.1
     */
    public function load_plugin_textdomain() {
        $domain = $this->get_plugin_slug();
        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

        load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
        load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
    }

    /**
     * Register and enqueue public-facing style sheet.
     *
     * @since    0.0.1
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->get_plugin_slug() . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
        wp_enqueue_style( $this->get_plugin_slug() . '-plugin-calendar', plugins_url( 'assets/css/commons-booking.css', __FILE__ ), array(), self::VERSION );

        if ( is_singular ( 'cb_items' )) {
            wp_enqueue_style( $this->get_plugin_slug() . '-tooltip-css', plugins_url( 'assets/css/tooltipster.css', __FILE__ ), array(), self::VERSION );
        }       
    }

    /**
     * Register and enqueues public-facing JavaScript files.
     *
     * @since    0.0.1
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->get_plugin_slug() . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
        if ( is_singular ( 'cb_items' )) {
            wp_enqueue_script( $this->get_plugin_slug() . '-tooltip-lib', plugins_url( 'assets/js/jquery.tooltipster.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
        }
    }
    /**
     * For calendar page: Print the PHP vars in the HTML of the frontend for access by JavaScript @TODO use settings api
     *
     * @since    0.0.1
     */
    public function enqueue_calendar_js_vars() {

        $s_bookings = get_option( $this->get_plugin_slug() . '-settings-bookings' ); 
        $maxdays = $s_bookings[ $this->get_plugin_slug() . '_bookingsettings_maxdays'];
        
        $s_display = get_option( $this->get_plugin_slug() . '-settings-pages' ); 
        $bookingpage = get_permalink ( $s_display[ $this->get_plugin_slug() . '_bookingconfirm_page_select'] );

        $allowclosed = 0; // weird bug with checkbox in cmb2: if not set, the key is not in the array. 
        if ( isset( $s_bookings[ $this->get_plugin_slug() . '_bookingsettings_allowclosed']) ) {
            $allowclosed = 1;
        }
        

        wp_localize_script( $this->get_plugin_slug() . '-plugin-script', 'cb_js_vars', array(
            'setting_maxdays' => $maxdays,
            'setting_booking_review_page' => $bookingpage,
            'setting_allowclosed' => $allowclosed,
            'text_start_booking' => __( 'Book here:', $this->get_plugin_slug() ),
            'text_choose' => __( 'Click pickup and return date(s):', $this->get_plugin_slug() ),
            'text_pickup' => __( 'Pickup date:', $this->get_plugin_slug() ),
            'text_return' => __( 'Return date:', $this->get_plugin_slug() ),
            'text_pickupreturn' => __( 'Pickup and return date:', $this->get_plugin_slug() ),
            'text_error_days' => __( 'Sorry, To many days between pickup and return, the maximum is: ', $this->get_plugin_slug() ),
            'text_error_timeframes' => __( 'Sorry, you can only book at one station.', $this->get_plugin_slug() ),
            'text_error_notbookable' => __( 'Sorry, this day is not bookable.', $this->get_plugin_slug() ),
            'text_error_bookedday' => __( 'Sorry, there must be no booked day between pickup and return.', $this->get_plugin_slug() ),
            'text_error_closedforbidden' => __( 'Sorry, you can´t book over closed days.', $this->get_plugin_slug() )
                )
        );
    }

    /**
     * Add class in the body on the frontend
     *
     * @since    0.0.1
     */
    public function add_cb_class( $classes ) {
        $classes[] = $this->get_plugin_slug();
        return $classes;
    }

    /**
     * Example for override the template system on the frontend @TODO: Cleanup
     *
     * @since    0.0.1
     */
    public function load_content_demo( $original_template ) {
        if ( is_singular( 'demo' ) && in_the_loop() ) {
            return pn_get_template_part( 'content', 'demo', false );
        } else {
            return $original_template;
        }
    }
   
    /**
     * NOTE:  Actions are points in the execution of a page or process
     *        lifecycle that WordPress fires.
     *
     *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
     *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
     *
     * @since    0.0.1
     */
    public function action_method_name() {
        // @TODO: Define your action hook callback here
    }

    /**
     * Redirect: Registration page
     *
     * @since    0.2
     */
    public function cb_registration_redirect() {

       $id = $this->settings->get('pages', 'user_page_select');
       $url = get_permalink( $id );
       return $url;
    }     
    /**
     * Redirect: User page
     *
     * @since    0.2
     */
    public function cb_user_url() {
       $id = $this->settings->get('pages', 'user_page_select');
       $url = get_permalink( $id );
       return $url;
    }    
    /**
     * Redirect: After registration
     *
     * @since    0.2
     */
    public function cb_register_url() {

       $id = $this->settings->get('pages', 'registration_page_select');
       $url = get_permalink( $id );
       return $url;
    }

    /**
     * NOTE:  Shortcode simple set of functions for creating macro codes for use
     *        in post content.
     *
     *        Reference:  http://codex.wordpress.org/Shortcode_API
     *
     * @since    1.0.0
     */
    function item_shortcode( $atts ) {
        $a = shortcode_atts( array(
            'p' => '',
            'cat' => '',
            'posts_per_page' => 10, 
            'post_type' => 'cb_items', 
            'orderby' => 'title', 
            'order' => 'DESC'
        ), $atts );

        $items = new Commons_Booking_Public_Items;
        return  $items->output( $a );
    }

}
