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
 * Settings module: Interaction with the Wordpress Admin Settings API 
 *
 * @package CB_Admin_Settings
 * @author  Florian Egermann <florian@wielebenwir.de>
 */

class CB_Admin_Settings extends Commons_Booking {

  public $prefix;
  public $setting_page;
  public $setting_name;


/**
 * Constructor & Defaults
 */
  public function __construct() {

    $this->prefix = parent::$plugin_slug;
    $this->defaults = array();

  }
  /**
  * Set the default values for the settings. 
  * Loop through each setting, if set, keep it otherwise write defaults to wp_options 
  */
  public function set_defaults( $item_page, $user_bookings_page, $booking_confirmed_page, $booking_review_page  ) {

    $this->item_page_id = $item_page;
    $this->user_bookings_page_id = $user_bookings_page;
    $this->booking_confirmed_page_id = $booking_confirmed_page;
    $this->booking_review_page_id = $booking_review_page;

    // Default Settings
    $this->defaults = array(
        $this->prefix. '-settings-pages' => array(
          $this->prefix.'_theme_select' => 'standard',
          $this->prefix.'_item_page_select' => $this->item_page_id,
          $this->prefix.'_user_bookings_page_select' => $this->user_bookings_page_id,
          $this->prefix.'_booking_confirmed_page_select' => $this->booking_confirmed_page_id, 
          $this->prefix.'_booking_review_page_select' => $this->booking_review_page_id, 
          $this->prefix.'_termsservices_url' => '' 
        ),
        $this->prefix.'-settings-bookings' => array(
          $this->prefix.'_bookingsettings_maxdays' => 3,
          $this->prefix.'_bookingsettings_daystoshow' => 30,
          $this->prefix.'_bookingsettings_allowclosed' => '',
          $this->prefix.'_bookingsettings_daystoshow' => 30,
          $this->prefix.'_bookingsettings_closeddayscount' => '',
          $this->prefix.'_bookingsettings_calendar_render_daynames' => '',
          $this->prefix.'_bookingsettings_allow_comments' => ''
        ),   
         $this->prefix.'-settings-codes' => array(
      $this->prefix.'_codes_pool' => 'African Flower, Afro Blue, Afternoon In Paris, Água De Beber, Airegin, Alfie , Alice In Wonderland, All Blues, All By Myself, All Of Me, All Of You, All The Things You Are, Alright, Okay, You Win, Always, Ana Maria, Angel Eyes, Anthropology, Apple Honey, April In Paris, April Joy, Arise, Her Eyes, Armageddon, Au Privave, Autumn In New York, Autumn Leaves, Beautiful Love, Beauty And The Beast, Bessies Blues, Bewitched, Big Nick, Black Coffee, Black Diamond, Black Narcissus, Black Nile, Black Orpheus, Blue Bossa, Blue In Green, Blue Monk, The Blue Room, Blue Train, Blues For Alice, Bluesette, Body And Soul, Boplicity, Bright Size Life, Broad Way Blues, Broadway, But Beautiful, Butterfly, Byrd Like, Cest Si Bon, Call Me, Call Me Irresponsible, Cant Help Lovin Dat Man, Captain Marvel, Central Park West, Ceora, Chega De Saudade, Chelsea Bells, Chelsea Bridge, Cherokee, Cherry Pink And Apple Blossom White, A Child Is Born, Chippie, Chitlins Con Carne, Come Sunday, Como En Vietnam, Con Alma, Conception, Confirmation, Contemplation, Coral, Cotton Tail, Could It Be You, Countdown, Crescent, Crystal Silence, D Natural Blues, Daahoud, Dancing On The Ceiling, Darn That Dream, Day Waves, Days And Nights Waiting, Dear Old Stockholm, Dearly Beloved, Dedicated To You, Deluge, Desafinado, Desert Air, Detour Ahead, Dexterity, Dizzy Atmosphere, Django, Doin The Pig, Dolores, Dolphin Dance, Domino Biscuit, Dont Blame Me, Dont Get Around Much Anymore, Donna Lee, Dream A Little Dream Of Me, Dreamsville, Easter Parade, Easy Living, Easy To Love, Ecclusiastics, Eighty One, El Gaucho, Epistrophy, Equinox, Equipoise, E.S.P., Fall, Falling Grace, Falling In Love With Love, Fee-Fi-Fo-Fum, A Fine Romance, 500 Miles High, 502 Blues, Follow Your Heart, Footprints, For All We Know, For Heavens Sake, Forest Flower, Four, Four On Six, Freddie Freeloader, Freedom Jazz Dance, Full House, Gee Baby, Aint I Good To You, Gemini, Giant Steps, The Girl From Ipanema, Glorias Step, God Bless The Child, Golden Lady, Good Evening Mr. And Mrs. America, Grand Central, The Green Mountains, Groovin High, Grow Your Own, Guilty, Gypsy In My Soul, Half Nelson, Have You Met Miss Jones?, Heaven, Heebie Jeebies, Hello, Young Lovers, Heres That Rainy Day, Hot Toddy, House Of Jade, How High The Moon, How Insensitive, How My Heart Sings, Hullo Bolinas, I Cant Get Started, I Cant Give You Anything But Love, I Could Write A Book, I Got It Bad And That Aint Good, I Let A Song Go Out Of My Heart, I Love Paris, I Love You, I Mean You, I Remember Clifford, I Should Care, I Wish I Knew How It Would Feel To Be Free, Ill Never Smile Again, Ill Remember April, Im All Smiles, Im Beginning To See The Light, Im Your Pal, Icarus, If You Never Come To Me, Impressions, In A Mellow Tone, In A Sentimental Mood, In The Mood, In The Wee Small Hours Of The Morning, In Your Quiet Place, The Inch Worm, Indian Lady, Inner Urge, Interplay, The Intrepid Fox, Invitation, Iris, Is You Is, Or Is You Aint, Isnt It Romantic?, Isotope, Israel, It Dont Mean A Thing, Its Easy To Remember, Jelly Roll, Jordu, Journey To Recife, Joy Spring, Juju, Jump Monk, June In January, Just One More Chance, Kelo, Lady Bird, Lady Sings The Blues, Lament, Las Vegas Tango, Lazy Bird, Lazy River, Like Someone In Love, Limehouse Blues, Lines And Spaces, Litha, Little Boat, Little Waltz, Long Ago, Lonnies Lament, Look To The Sky, Love Is The Sweetest Thing, Lucky Southern, Lullaby Of Birdland, Lush Life, The Magician In You, Mahjong, Maiden Voyage, A Man And A Woman, Man In The Green Shirt, Meditation, Memories Of Tomorrow, Michelle, Midnight Mood, Midwestern Nights Dream, Milano, Minority, Miss Ann, Missouri Uncompromised, Mr. P.C., Misty, Miyako, Moments Notice, Mood Indigo, Moonchild, The Most Beautiful Girl In The World, My Buddy, My Favorite Things, My Foolish Heart, My Funny Valentine, My One And Only Love, My Romance, My Shining Hour, My Ship, My Way, Mysterious Traveller, Naima, Nardis, Nefertiti, Never Will I Marry, Nicas Dream, Night Dreamer, The Night Has A Thousand Eyes, A Night In Tunisia, Night Train, Nobody Knows You When Youre Down And Out, Nostalgia In Times Square, Nuages, Oleo, Oliloqui Valley, Once I Loved, Once In Love With Amy, One Finger Snap, One Note Samba, Only Trust Your Heart, Orbits, Ornithology, Out Of Nowhere, Paper Doll, Passion Dance, Passion Flower, Peace, Peggys Blue Skylight, Pent Up House, Penthouse Serenade, Peris Scope, Pfrancing, Pinocchio, Pithecanthropus Erectus, Portsmouth Figurations, Prelude To A Kiss, Prince Of Darkness, P.S. I Love You, Pussy Cat Dues, Quiet Nights Of Quiet Stars, Quiet Now, Recorda Me, Red Clay, Reflections, Reincarnation Of A Lovebird, Ring Dem Bells, Road Song, Round Midnight, Ruby, My Dear, Poem For #15, Satin Doll, Scotch And Soda, Scrapple From The Apple, Sea Journey, Seven Come Eleven, Seven Steps To Heaven, Sidewinder, Silver Hollow, Sirabhorn, Skating In Central Park, So Nice, So What, Solar, Solitude, Some Day My Prince Will Come, Some Other Spring, Some Skunk Funk, Somebody Loves Me, Sometime Ago, Song For My Father, The Song Is You, Sophisticated Lady, The Sorcerer, Speak No Evil, The Sphinx, Standing On The Corner, The Star-Crossed Lovers, Stella By Starlight, Steps, Stolen Moments, Stompin At The Savoy, Straight No Chaser, A String Of Pearls, Stuff, Sugar, A Sunday Kind Of Love, The Surrey With The Fringe On Top, Swedish Pastry, Sweet Georgia Bright, Sweet Henry, Take Five, Take The “A” Train, Tame Thy Pen, Tell Me A Bedtime Story, Thanks For The Memory, Thats Amore, There Is No Greater Love, There Will Never Be Another You, Therell Be Some Changes Made, They Didnt Believe Me, Think On Me, Thou Swell, Three Flowers, Time Remembered, Tones For Joans Bones, Topsy, Tour De Force, Triste, Tune Up, Turn Out The Stars, Twisted Blues, Unchain My Heart, Uniquity Road, Unity Village, Up Jumped Spring, Upper Manhattan Medical Group, Valse Hot, Very Early, Virgo, Wait Till You See Her, Waltz For Debby, Wave, Well Be Together Again, Well You Neednt, West Coast Blues, What Am I Here For?, What Was, When I Fall In Love, When Sunny Gets Blue, When You Wish Upon A Star, Whispering, Wild Flower, Windows, Witch Hunt, Wives And Lovers , Woodchoppers Ball, Woodyn You, The World Is Waiting For The Sunrise, Yes And No, Yesterday, Yesterdays, You Are The Sunshine Of My Life, You Are Too Beautiful, You Brought A New Kind Of Love To Me, You Dont Know What Love Is, You Took Advantage Of Me, Youre Nobody til Somebody Loves You, Young At Heart,'
    ),
    $this->prefix.'-settings-messages' => array(
      $this->prefix.'_messages_booking_pleaseconfirm' => __( 'Please review your booking and click "confirm".', 'commons-booking'),
      $this->prefix.'_messages_booking_confirmed' => __( 'Congratulations, {{FIRST_NAME}}! You´ve successfully booked {{ITEM_NAME}}. An email has been sent to your address {{USER_EMAIL}}.', 'commons-booking'),
      $this->prefix.'_messages_booking_canceled' => __( 'Your booking has been canceled! Thanks for letting us know.', 'commons-booking'),      
      $this->prefix.'_messages_booking_comment_notice' => __( 'To leave a booking comment, click <a href="{{URL}}">here</a>', 'commons-booking'),
    ),         
    $this->prefix.'-settings-mail' => array(
      $this->prefix.'_mail_confirmation_sender' => 'recipient@domain.com', /* @TODO: retired – delete this with the next update */
      $this->prefix.'_mail_from' => '',
      $this->prefix.'_mail_from_name' => '',
      $this->prefix.'_mail_confirmation_subject' => __( 'Your booking {{ITEM_NAME}}', 'commons-booking'),
      $this->prefix.'_mail_confirmation_body' => __('<h2>Hi {{FIRST_NAME}}, thanks for booking {{ITEM_NAME}}!</h2>

          <p>Click here to see or cancel you booking: {{URL}}.</p>
          <p>Booking code: <strong>{{CODE}}</strong></p>

          <h3>Pick up information</h3>

          <p>Pick up {{ITEM_NAME}} at {{LOCATION_NAME}} on {{DATE_START}}.<br>
          Return it there on {{DATE_END}}.<br>
          Address: {{LOCATION_ADDRESS}}<br>
          Opening hours: {{LOCATION_OPENINGHOURS}}.</p>

          <h3>Your information</h3>

          <em>Please make sure you have entered the correct name and adress from your ID - otherwise you will not be able to pick up the item</em>

          <p>Name: {{FIRST_NAME}} {{LAST_NAME}}.<br>
            Address: {{USER_ADDRESS}}</p>

          <p>Thanks, the Team. </p>
        ', 'commons-booking'),
      $this->prefix.'_mail_registration_subject' => __( 'Welcome, {{USER_NAME}} – here´s your account information.', 'commons-booking'),
      $this->prefix.'_mail_registration_body' => __( '<h2>Hi {{USER_NAME}}, thanks for registering!</h2>

          <p>Only one more step: Click here to set your password:</p>
          <p><strong>{{ACTIVATION_URL}}</strong></p>

          <h3>Your information</h3>
          <p>Username: <strong>{{USER_NAME}}</strong></p>
          <p>Name: {{FIRST_NAME}} {{LAST_NAME}}</p>
          <p>Address: {{ADDRESS}}</p>
          <p>Phone: {{PHONE}}</p>

          <p>Thanks, the Team. </p>
        ', 'commons-booking'),
      ),
    $this->prefix.'-settings-advanced' => array(
      $this->prefix.'_enable_customprofile' => 'ON'
      ),  
    );

  }
  /**
  * Set the default values for the settings. 
  * Loop through each setting, if set, keep it otherwise write defaults to wp_options 
  */
  public function apply_defaults() {
    
    foreach ($this->defaults as $d_page => $d_contents) { // get the option d_page / array
      $option = get_option( $d_page );
      foreach ($d_contents as $d_key => $d_value) {
        if ( empty( $option[$d_key] ) ) { // ignore if already set
          $option[$d_key] = $d_value; 
        } 
      }
      update_option( $d_page, $option );
    }
  }


/**
 * Get settings from backend. Return either full array or specified setting
 * If array, remove the prefix for easier retrieval
 *
 *@param setting_page: name of the page (cmb metabox name)
 *@param (optional) name of the setting
 *@param (optional) key/value pairs of the template tags
 *
 *@return string / array
 */
  public function get_settings( $setting_page, $setting_name = "") {
    global $wpdb;
    $page = get_option( $this->prefix . '-settings-' .$setting_page ); 

    if ( $setting_name ) { // setting field set
      if ( !empty ( $page[ $this->prefix . '_'. $setting_name ] ) ) { // value set
        return $page [ $this->prefix . '_'. $setting_name ];
        } else { // value not set
          return "";
        }
    } else { // grabbing all entries     
      foreach($page as $key => $value) { // remove the prefix 
            $clean = str_replace( $this->prefix. '_', '', $key); 
            $clean_array[$clean] = $value; 
        }
      return $clean_array;
    }
  }

}

?>