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
    $p =  $this->prefix; 

    // Default Settings
    $this->defaults = array(
        $p. '-settings-pages' => array(
          $p.'_item_page_select' => $item_page,
          $p.'_user_page_select' => $user_page,
          $p.'_registration_page_select' => $user_reg_page,
          $p.'_bookingconfirm_page_select' => $booking_confirm_page,
        ),
        $p.'-settings-bookings' => array(
          $p.'_bookingsettings_maxdays' => 3,
          $p.'_bookingsettings_daystoshow' => 30,
          $p.'_bookingsettings_allowclosed' => ''
        ),   
         $p.'-settings-codes' => array(
      $p.'_codes_pool' => 'Til I Die, (I Saw Santa) Rockin Around the Christmas Tree, 409, 409, 4th of July, A Casual Look, A Day in the Life of a Tree, A Thing or Two, A Time to Live in Dreams, A Young Man Is Gone, Add Some Music to Your Day, Airplane, All Alone, All Dressed Up for School, All I Wanna Do, All I Want to Do, All I Want to Do, All Summer Long, All This Is That, Alley Oop, Amusement Parks U.S.A., And Your Dream Comes True, Angel Come Home, Anna Lee, the Healer, Arent You Glad, Arent You Glad, At My Window, Auld Lang Syne, Baby Blue, Back Home, Ballad of Ole Betsy, Barbara Ann, Barbara Ann, Barbara, Barbie, Barnyard Blues, Be Here in the Mornin, Be Still, Be True to Your School, Be True to Your School, Be with Me, Beach Boys Stomp, Beaches In Mind, Belles of Paris, Bells of Christmas, Better Get Back in Bed, Blue Christmas, Blueberry Hill, Bluebirds over the Mountain, Bluebirds over the Mountain, Boogie Woodie, Break Away, Brians Back, Bull Session with the Big Daddy, Busy Doin Nothin, Cabinessence, California Calling, California Dreamin, California Feelin, California Girls, California Girls, California Saga: Big Sur, California Saga: California, California Saga: The Beaks of Eagles, Cant Wait Too Long, Car Crazy Cutie, Carls Big Chance, Caroline, No, Caroline, No, Cassius Love vs. Sonny Wilson, Catch a Wave, Celebrate the News, Chasin the Sky, Cherry, Cherry Coupe, Child of Winter, Christmas Day, Christmas Time Is Here Again, Chug-A-Lug, Cindy, Oh Cindy, Come Go with Me, Cool, Cool Water, Cotton Fields (The Cotton Song), Country Air, County Fair, Crack at Your Love, Crocodile Rock, Cuckoo Clock, Cuddle Up, Custom Machine, Dance, Dance, Dance, Dance, Dance, Dance, Darlin, Darlin, Daybreak Over The Ocean, Deirdre, Dennys Drums, Devoted to You, Diamond Head, Ding Dang, Disney Girls, Do It Again, Do It Again, Do You Like Worms, Do You Remember?, Do You Wanna Dance?, Dont Back Down, Dont Go Near the Water, Dont Hurt My Little Sister, Dont Talk (Put Your Head on My Shoulder), Donâ€™t Worry Baby, Donâ€™t Worry Baby, Drive-In, East Meets West, Endless Harmony, Everyones in Love with You, Farmers Daughter, Feel Flows, Finders Keepers, Forever, Forever, Friends, Friends, From There To Back Again, Frosty the Snowman, Full Sail, Fun, Fun, Fun, Fun, Fun, Fun, Funky Pretty, Funky Pretty, Games Two Can Play, Getcha Back, Gettin Hungry, Girl Dont Tell Me, Girls on the Beach, God Only Knows, God Only Knows, Goin On, Goin South, Goin To The Beach, Good Time, Good Timin, Good to My Baby, Good Vibrations, Good Vibrations, Got to Know the Woman, Graduation Day, Guess Im Dumb, H. E. L. P. Is on the Way, Had to Phone Ya, Hang On to Your Ego, Happy Endings, Hawaii, Hawaii, He Come Down, Heads You Win - Tails I Lose, Help Me, Rhonda, Help Me, Rhonda, Help Me, Ronda, Here Comes the Night, Here Comes the Night, Here She Comes, Here Today, Heroes and Villains, Heroes and Villains, Hey, Little Tomboy, Hold On Dear Brother, Honkin Down the Highway, Hot Fun in the Summertime, How She Boogalooed It, Hully Gully, Hushabye, Hushabye, I Can Hear Music, I Can Hear Music, I Do Love You, I Do, I Get Around, I Get Around, I Just Got My Pay, I Just Wasnt Made for These Times, I Know Theres an Answer, I Love To Say Da-Da, I Should Have Known Better, I Wanna Pick You Up, I Was Made to Love Her, I Went to Sleep, Id Love Just Once to See You, Ill Be Home for Christmas, Ill Bet Hes Nice, Im Bugged at My Ol Man, Im So Lonely, Im So Young, Im the Pied Piper (instrumental), Im the Pied Piper, Im Waiting for the Day, In My Car, In My Room, In My Room, In the Back of My Mind, In the Parkin Lot, In the Still of the Night, Island Fever, Island Girl, Isnt It Time, Its a Beautiful Day, Its About Time, Its About Time, Its Gettin Late, Its Just a Matter of Time, Its OK, Its Over Now, Johnny B. Goode, Johnny Carson, Judy, Just Once in My Life, Keep an Eye on Summer, Keepin the Summer Alive, Keepin the Summer Alive, Kiss Me, Baby, Kokomo, Kokomo, Kona Coast, Lady Lynda, Lady Lynda, Lady, Lahaina Aloha, Lana, Land Ahoy, Lavender, Leaving This Town, Leaving This Town, Let Him Run Wild, Let the Wind Blow, Let the Wind Blow, Let Us Go on This Way, Lets Go Away For Awhile, Lets Go Trippin, Lets Go Trippin, Lets Put Our Hearts Together, Little Bird, Little Bird, Little Deuce Coupe, Little Deuce Coupe, Little Girl (Youre My Miss America), Little Honda, Little Pad, Little Saint Nick, Little Surfer Girl, Livin with a Heartache, Lonely Days, Lonely Sea, Long Promised Road, Long Promised Road, Long, Tall Texan, Lookin at Tomorrow (A Welfare Song), Loop De Loop (Flip Flop Flyin In An Aeroplane), Louie, Louie, Love Is a Woman, Love Surrounds Me, Luau, Magic Transistor Radio, Make It Big, Make It Good, Male Ego, Mama Says, Marcella, Marcella, Match Point of Our Love, Maybe I Dont Know, Meant for You, Melekalikimaka, Merry Christmas, Baby, Misirlou, Mona Kona, Mona, Monster Mash, Moon Dawg, Morning Christmas, Mother May I, Mountain of Love, Mt. Vernon and Fairway (Theme), My Diane, My Love Lives On, Never Learn Not to Love, No-Go Showboat, Noble Surfer, Oh Darlin, Old Folks at Home (Swanee River), Only with You, Only With You, Our Car Club, Our Favorite Recording Sessions, Our Prayer, Our Sweet Love, Our Team, Pacific Coast Highway, Palisades Park, Papa-Oom-Mow-Mow, Papa-Oom-Mow-Mow, Passing By, Passing Friend, Peggy Sue, Pet Sounds, Pitter Patter, Please Let Me Wonder, Pom Pom Play Girl, Private Life Of Bill And Sue, Problem Child, Punchline, Radio King Dom, Rock n Roll to the Rescue, Rock and Roll Music, Rock and Roll Music, Roller Skating Child, Ruby Baby, Runaway, Sail On, Sailor, Sail On, Sailor, Sail Plane Song, Salt Lake City, San Miguel, Santa Ana Winds, Santa Claus Is Comin To Town, Santas Beard, Santas Got an Airplane, School Day (Ring! Ring! Goes The Bell), School Days, Sea Cruise, She Believes in Love Again, She Knows Me Too Well, Shes Goin Bald, Shes Got Rhythm, Shelter, Sherry She Needs Me, Shortenin Bread, Shut Down, Shut Down, Shut Down, Part II, Side Two, Slip On Through, Sloop John B, Sloop John B, Slow Summer Dancing (One Summer Night), Solar System, Some of Your Love, Somewhere Near Japan, Soul Searchin, Soulful Old Man Sunshine, Sounds of Free, South Bay Surfer, Spirit of America, Spring Vacation, Steamboat, Still Cruisin, Still I Dream of It, Still Surfin, Stoked, Strange Things Happen, Strange World, Student Demonstration Time, Sumahama, Summer in Paradies, Summer in Paradise, Summer Means New Love, Summer of Love, Summers Gone, Summertime Blues, Sunshine, Surf Jam, Surfs Up, Surfer Girl, Surfer Girl, Surfers Rule, Surfin Safari, Surfin U.S.A., Surfin U.S.A., Surfin, Surfin, Susie Cincinnati, Sweet Sunday Kinda Love, T M Song, Take a Load Off Your Feet, Talk to Me, Tears in the Morning, Tell Me Why, Ten Little Indians, That Same Song, Thats Not Me, Thats Why God Made the Radio, The Baker Man, The Girl from New York City, The Letter (The Box Tops song), The Letter, The Little Girl I Once Knew, The Little Old Lady from Pasadena, The Lords Prayer, The Monkeys Uncle, The Surfer Moon, The Times They Are A-Changin, The Trader, The Trader, The Wanderer, The Warmth of the Sun, Their Hearts Were Full of Spring, Their Hearts Were Full of Spring, Then I Kissed Her, Theres No Other (Like My Baby), Things We Did Last Summer, Think About The Days, This Car of Mine, This Whole World, Time to Get Alone, Transcendental Meditation, Trombone Dixie, Tune X, Under the Boardwalk, Unreleased Backgrounds, Vegetables, Vegetables, Wake the World, Wake the World, Walk On By, We Got Love, We Three Kings of Orient Are, Well Run Away, Were Together Again, Wendy, What Is a Young Girl Made Of?, Whatd I Say, When a Man Needs a Woman, When Girls Get Together, When I Grow Up (To Be a Man), Where I Belong, Where Is She?, Whistle In, White Christmas, Why Do Fools Fall In Love, Why Dont They Let Us Fall in Love, Why, Wild Honey, Wild Honey, Wind Chimes, Winds of Change, Winter Symphony, Wipe Out, With a Little Help from My Friends, With Me Tonight, Wonderful, Wonderful, Wontcha Come Out Tonight, Wouldnt It Be Nice To Live Again, Wouldnt It Be Nice, You Need a Mess of Help to Stand Alone, You Still Believe in Me, Youre So Good to Me, Youre Still A Mystery, Youre Welcome, Youre with Me Tonight, Youve Got to Hide Your Love Away, Youve Lost That Lovin Feelin, Your Summer Dream, Chapel of Love, Da Doo Ron Ron, Fall Breaks and Back to Winter (Woody Woodpecker Symphony), Graduation Day, Honky Tonk, Lady Liberty,'
    ),
    $p.'-settings-messages' => array(
      $p.'_messages_booking_pleaseconfirm' => __( '<p> Please review your booking and click "confirm".</p>', $p ),
      $p.'_messages_booking_confirmed' => __( '<h2>Congratulations, {{FIRST_NAME}}!</h2> <p>You´ve successfully booked {{ITEM_NAME}}. An email has been sent to your address {{USER_EMAIL}}. </p>', $p ),
      $p.'_messages_booking_canceled' => __( '<h2>Your booking has been canceled!</h2><p>Thanks for letting us know.</p>', $p ),
    ),         
    $p.'-settings-mail' => array(
      $p.'_mail_confirmation_sender' => 'recipient@domain.com',
      $p.'_mail_confirmation_subject' => __( 'Your booking', $p ),
      $p.'_mail_confirmation_body' => __('<h2>Hi {{FIRST_NAME}}, thanks and for booking {{ITEM_NAME}}!</h2>

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

          <p>Name: {{FIRST_NAME}} {{LAST_NAME}}.<br>
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

}

  /**
  * Set the default values for the settings. 
  * Loop through each setting, if set, keep it otherwise write defaults
  */
  public function set_defaults() {
    
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
  public function get( $setting_page, $setting_name = "") {
    global $wpdb;
    $page = get_option( $this->prefix . '-settings-' .$setting_page ); 

    if ( $setting_name ) {
      return $page [ $this->prefix . '_'. $setting_name ];
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