<?php

session_start();
$sessionUser = $_SESSION['usr_id'];
$user_id = $_GET['user_id'];

    require_once('connect.php');

    //lookup all links from the xml file if length of q>0
    if (strlen($user_id)>0) {
          $query = $connect->query("
			       DELETE FROM following where user_me = $sessionUser and following_user = $user_id
		   ");
      }

      header( "Location: /userProfile.php?user_id=$user_id" ) ;   
?>