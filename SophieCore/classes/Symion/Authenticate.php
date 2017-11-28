<?php

/**
* Symion vPHP - A Vanilla PHP Framework
*
* Authenicate.php
*
* A simple multi type authenication class for API's.
*
* @package      Symion vPHP
* @author       Corné de Jong <corne@cornedejong.net>
* @version      0.1
* @link         # http://symion.cf/portfolio/vphp # Not Available yet!
* @link         # https://api.symion.cf/yar/vphp # Not Available yet!
* @since        File Available since 20 November 2017
*
*/

namespace Symion;

class Authenticate
{


    /*
    | END OF -> Variables
    |--------------------------------------------------------------------------
    | __constructor
    |--------------------------------------------------------------------------
    |
    | Init the authenication class.
    |
    */ 

    private function __constructor() {

    }

    /*
    | END OF -> __constructor
    |--------------------------------------------------------------------------
    | basic
    |--------------------------------------------------------------------------
    |
    | This function uses http basic authenication to authenicate the user.
    | The settings array consists of all the parametes needed to succesfully 
    | authenicate the user. 
    |
    | $users = array( NAME, KEY, TOKEN, PASSWORD, BEARER ),
    |
    */

    public function basic($users = array()) {

        $current_user = "undefined";
        $submit_pass = "undefined";

        if(!is_array($users)) {
            $status = false;
            $message = gettype($users) . " supplied. Array required!";
            goto response;
        }

        if(!empty($_SERVER['PHP_AUTH_USER']))    {  
            $submit_user = $_SERVER['PHP_AUTH_USER'];   
        } else {
            $status = false; 
            $message = "no user";
            goto response;
        }
        
        if(!empty($_SERVER['PHP_AUTH_PW']))   { 
            $submit_pass = $_SERVER['PHP_AUTH_PW'];     
        } else {
            $status = false; 
            $message = "no pass";
            goto response;
        }

        # Loop through the array to check if the username exists
        foreach($users as $key => $user) {
            if($user['NAME'] == $submit_user) {
                # if so, set the status to true and break the loop
                $user_array_key = $key;
                $current_user = $user['NAME'];
                $status = true;
                break;
            } else {
                $status = false;
                $message = "wrong user";
            }
        }

        if(!$status) {
            goto response;
        }

        if($users[$user_array_key]['PASSWORD'] == $submit_pass) {
            $status = true;
            $message = "access granted";
        } else {
            $status = false; 
            $message = "wrong pass";
            goto response;
        }

        # Format the response and return the array
        response:
        return(array(
            "status" => $status,
            "message" => $message,
            "user" => $current_user
        ));

    }

    /*
    | END OF -> basic
    |--------------------------------------------------------------------------
    | keyToken
    |--------------------------------------------------------------------------
    |
    | This function checks weighter or not the given key/token combination is 
    | in the users array. 
    |
    | $users = array( NAME, KEY, TOKEN, PASSWORD, BEARER ),
    |
    */

    public function keyToken($users = array())    {

        $current_user = "undifined";

        # Get Apache Request Headers
		$headers = apache_request_headers();        
        
        /** 
        * Extract the API Key & Token
        * ---------------------------
        */
                
        # Check if the API Key is set
        if(isset($headers['Auth-Key'])) {
            # if so, store it in $api_key
            $api_key = $headers['Auth-Key'];
        } else {
        # If the API Key isn't set.. set status to false and goto response
            $status = false; 
            $message = "key isn't set";
            goto response;
        }
                
        # Check if the API Token is set
        if(isset($headers['Auth-Token'])) {
        $api_token = $headers['Auth-Token'];
        } else {
            # If the API Token isn't set.. set status to false and goto response
            $status = false; 
            $message = "token isn't set";
            goto response;
        }
                
        /** 
        * Checking the API Key & Token
        * ----------------------------
        */
 
        # Loop through the array to check if the key exists
        foreach($users as $key => $user) {
            if($user['KEY'] == $api_key) {
                # if so, set the status to true and break the loop
                $user_array_key = $key;
                $current_user = $user['NAME'];
                $status = true;
                # no need to look any further. Lets break the loop
                break;
            } else {
                # if not, Set the status to false and add the message
                $status = false;
                $message = "incorrect key";
            }
        }

        if(!$status) {
            # if there was no match foud. Go to response
            goto response;
        }

        if($users[$user_array_key]['TOKEN'] == $api_token) {
            $status = true;
            $message = "access granted";
        } else {
            $status = false; 
            $message = "incorrect token";
            goto response;
        }

        # Format the response and return the array
        response:
        return(array(
            "status" => $status,
            "message" => $message,
            "user" => $current_user
        ));
    }

    /*
    | END OF -> keyToken
    |--------------------------------------------------------------------------
    | bearer
    |--------------------------------------------------------------------------
    |
    | Checks if the bearer token provided is defined in the users array.
    | 
    |
    */

    public function bearer($users)  {

        $current_user = "undifined";

        # Get Apache Request Headers
        $headers = apache_request_headers();
        
        if(isset($headers['Authorization']) && !empty($headers['Authorization'])) {
            # If it is set, and isn't empty, Strip 'Bearer' from the header and store it in $bearerToken
            $bearerToken = str_replace('Bearer', '', $headers['Authorization']);
            # And lets trim it, becouse we're not expectiong spaces in the token
            $bearerToken = trim($bearerToken);
        } else {
            # if it's not set, or it's empty. Set the status to false and go to the response
            $status = false; 
            $message = "no token set";
            goto response;
        }

        # Loop through the array to check if the key exists
        foreach($users as $key => $user) {
            if($user['BEARER'] == $bearerToken) {
                # if so, set the status to true and break the loop
                $current_user = $user['USERNAME'];
                $message = "access granted";
                $status = true;
                goto response;
            } else {
                $status = false;
                $message = "incorrect key";
            }
        }

        # Format the response and return the array
        response:
        return(array(
            "status" => $status,
            "message" => $message,
            "user" => $current_user
        ));

    }

}

?>