<?php

session_start();
$sessionUser = $_SESSION['usr_id'];
$user_id = $_GET['user_id'];

    require_once('connect.php');

    //lookup all links from the xml file if length of q>0
    if (strlen($user_id)>0) {
          $query = $connect->query("
          		   UPDATE friends
          		   SET friend_request= 'Friends' WHERE user_me = $user_id and user_other = $sessionUser
		   ");
           $query = $connect->query("
			       INSERT INTO friends (user_me, user_other, friend_request) 
			       VALUES ($sessionUser, $user_id, 'Friends')
		   ");
      }

      header( "Location: /userProfile.php?user_id=$user_id" ) ;   
?>