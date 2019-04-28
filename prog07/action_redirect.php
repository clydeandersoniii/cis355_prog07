<?php
session_start();

// include the class that handles database connections
require "database.php";

// include the class containing functions/methods for "customer" table
// Note: this application uses "customer" table, not "cusotmers" table
require "runner.class.php";
$runner = new Runner();

//join
if(isset($_POST['email']))      $runner->email = htmlspecialchars($_POST['email']);
if(isset($_POST['retypeEmail']))	$runner->retypeEmail = htmlspecialchars($_POST['retypeEmail']);
if(isset($_POST['password']))     $runner->password = $_POST['password'];

// set active record field values, if any 
// (field values not set for display_list and display_create_form)
if(isset($_GET["id"]))          $id = $_GET["id"]; 
if(isset($_POST['First']))       $runner->firstname = htmlspecialchars($_POST['First']);
if(isset($_POST['Last']))       $runner->lastname = htmlspecialchars($_POST['Last']);
if(isset($_POST['Email']))      	$runner->email = htmlspecialchars($_POST['Email']);

if(isset($_POST['Event']))       $runner->race = $_POST['Event'];
if(isset($_POST['Result']))       $runner->race_time = $_POST['Result'];
if(isset($_POST['Location']))   $runner->race_location = $_POST['Location'];
if(isset($_POST['Date']))       $runner->race_date = $_POST['Date'];

if(isset($_SESSION["tJHSQRuoNnWUwLR"])) $runner->testID = $_SESSION["tJHSQRuoNnWUwLR"]; 

if(!isset($_SESSION["tJHSQRuoNnWUwLR"])) // if "user" NOT set,
{ 
		$fun = $_GET["fun"];
		if($fun == "login")
		{
			$runner->login(0);
		}
		elseif($fun == "add_user")
		{
			$runner->add_user();
		}
		else
		{
			session_destroy(); // --unnecessary?
			header('Location: login.php');     // go to login page
			exit();
		}
}



if (isset($_GET["fun"]))
{
	$fun = $_GET["fun"];
}
else
{
	$fun = "display_list"; 
} // end if/else
 


switch ($fun) 
{
    case "display_list":        $runner->list_records();
        break;
    case "add_race": 			$runner->display_create($id); 
        break;
	case "insert_race": 		$runner->insert_race();
		break;
	case "list_races":			$runner->list_races($id);
		break;
    case "display_read_form":   $runner->display_read($id); 
        break;
    case "display_update_form": $runner->display_update($id);
        break;
    case "display_delete_form": $runner->display_delete($id); 
        break;
    case "insert_db_record":    $runner->insert_db_record(); 
        break;
    case "update_db_record":    $runner->update_db_record($id);
        break;
    case "delete_db_record":    $runner->delete_db_record($id);
        break;
    default: 
        echo "Error: Invalid function call (action_redirect.php)";
        exit();
        break;
} // end switch
?>
