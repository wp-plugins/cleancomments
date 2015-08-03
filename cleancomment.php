<?php
/*
Plugin Name: CleanComments Commenting System
Plugin URI: http://www.cleancomments.com/
Description: The CleanComments comment system replaces your WordPress comment system with your comments hosted and powered by CleanComments. Use the CleanComments admin page to set it up.
Author: CleanComments <info@cleancomments.com>
Version: 1.0.1
Author URI: http://cleancomments.com
*/
function cleancomment_template_load( $comment_template ) {
     global $post;
     if ( !( is_singular() && ( have_comments() || 'open' == $post->comment_status ) ) ) {
        return;
     }
     return dirname(__FILE__) . '/cleancomments_template.php';
}


function register_cleancomment_submenu_page()
{
     add_submenu_page( 'edit-comments.php', 'CleanComments', 'CleanComments', 'manage_options', 'CleanCommentSettingsPage', 'render_cleancomment_submenu_page' );
}

function render_cleancomment_submenu_page()
{
     echo '<div class="wrap"><h2>CleanComments settings</h2>';
     if(isset($_POST['submit']))
     {
          update_cleancomments_options();
     }
     $val_CleanCommentsID = stripslashes(get_option('CleanCommentsID'));
     echo <<<EOF
     <p>Add the key that was sent to your email. If you don't have one, register on <a href="http://cleancomments.com/" target="blank">CleanComments</a> </p>
     <form method="post">
     <input type="text" name="CleanComments_id" size="50" value="$val_CleanCommentsID" />
     <input type="submit" name="submit" value="Save Changes" />
     </form>
EOF;
     echo '</div>';

}

function update_cleancomments_options()
{
     $updated = false;
     if ($_POST['CleanComments_id'])
     {
          $safe_val = addslashes(strip_tags($_POST['CleanComments_id']));
          $test_key = stripslashes($safe_val);
          if(check_site_key($test_key))
          {
               update_option('CleanCommentsID', $safe_val);
               $updated = true;
          }
     }
     if ($updated)
     {
          echo '<div id="message" class="updated fade">';
          echo '<p>CleanComments KEY successfully updated!</p>';
          echo '</div>';
     }
     else
     {
          echo '<div id="message" class="error fade">';
          echo '<p>Key is not provided or site is not registered.</p>';
          echo '</div>';
     }
}

function get_cleancomments_count($count)
{
     static $post_comment_count_arr = array();
     $post_ID = get_the_ID();
     $site_key = stripslashes(get_option('CleanCommentsID'));
     $curr_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
     if(empty($post_comment_count))
     {
          $response = GetPage("http://cleancomments.com/api/count?site_key=$site_key");
          $response_decoded = json_decode($response, true);
          $post_comment_count_tmp = $response_decoded['counts'];
          foreach($post_comment_count_tmp as $id_count_arr)
          {
               $post_comment_count_arr[$id_count_arr[0]] = $id_count_arr[1];
          }
     }
     return $post_comment_count_arr[$post_ID];
}

function GetPage($strSubmitURL,$strPostFields="",$strReferrer="",$strCookieFile="",$strProxy="")
{

    $cookie_jar = $strCookieFile;
    $agent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0";
    $ch = curl_init();
    if($strProxy != "")
    {
        curl_setopt($ch, CURLOPT_PROXY,$strProxy );
    }

    // store cookies //

    if($cookie_jar!="")
    {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);
    }

    if($strReferrer != "")
    {
        curl_setopt($ch, CURLOPT_REFERER, "$strReferrer");
    }

    //check the browser
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);

    //return parameter
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
    curl_setopt($ch, CURLOPT_TIMEOUT, 140);

    //site name
    curl_setopt($ch, CURLOPT_URL,$strSubmitURL);

    if($strPostFields != "")
    {
        //set type as an post
        curl_setopt($ch, CURLOPT_POST, true);
        //field name
        curl_setopt($ch, CURLOPT_POSTFIELDS,$strPostFields);
    }



    // don' verify ssl host
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
    $strData = curl_exec ($ch);
    
    if (!$strData) {
        //die ("cURL error: " . curl_error ($ch) . "\n");
        return '';
    }

    curl_close ($ch);
    unset($ch);
    return $strData;
}

function check_site_key($site_key)
{
     $response = GetPage("http://cleancomments.com/api/count?site_key=$site_key");
     $response_decoded = json_decode($response, true);
     if(is_array($response_decoded['counts']))
     {
          return true; //Valid site key
     }
     return false; //Invalid site key
}

// Add settings link on plugin page
function cleancomments_settings_link($links) { 
  $settings_link = '<a href="edit-comments.php?page=CleanCommentSettingsPage">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}

//Show message after plugin activation
function cleancomment_admin_notices()
{
     include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
     if (!get_option('cleancomment_notice_shown') && !is_plugin_active('plugins/cleancomment.php'))
     {
          echo <<<NOTICE
          <!-- CSS and HTML can be added here -->
          <div class='updated'>
          <p>
          Thank you for using cleancomments plugin. To register a host <a href="http://cleancomments.com/" target="blank">Press here</a>
          </p>
          </div>
NOTICE;
          update_option('cleancomment_notice_shown', 'true');
     }
}

function delete_cleancomments_option()
{
     delete_option('cleancomment_notice_shown'); 
}

add_action('admin_notices', 'cleancomment_admin_notices');
add_action('admin_menu', 'register_cleancomment_submenu_page');
$val_CleanCommentsID = stripslashes(get_option('CleanCommentsID'));
if(check_site_key($val_CleanCommentsID))
{
     add_filter( 'comments_template', 'cleancomment_template_load' );
     add_filter('get_comments_number', 'get_cleancomments_count');
}
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'cleancomments_settings_link' );
register_deactivation_hook(__FILE__, 'delete_cleancomments_option');

?>