<?php

class BaseController{

  public static function get_user_logged_in(){
    if(isset($_SESSION['user'])){
      $user_id = $_SESSION['user'];

      $user = User::find($user_id);
      return $user;
    }
    return null;
  }


  public static function check_logged_in(){

      if(!isset($_SESSION['user'])){
      Redirect::to('/login', array('message' => 'You have to log in first!'));
    }
  }


  public static function check_logged_in_is_admin() {

    $user = self::get_user_logged_in();
    if(!is_null($user) and $user->admin == true) {
      return;
    }

    Redirect::to('/login', array('message' => 'You need to log in as an admin first!'));
  }


  public static function check_logged_in_is_admin_or_has_id($id) {
    $user = self::get_user_logged_in();

    if(!is_null($user)) {

      if($user->id == $id or $user->admin == true) {
        return;
      }
    }

    Redirect::to('/login', array('message' => 'You need to log in as an admin or the user in question first!'));
  }

}
