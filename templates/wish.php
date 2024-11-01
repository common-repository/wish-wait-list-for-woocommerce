<?php
global $br_current_wish;
$br_current_list = array();
if ( isset( $br_current_wish ) && is_array( $br_current_wish ) && count( $br_current_wish ) > 0 ) {
    $br_current_list = $br_current_wish;
}
$list_name = "wish";
include("wwlist.php");
