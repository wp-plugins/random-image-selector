<?php
/*
  Plugin Name: Random Image Selector
  Plugin URI:  http://kdmurray.net/projects/wp-random-image/
  Version:     1.0.2
  Description: Selects a random image from a specified folder, and provides
               methods for using it.  Current supported methods generate an
               Image Tag, or a "background" entry for use in a stylesheet.
  Author:      Keith Murray
  Author URI:  http://kdmurray.net/
*/

/*
    Copyright 2007 Keith Murray  (email : kdmurray@kdmurray.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//Check to see if user has sufficient privileges
function ri_is_authorized() {
        global $user_level;
        if (function_exists("current_user_can")) {
                return current_user_can('activate_plugins');
        } else {
                return $user_level > 5;
        }
}


// Hook for adding admin menus
add_action('admin_menu', 'ri_add_pages');

// action function for above hook
function ri_add_pages() {

    // Add a new submenu under Options:
    add_options_page('Random Image', 'Random Image', 8, 'randomimg_options', 'ri_options_page');

}

function ri_options_page() {

        global $ol_flash, $_POST;
        if (ri_is_authorized()) {
                if (isset($_POST['randomimage_url'])) {
                        update_option('randomimage_url',$_POST['randomimage_url']);
                        $ol_flash = "Your settings have been saved.";
                }
                if (isset($_POST['randomimage_path'])) {
                        update_option('randomimage_path',$_POST['randomimage_path']);
                        $ol_flash = "Your settings have been saved.";
                }
        }       else {
              $ol_flash = "You don't have sufficient privilges.";
        }


        if (ri_is_authorized()) {
                echo '<div class="wrap">';
                echo '<h2>Set up your Random Image Options</h2>';
                echo '<p>This plugin gives you the ability to add a random image to any part of your wordpress installation, for example in the header of your theme or page.  ';
                echo 'By pointing the plugin at a folder in your Wordpress directory, it will select at random one image from that folder and display it wherever you need. </p>';
                echo '<form action="" method="post">';
                echo '<input type="hidden" name="redirect" value="true" />';
                echo '<ol>';
                echo '<li>Enter the path (full path) of the folder you would like to pull the images from: (<b>e.g.</b> <i>/home/myuser/mydomain.com/wp-content/backgrounds</i>)<br/>';
                echo '<input type="text" name="randomimage_path" size="65" value="'.get_option('randomimage_path').'" /></li>';
                echo '<li>Enter the corresponding URL path (full  path) of the folder in #1: (<b>e.g.</b> <i>http://mydomain.com/wp-content/backgrounds</i>)<br />';
                echo '<input type="text" name="randomimage_url" size="65" value="'.get_option('randomimage_url').'" /></li>';
                echo '</ol>';
                echo '<p><input type="submit" value="Save" /></p></form>';
                echo '</div>';
        }
        else {
              $ol_flash = "You don't have sufficient privilges.";
        }
}





  function generateRandomImage()
  {
    $physicalPath = get_option('randomimage_path');
    $vPath = get_option('randomimage_url');
    $image_types = array('jpg','png','gif'); // Array of valid image types
    $image_directory = opendir($physicalPath);

    while($image_file = readdir($image_directory))
    {
      if(in_array(strtolower(substr($image_file,-3)),$image_types))
      {
         $image_array[] = $image_file;
         sort($image_array);
         reset ($image_array);
      }
    }

    return $vPath.'/'.$image_array[rand(1,count($image_array))-1];
  }

  function generateRandomBGStyle()
  {
    $filename = generateRandomImage();
    echo 'background: url('.$filename.');';
  }

  function generateRandomImgTag()
  {

    $physicalPath = get_option('randomimage_path');
    $vPath = get_option('randomimage_url');
    $image_types = array('jpg','png','gif'); // Array of valid image types  
    $image_directory = opendir($physicalPath);

    while($image_file = readdir($image_directory))
    {
      if(in_array(strtolower(substr($image_file,-3)),$image_types))
      {
         $image_array[] = $image_file;
         sort($image_array);
         reset ($image_array);
      }
    }

   $image_filename=$image_array[rand(1,count($image_array))-1];
   $filename=$vPath.'/'.$image_filename;

    echo '<img src="'.$filename.'" title="'.substr($image_filename,0,-4).'" />';
  }

?>
