<?php


/**
 * @package cosmicPlugin
 */

/* 
    Plugin Name: Cosmic Plugin
    Plugin URI: http://cosmic.com/plugin
    Description: This is my first attemp
    Version: 1.0.0
    Author: Cosmic Sword "cosmic"
    Author URI: http://cosmic.com
    License: GPLv2 or later
    Text Domain: cosmic-plugin
 */


if (!defined('ABSPATH')) {
   exit;
}

class CosmicPlugin
{


   public function __construct()
   {
      add_action('init', array($this, 'create_custom_feedback'));
      add_action('wp_enqueue_scripts', array($this, 'load_assests'));
      add_shortcode('feedback-form', array($this, 'load_shortcode'));
      add_action('wp_footer', array($this, 'load_scripts'));
      add_action('rest_api_init', array($this, 'register_rest_api'));
   }

   public function create_custom_feedback()
   {
      $args = array(
         'public' => true,
         'has_archive' => true,
         'supports' => array('title'),
         'exclude_from_search' => true,
         'publicly_queryable' => false,
         'capability' => 'manage_options',
         'labels' => array(
            'name' => 'FeedBack Form ',
            'singular_name' => 'FeedBack Form Entry'
         ),
         'menu_icon' => 'dashicons-feedback',
      );
      register_post_type('feedback_form', $args);
   }


   public function load_assests()
   {
      wp_enqueue_style(
         'cosmic-plugin',
         plugin_dir_url(__FILE__) . 'css/styles.css',
         array(),
         1,
         'all'
      );
      wp_enqueue_script(
         'cosmic-plugin',
         plugin_dir_url(__FILE__) . 'js/CosmicPlugin.js',
         array('jquery'),
         1,
         true
      );
   }


   public function load_shortcode()
   { ?>
      <h1 class="font-Poppins text-center mt-4">Give us your Feedback</h1>

      <div class="w-1/2 mx-auto bg-gray-400 pt-3 space-y-6 py-2">
         <div class="mx-auto w-3/4 bg-white space-y-3 px-6 py-7 rounded-md">
            <h4 class="font-Poppins">Rate your experience with our product...</h4>
            <div class="flex space-x-3 items-center">
               <div class="w-14 text-lg h-12 font-Poppins border-[1px] border-gray-400  text-center rounded-lg hover:bg-[#333] hover:text-white hover:duration-700 cursor-pointer flex items-center justify-center" name="rating_1" id="rating_1" onclick="getValue(document.getElementById('inputValue1'),document.getElementById('rating_1'))">
                  1
                  <input type="hidden" value="1" id="inputValue1">
               </div>
               <div class="w-14 text-lg h-12 font-Poppins border-[1px] border-gray-400  text-center rounded-lg hover:bg-[#333] hover:text-white hover:duration-700 cursor-pointer flex items-center justify-center" name="rating-2" id="rating_2" onclick="getValue(document.getElementById('inputValue2'),document.getElementById('rating_2'))">
                  2
                  <input type="hidden" value="2" id="inputValue2">
               </div>
               <div class="w-14 text-lg h-12 font-Poppins border-[1px] border-gray-400  text-center rounded-lg hover:bg-[#333] hover:text-white hover:duration-700 cursor-pointer flex items-center justify-center" name="rating-3" id="rating_3" onclick="getValue(document.getElementById('inputValue3'),document.getElementById('rating_3'))">
                  3
                  <input type="hidden" value="3" id="inputValue3">
               </div>
               <div class="w-14 text-lg h-12 font-Poppins border-[1px] border-gray-400  text-center rounded-lg hover:bg-[#333] hover:text-white hover:duration-700 cursor-pointer flex items-center justify-center" name="rating-4" id="rating_4" onclick="getValue(document.getElementById('inputValue4'),document.getElementById('rating_4'))">
                  4
                  <input type="hidden" value="4" id="inputValue4">
               </div>
               <div class="w-14 text-lg h-12 font-Poppins border-[1px] border-gray-400  text-center rounded-lg hover:bg-[#333] hover:text-white hover:duration-700 cursor-pointer flex items-center justify-center" name="rating-5" id="rating_5" onclick="getValue(document.getElementById('inputValue5'),document.getElementById('rating_5'))">
                  5
                  <input type="hidden" value="5" id="inputValue5">
               </div>
               <div class="text-lg h-12 flex items-center justify-center">
                  <span class="dashicons dashicons-star-filled  ml-4 text-green-600"></span>
                  <span class="ml-2 font-Poppins">Stars</span>
               </div>
            </div>
         </div>
         <div class="mx-auto w-3/4 bg-white space-y-3 px-6 py-7 h-96 rounded-md">
            <h4 class="font-Poppins">Anything that can be improved?</h4>
            <textarea type="text" class="w-full border-[1px] border-gray-400 h-44" name="feedback" id="feedbackArea"></textarea>
            <button class="px-10 py-2 bg-[#333] text-white rounded" id="save">Submit</button>
         </div>

      </div>
   <?php }



   public function load_scripts()
   { ?>

      <script>
         let rate;
         let rating_1 = document.getElementById('rating_1')
         let rating_2 = document.getElementById('rating_2')
         let rating_3 = document.getElementById('rating_3')
         let rating_4 = document.getElementById('rating_4')
         let rating_5 = document.getElementById('rating_5')

         function getValue(e, v) {
            console.log(e.value);
            rate = e.value
            v.style.backgroundColor = '#333'
            v.style.color = 'white'
         }

         let nonce = '<?php echo wp_create_nonce('wp_rest'); ?>'
         let btn = document.getElementById('save')
         let post_id = '<?php echo get_the_ID() ?>';

         btn.onclick = () => {
            let feedbackArea = document.getElementById('feedbackArea').value
            console.log(post_id);
            const req = new XMLHttpRequest();
            req.open('POST', '<?php echo get_rest_url(null, 'feedback-form/v1/send-feedback'); ?>', true);
            req.onload = () => {
               if (req.readyState === XMLHttpRequest.DONE) {
                  if (req.status === 200) {
                     let data = req.response.trim();

                     console.log(data);
                     if(data == "Thank you for your feedback"){
                        console.log('what');
                        alert('Thank you for your feedback')
                     }
                  }
               }
            };
            req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            req.setRequestHeader("Content-Type", "multipart/form-data");

            req.setRequestHeader("X-WP-Nonce", nonce);
            req.send(`rating=${rate}&feedback=${feedbackArea}&post_id=${post_id}`);

         }
      </script>

<?php }


   public function register_rest_api()
   {
      register_rest_route('feedback-form/v1', 'send-feedback', array(
         'methods' => 'POST',
         'callback' => array($this, 'handle_feedback_form')
      ));
   }

   public function handle_feedback_form($data)
   {
      global $wpdb;
      $headers = $data->get_headers();
      $nonce = $headers['x_wp_nonce'][0];

      if (!wp_verify_nonce($nonce, 'wp_rest')) {
         return new WP_REST_Response('Feedback not send', 401);
      }

      $rating = $_POST['rating'];
      $feedback = $_POST['feedback'];
      $post_id = $_POST['post_id'];

      $posts = wp_insert_post([
         'post_type' => 'feedback_form',
         'post_title' => 'Feedback',
         'post_status' => 'New',
         'post_rating' => $rating,
         'post_feedback' => $feedback,
      ]);

      if ($posts) {
         $sql = $wpdb->insert('feedbacksiguess', array('rate_star' => $rating, 'feedback' => $feedback, 'post_id' => $post_id));
         if ($sql) {
            echo 'Thank you for your feedback';
         }
      }
   }
}

new CosmicPlugin;
