<?php 

/**
* SophieDB - JSON DATABASE API
* ----------------------------
* SophieCore
* ./SophieCore/Core.php
* 
* The SophieDB\Core consists of 3 types of functions:
* - Set functions:
*     These functions set values for variables.
* - Print functions:
*     These functions print responses out to the user.
* - Return functions:
*     These functions return a value to the script that insantiated it. 
* 
* This is the core class of SophieDB and consits
* of the main functions of the database api.
*
* @package      SophieDB
* @author       Corné de Jong <corne@cornedejong.net>
* @version      0.1.2
* @link         http://get.sophiedb.ga/v2
* @since        File Available since 11 November 2017
* 
*/

namespace SophieDB;

class Core
{
    
    private $CORE = array();
    private $DBNAME = 'string';
    private $DBLOCATION = 'string';
    private $DBDATA = array();
    private $DBASOCC = array();
    private $USER = array();
    private $ACTIONS = array();
    private $REQUESTFUNCTION = 'string';
    private $REQUESTPOINTER = array();
    private $QUERYPARAMETERS = array();
    private $SEARCHQUERY;

    /*
    | END OF -> VARIABLES
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    |
    | The constructor checks if the request was made on HTTPS and loads the 
    | core.json config file. 
    |
    */

    public function __construct() {
        $this->CORE = json_decode($this->deCommentFile("./SophieCore/core.sophie"), true);
        if(!$this->isHTTPS())   {
            $this->error(array(0));
        }
        $this->actionsArray();

        $this->REQUESTFUNCTION = $this->ACTIONS[0];

        $this->queryParameters();
    }

    /*
    | END OF -> __construct
    |--------------------------------------------------------------------------
    | isHTTPS
    |--------------------------------------------------------------------------
    |
    | isHTTPS checks if the request was made over HTTPS or not. 
    | Returns true if it was made with HTTPS, else it returns false.
    |
    */

    private function isHTTPS(){
        # Lets see if the request is made over HTTPS
        if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on"){
            # if thats the case, return true
            return true;
        } else {
            # if not, return false
            return false;
        }
    }

    /*
    | END OF -> isHTTPS()
    |--------------------------------------------------------------------------
    | isJson
    |--------------------------------------------------------------------------
    |
    | This function chech's weigther it is an json object or not. 
    | If Json, it returns false. If not, it returns the json error.
    |
    | @param $string = string(JSON ENCODED STRING)
    |
    */

    private function isJson($string) {
        # Decode the given string
        json_decode($string);
        # And return the json error
        return (json_last_error() == JSON_ERROR_NONE);
   }

   /*
   | END OF -> isJson
   |--------------------------------------------------------------------------
   | searchQueryType
   |--------------------------------------------------------------------------
   |
   | This function checks if the submitted search query is a 
   | key/value pair or not.
   |
   */

   private function searchQueryType() {
       # First check if there is a searchquery privided. 
       # By excluding the requested function and database from the equation
        if(count($this->ACTIONS) > 2) {   
            # Set the $query variable with the last item of the uri
            $query = $this->ACTIONS[count($this->ACTIONS) - 1 ];
            # Check if the '=' sign is found in the string
            if (preg_match('/=/', $query)) {
                # If so, explode it and make it into an array with keys of 'key' and 'value'
                $searchQuery = explode("=", $query);
                $this->SEARCHQUERY = array("key" => $searchQuery[0], "value" => $searchQuery[1]);
            } else {
                # otherwise, store the sting with the key 'value' in the varable $SEARCHQUERY
                $this->SEARCHQUERY = array("value" => $query);
            }
        } else {
            # If the actions list does not contain more than 2 items.. 
            # Set $SEARCHQUERY to false
            $this->SEARCHQUERY = false;
        }
        # After this all return the query
        return $this->SEARCHQUERY;
   }

    /*
    | END OF -> searchQueryType
    |--------------------------------------------------------------------------
    | error
    |--------------------------------------------------------------------------
    |
    | The error functions handles the error display to the end user. 
    | The function takes an array with the folowing information:
    |       
    | @param $resource = array(ERRORCODE, RESOURCES)
    |
    | For errorcode refrences see: ./SophieCore/errorcodes.json
    |
    */

    private function error($resource) {
		# Load the error codes JSON File and remove the comments
        $errorcodes = json_decode($this->deCommentFile("SophieCore/errorcodes.sophie"), true);
        # Get the error message from the JSON file
		$error = $errorcodes["e" . $resource[0]];
        # Shift the array to only keep the provided resources
        $temp = array_shift($resource);

        # Set the http response header for the error
		header($error['http']);
        
        # Compile and print the error message out to the user
		print_r(json_encode(array(
                    "status" => "error",
                    "statuscode" => $error["errorcode"],
                    # Add the resource to the error message
					"message" => vsprintf($error["message"], $resource)))) && die;      # And die the script. 
		
	}

    /*
    | END OF -> error()
    |--------------------------------------------------------------------------
    | actionsArray()
    |--------------------------------------------------------------------------
    |
    | This function extracts the user submitted request parameters from the URI.
    |
    */

    private function actionsArray() {
        if(!empty($_GET['action'])) {
            $this->ACTIONS = explode("/", $_GET['action']);
            
            $temp = array();
            
            foreach($this->ACTIONS as $action) {
                if($action !== "") {
                    $temp[] = $action;
                } else {}
            }
            if(empty($temp)) {
                $this->ACTIONS = false; 
            } else { 
                $this->ACTIONS = $temp; 
            }
            
        } else { 
            $this->ACTIONS = false; 
        }

    }

    /*
    | END OF -> actionsArray()
    |--------------------------------------------------------------------------
    | backupDB()
    |--------------------------------------------------------------------------
    |
    | This functions is the interface between the Core class and the Backup class.
    |
    */

    private function backupDB() {

    }

    /*
    | END OF -> backupDB
    |--------------------------------------------------------------------------
    | queryParameters
    |--------------------------------------------------------------------------
    |
    | This function gets the query parametrs.
    |
    */

    private function queryParameters() {

        $LIMITRESPONSE = false;
        $ORDERRESPONSE = false;

        if(isset($_GET['limit']) && !empty($_GET['limit'])) {
            $LIMITRESPONSE = $_GET['limit'];
        }

        if(isset($_GET['order']) && !empty($_GET['order'])) {
            $ORDERRESPONSE = $_GET['order'];
        }

        if(isset($_GET['']) && !empty($_GET[''])) {

        }

        return($this->QUERYPARAMETERS = array(
            "limit" => $LIMITRESPONSE,
            "order" => $ORDERRESPONSE
        ));
    }

    /*
    | END OF -> queryParamters
    |--------------------------------------------------------------------------
    | setRequestPointer
    |--------------------------------------------------------------------------
    |
    | This function sets the pointer for the requested location in the database
    |
    */

    private function setRequestPointer()   {
        
        if($this->REQUESTFUNCTION == "search") {
            # Loop through the actions array
            foreach($this->ACTIONS as $key => $action) {
                # exclude the query function, database and the search query from the loop
                if($key >= 2 && $key != (count($this->ACTIONS) -1 )){
                    # add the pointer to the variable '$REQUESTPOINTER'
                    $this->REQUESTPOINTER[] = $action;
                }
            }
        } else {
            # Loop through the actions array
            foreach($this->ACTIONS as $key => $action) {
                # exclude the query function and the database from the loop
                if($key >= 2 ){
                    # add the pointer to the variable '$REQUESTPOINTER'
                    $this->REQUESTPOINTER[] = $action;
                }
            }
        }
    }

    /*
    | END OF -> setRequestPointer
    |--------------------------------------------------------------------------
    | deCommentFile
    |--------------------------------------------------------------------------
    |
    | This function removes all the comments from the submitted file. In this
    | case it's used to remove the comments from the JSON config file. 
    |
    |   $file = "location/to/the/file.ext
    |
    */

    private function deCommentFile($file) {

        $data = file_get_contents($file);
        $regex = array(
            "`^([\t\s]+)`ism"=>'',
            "`^\/\*(.+?)\*\/`ism"=>"",
            "`([\n\A;]+)\/\*(.+?)\*\/`ism"=>"$1",
            "`([\n\A;\s]+)//(.+?)[\n\r]`ism"=>"$1\n",
            "`(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+`ism"=>"\n"
            );
            return(preg_replace(array_keys($regex),$regex,$data));
    }

    /*
    | END OF -> deCommentFile
    |--------------------------------------------------------------------------
    | isLike
    |--------------------------------------------------------------------------
    |
    | This Function checks if the needle is in the haystack
    |
    */

    private function isLike($haystack, $needle) {
        
        if (strpos($haystack, $needle) !== false) {
            return true;
        } else {
            return false;
        }

    }

    /*
    | END OF -> isLike
    |--------------------------------------------------------------------------
    | Authenticate
    |--------------------------------------------------------------------------
    |
    | The Auth function checks weighter or not an accurate key & token are set.
    |
    */

    public function Authenticate($type) {
        
        $auth = new \Symion\Authenticate;
        
        if($type == "basic") {
            $status = $auth->basic($this->CORE['users']);
        } elseif($type == "keyToken") {
            $status = $auth->keyToken($this->CORE['users']);
        } elseif($type == "bearer") {
            $status = $auth->bearer($this->CORE['users']);
        }

        if($status['status']) {
            return(array(true, $status['user']));
        } else {
            print("error");
            if($status['message'] == "no user") {
                $this->error(array(0));
            }
        }
		
	}

    /*
    | END OF -> Authenticate
    |--------------------------------------------------------------------------
    | loadDatabase
    |--------------------------------------------------------------------------
    |
    | The loadDatabse function loads the database itself and the associated
    | data into variables for the rest of the script. 
    |
    */

    private function loadDatabase() {
        
        # Get the requested databse from the actions array
        if(isset($this->ACTIONS[1])):
            $this->DBNAME = $this->ACTIONS[1];
        else:
            $this->error(array(1));
        endif;

        # Set the database location
        $this->DBLOCATION = "./databases/$this->DBNAME.sophie";

        /** 
         * Load database into Variable $DBDATA
         * -----------------------------------
        */

        # Check if the database exists
        if(!file_exists($this->DBLOCATION)):
            # Save the database name into $DBNAME
            $this->error(array(2, $this->DBNAME));
        endif;

        # load the database itself into variable $DBDATA
        $this->DBDATA = json_decode(file_get_contents($this->DBLOCATION), true);
        
        /** 
         * Load databse associated data into Variablle $DBASSOC
         * ----------------------------------------------------
        */

        # load the database associated data

    }

    /*
    | END OF -> loadDatabase
    |--------------------------------------------------------------------------
    | getTable
    |--------------------------------------------------------------------------
    |
    | This function navigates to the specified location in the database. 
    |
    */

    private function getTable()   {
        # First Load the database into $database
        $database = $this->DBDATA;
        # Loop through the request pointer array
        foreach($this->REQUESTPOINTER as $location):
            # If the array key exists
            if(array_key_exists($location, $database))  {
                # Move the $database variable to the location
                $database = $database[$location];
            } else {
                # Else, return the error to inform the user
                $this->error(array(14, $location, $this->DBNAME));
            }
        endforeach;
        # An at last return the result
        return $database;
    }

    /*
    | END OF -> getTable
    |--------------------------------------------------------------------------
    | requestHandler
    |--------------------------------------------------------------------------
    |
    | This function handles the requests made by the user. It executes functions
    | to perform the requested actions. 
    |
    */

    public function requestHandler() {

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                
                switch ($this->REQUESTFUNCTION) {
                    case 'search':

                        $response = "";

                        # Check if the requested function needs authenication
                        if($this->CORE['settings']['authZones'][strtolower($_SERVER['REQUEST_METHOD'])][$this->REQUESTFUNCTION]) {
                            # If so, run the authenticate function and give it the auth method
                            $this->Authenticate($this->CORE['settings']['authMethod']);
                        }
                        
                        /**
                         * SearchDB function
                         */

                        $this->setRequestPointer();

                        # Lets start with loading the requested database
                        $this->loadDatabase();
                        
                        if(!empty($this->REQUESTPOINTER)):
                            $data = $this->getTable();
                        else:
                            $data = $this->DBDATA;
                        endif;

                        # First Check if the user submitted a query
                        if(empty($this->searchQueryType())) {
                            # If not, Send an error to inform them
                            $this->error(array(18));
                        }

                            # Check if the user submitted an array or string as the search query
                            if($this->SEARCHQUERY['key'] !== "*") {
                                # Search by Key => Value pair
                            
                                foreach($data as $key => $value):
                                    if(is_array($value)):
                                        if(isset($value[$this->SEARCHQUERY['key']]) && $value[$this->SEARCHQUERY['key']] == $this->SEARCHQUERY['value']) {
                                            $response[] = $data[$key];
                                            # goto response;
                                        } else {
                                            $parent_key = $key;
                                            foreach($value as $key => $value) {
                                                if(is_array($value)){
                                                    if(isset($value[$this->SEARCHQUERY['key']]) && $value[$this->SEARCHQUERY['key']] == $this->SEARCHQUERY['value']) {
                                                        $response[] = $data[$parent_key];
                                                        # goto response;
                                                    } 
                                                }
                                            }
                                        } 
                                    endif;
                                endforeach;

                                if(empty($response)) {
                                    $response = array("no results");
                                    goto response;
                                } elseif (count($response) <= 1) {
                                    $response = $response[0];
                                    goto response;
                                } 

                            } else {
                                # Search by value
                                $response = array("search by value") ;
                            }

                        /**
                         * End of SearchDB function
                         */

                        break;
                    
                    case 'get':
                        $response = "";
                    
                         # Check if the requested function needs authenication
                        if($this->CORE['settings']['authZones'][strtolower($_SERVER['REQUEST_METHOD'])][$this->REQUESTFUNCTION]) {
                            # If so, run the authenticate function and give it the auth method
                            $this->Authenticate($this->CORE['settings']['authMethod']);
                        }
                        
                        $this->setRequestPointer();

                        # Lets start with loading the requested database
                        $this->loadDatabase();
                                            
                        if(!empty($this->REQUESTPOINTER)):
                            $data = $this->getTable();
                        else:
                            $data = $this->DBDATA;
                        endif;

                        # Format the response and return it
                        return($data);

                        break;

                    default:
                        # code...
                        break;
                }
                break;
            
        case 'POST':
                
            break;

        case 'OPTIONS':

                switch ($this->REQUESTFUNCTION) {
                    case 'create':

                        if(isset($this->ACTIONS[1])) {
                            $this->DBNAME = $this->ACTIONS[1];
                        } else {
                            $this->error(array(11));
                        }

                        $dblocation = "databases/$this->DBNAME.sophie";

                        if(file_exists($dblocation)) {
                            $this->error(array(19, $this->DBNAME));
                        }

                        $create = fopen($dblocation, "w");	

                        if($create) {
                            $placeholder = file_get_contents("SophieCore/new_db_placeholder_data.sophie");
                            fwrite($create, $placeholder);

                            $response = array(
                                "succes" => true,
                                "message" => "Database '$this->DBNAME' is succesfuly created!");

                            fclose($create);
                            goto response;
                        } else {
                            close($create);
                            $this->error(array(12, $this->DBNAME));
                        }

                        break;
                    
                    default:
                        # code...
                        break;
                }
                break;
            
        default:
            $this->error(array(9, $_SERVER['REQUEST_METHOD']));
            break;
        }

            # Format the response and return it
            response:
            return($response);

    }

    /*
    | END OF -> requestHandler
    |--------------------------------------------------------------------------
    | getPostBody
    |--------------------------------------------------------------------------
    |
    | This function gets the content of the postbody, checks if it is json.
    | If not Json it'll send an error. Otherwise, No problemo and returns
    | the data.
    |
    */

    private function getPostBody() {
			
		# Retreve the post body
		$postdata = file_get_contents('php://input');
        
        # Check if post body isn't empty 
		if(empty($postdata)) {
			// If the body is empty.. throw error 6
			$this->error(array(7));
		}

		# Check if the content is JSON formated
		if(!$this->isJson($postdata)) {	
			// If the body is not in json format.. throw error 7 + Json error
			$this->error(array(8, $this->isJson($postdata)));
		} 
			
		# Decode the Json
		$postdata = json_decode($postdata, true);
		
		# Return the array
		return($postdata);
		
    }
    
    /*
    | END OF -> getPostData
    |--------------------------------------------------------------------------
    | NEW FUNCTION
    |--------------------------------------------------------------------------
    |
    | FUNCTION DESCRIPTION
    |
    */

    public function getActions()
    {
        return($this->ACTIONS);
    }

    /*
    | END OF -> getActions
    |--------------------------------------------------------------------------
    | __destruct
    |--------------------------------------------------------------------------
    |
    | The cleanup after yourself
    |
    */

    public function __destruct() {
        clearstatcache();
    }

}
