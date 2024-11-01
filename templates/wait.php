<?php
global $br_current_wait;
$br_current_list = array();
if ( isset( $br_current_wait ) && is_array( $br_current_wait ) && count( $br_current_wait ) > 0 ) {
    $br_current_list = $br_current_wait;
}
$list_name = "wait";
include("wwlist.php");