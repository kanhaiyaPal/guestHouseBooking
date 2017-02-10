<?php
/*
Plugin Name: Guest House Online Booking
Plugin URI: http://www.gapinfotech.com
Description: This plugin provide you an extensive system to manage guest houses at multiple location and multiple cities
Author: Kanhaiya Pal, PHP Developer, Gap Infotech-Gurugram
Version: 0.0.1
Author URI: https://github.com/kanhaiyaPal
*/

function hk_trim_content( $limit ) {
  $content = explode( ' ', get_the_content(), $limit );
  
  if ( count( $content ) >= $limit ) {
    array_pop( $content );
    $content = implode(" ",$content).'...';
  } else {
    $content = implode(" ",$content);
  }	
  
  $content = preg_replace('/\[.+\]/','', $content);
  $content = apply_filters('the_content', $content); 

  return $content;
}

?>