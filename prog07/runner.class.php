<?php
session_start();
/* require "form.php";
$form_generator = new Form(); */

//<a class="btn btn-primary" href="customer.php?fun=add_user">Join (New Volunteer)</a>

class Runner { 	
    public $id;
    public $firstname;
	public $lastname;
    public $email;
	public $retypeEmail;
	public $password; // text from HTML form
	public $password_hashed; // hashed password
	public $race = "race name";
	public $race_time = "00:00:00.00";
	public $race_location = "location";
	public $race_date = "mm/dd/yyyy";
    private $noerrors = true;
    private $nameError = null;
    private $emailError = null;
	private $passwordError = null;
    private $title = "Runner";
    private $tableName = "runners";
	public  $testID;
    
    function display_create() // display "create" form
	{ 
        $this->generate_html_top (1);
		$this->generate_select("Event");
        //$this->generate_form_group("Event", $this->nameError, "", "autofocus onfocus='this.select()'","text","event name");
		$this->generate_form_group("Result", $this->nameError, "","","text","00:00:00.00");
		$this->generate_form_group("Location", $this->nameError, "","","text","location");
		$this->generate_form_group("Date", $this->nameError, "","","date","yyyy-mm-dd");
        $this->generate_html_bottom (1);
    } // end function create_record()
    
    function display_read($id) // display "read" form
	{ 
        $this->select_db_record($id);
        $this->generate_html_top(2);
        $this->generate_form_group("name", $this->nameError, $this->name, "disabled");
        $this->generate_form_group("email", $this->emailError, $this->email, "disabled");
        $this->generate_html_bottom(2);
    } // end display_delete
    
    function display_update($id) // display "update" form
	{ 
        if($this->noerrors) $this->select_db_record($id);
        $this->generate_html_top(3, $id);
        $this->generate_form_group("First", $this->nameError, $this->firstname, "autofocus onfocus='this.select()'","text","first name");
		$this->generate_form_group("Last", $this->nameError, $this->lastname,"","text","last name");
        $this->generate_form_group("Email", $this->emailError, $this->email,"","text","johndoe@email.com");
        $this->generate_html_bottom(3);
    } // end display_update
    
    function display_delete($id) // display "read" form
	{
        $this->select_db_record($id);
        $this->generate_html_top(4, $id);
        $this->generate_form_group("firstname", $this->nameError, $this->firstname, "disabled");
		$this->generate_form_group("lastname", $this->nameError, $this->lastname, "disabled");
        $this->generate_form_group("email", $this->emailError, $this->email, "disabled");
        $this->generate_html_bottom(4);
    } // end display_delete */
	
 	function add_user()
	{
		if($this->retypeEmail == $this->email)
		{
			//echo "<script type='text/javascript'>alert('$email');</script>";
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "SELECT * FROM $this->tableName WHERE email = ? LIMIT 1";
			$q = $pdo->prepare($sql);
			$q->execute(array($this->email));
			$data = $q->fetch(PDO::FETCH_ASSOC);

			if($data) { // if successful login, user already exists			
				Database::disconnect();
				$_SESSION["addError"] = "A user with this email already exists!";
				header("Location: login.php");
				exit();
			}
			else { //add the user
				$pdo = Database::connect();
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->password_hashed = MD5($this->password);
				$sql = "INSERT INTO $this->tableName (email,password) values(?, ?)";
				$q = $pdo->prepare($sql);
				$q->execute(array($this->email, $this->password_hashed));
				Database::disconnect();
				$_SESSION["loginError"] = null;
				$this->login(1);
			}
		}
		else
		{
			$_SESSION["addError"] = "Please confirm email.";
			header("Location: login.php");
			exit();
		}
		
	} //end add_user
	
	function login($addUser)
	{
			$this->password_hashed = MD5($this->password);
			$labelError = "";
			
			// verify the username/password
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "SELECT * FROM runners WHERE email = ? AND password = ? LIMIT 1";
			$q = $pdo->prepare($sql);
			$q->execute(array($this->email,$this->password_hashed));
			$data = $q->fetch(PDO::FETCH_ASSOC);

			if($data) { // if successful login set session variables			
				$_SESSION['tJHSQRuoNnWUwLR'] = $data['id'];
				$this->testID = $data['id'];
				$currentID = $data['id'];
				Database::disconnect();
				$_SESSION["signInError"] = null;
				switch ($addUser)
				{
					case 0:
						header("Location: action_redirect.php");
						break;
					case 1: 
						header("Location: action_redirect.php?fun=display_update_form&id=$currentID");
						break;
				}
				exit();
			}
			else { // display error message
				Database::disconnect();
				$_SESSION["signInError"] = "Incorrect username/password";
				header("Location: login.php");
				exit();
			}
	}
	/*
     * This method inserts one record into the table, 
     * and redirects user to List, IF user input is valid, 
     * OTHERWISE it redirects user back to Create form, with errors
     * - Input: user data from Create form
     * - Processing: INSERT (SQL)
     * - Output: None (This method does not generate HTML code,
     *   it only changes the content of the database)
     * - Precondition: Public variables set (name, email, mobile)
     *   and database connection variables are set in datase.php.
     *   Note that $id will NOT be set because the record 
     *   will be a new record so the SQL database will "auto-number"
     * - Postcondition: New record is added to the database table, 
     *   and user is redirected to the List screen (if no errors), 
     *   or Create form (if errors)
     */
    function insert_db_record () {
        if ($this->fieldsAllValid()) { // validate user input
            // if valid data, insert record into table
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->password_hashed = MD5($this->password);
			// safe code
            $sql = "INSERT INTO $this->tableName (name,email,password) values(?, ?, ?)";
            $q = $pdo->prepare($sql);
			// safe code
            $q->execute(array($this->name, $this->email, $this->password_hashed));
			// dangerous code
			//$q->execute(array());
            Database::disconnect();
            header("Location: action_redirect.php"); // go back to "list"
        }
        else {
            // if not valid data, go back to "create" form, with errors
            // Note: error fields are set in fieldsAllValid ()method
            $this->create_record(); 
        }
    } // end function insert_db_record
    
	function insert_race()
	{
		//need to validate later...
		if (true) { // validate user input
            // if valid data, insert record into table
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "INSERT INTO `races` (`runner_id`, `race`, `race_time`, `location`, `date`) VALUES (?, ?, ?, ?, ?)";
            //$sql = "INSERT INTO races (runner_id, race, race_time, location, date) values(?, ?, ?, ?, ?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($this->testID, $this->race, $this->race_time, $this->race_location, $this->race_date));
            Database::disconnect();
            header("Location: action_redirect.php?fun=list_races&id=$this->testID"); // go back to "list"
        }
        else {
            // if not valid data, go back to "create" form, with errors
            // Note: error fields are set in fieldsAllValid ()method
            $this->display_create(); 
        }
	} //end insert_race
	
	function list_races($id)
	{
		echo "<!DOCTYPE html>
        <html>
            <head>
				<link rel='icon' href='track_and_field.png' type='image/png'/>
                <title>Races</title>";
        echo "
                <meta charset='UTF-8'>
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>
                    ";  
        echo "
            </head>
            <body>
                <div class='container'>
                    <p class='row'>
                        <h3>Results</h3>
                    </p>
                    <div class='row'>
                        <table class='table table-striped table-bordered'>
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Result</th>
                                    <th>Location</th>
									<th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                    ";
        $pdo = Database::connect();
        $sql = "SELECT * FROM races where runner_id = $id";
        foreach ($pdo->query($sql) as $row) {
            echo "<tr>";
			echo "<td>". $row["race"] . "</td>";
            echo "<td>". $row["race_time"] . "</td>";
			echo "<td>". $row["location"] . "</td>";
			echo "<td>". $row["date"] . "</td>";
            echo "</td>";
            echo "</tr>";
        }
        Database::disconnect();        
        echo "
                            </tbody>
                        </table>
                    </div>
					<a class='btn btn-success' href='action_redirect.php'>Back</a>
                </div>
				<div class='form-actions'>
					
                </div>
            </body>

        </html>
                    "; 
	} //end list_races
	
    private function select_db_record($id) {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM $this->tableName where id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        $data = $q->fetch(PDO::FETCH_ASSOC);
        Database::disconnect();
        $this->firstname = $data['first_name'];
		$this->lastname = $data['last_name'];
        $this->email = $data['email'];
    } // function select_db_record()
    
    function update_db_record ($id) {
        $this->id = $id;
        if ($this->fieldsAllValid()) {
            $this->noerrors = true;
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "UPDATE $this->tableName  set first_name = ?, last_name = ?, email = ? WHERE id = ?";
            $q = $pdo->prepare($sql);
            $q->execute(array($this->firstname,$this->lastname,$this->email,$this->id));
            Database::disconnect();
            header("Location: action_redirect.php");
        }
        else {
            $this->noerrors = false;
            $this->display_update($id);  // go back to "update" form
        }
    } // end function update_db_record 
    
    function delete_db_record($id) {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "DELETE FROM $this->tableName WHERE id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        Database::disconnect();
        header("Location: logout.php");
    } // end function delete_db_record()
    
    private function generate_html_top ($fun, $id=null) {
        switch ($fun) {
            case 1: // create
                $funWord = "Add Result"; $funNext = "insert_race"; 
                break;
            case 2: // read
                $funWord = "View Races"; $funNext = "display_read()"; 
                break;
            case 3: // update
                $funWord = "Update"; $funNext = "update_db_record&id=" . $id; 
                break;
            case 4: // delete
                $funWord = "Delete"; $funNext = "delete_db_record&id=" . $id; 
                break;
            default: 
                echo "Error: Invalid function: generate_html_top()"; 
                exit();
                break;
        }
        echo "<!DOCTYPE html>
        <html>
            <head>
				<link rel='icon' href='track_and_field.png' type='image/png'/>
                <title>$funWord</title>
                    ";
        echo "
                <meta charset='UTF-8'>
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>
                <style>label {width: 5em;}</style>
                    "; 
        echo "
            </head>";
        echo "
            <body>
                <div class='container'>
                    <div class='span10 offset1'>
                        <p class='row'>
                            <h3>$funWord</h3>
                        </p>
                        <form class='form-horizontal' action='action_redirect.php?fun=$funNext' method='post'>                        
                    ";
    } // end function generate_html_top() */
    
    private function generate_html_bottom ($fun) {
        switch ($fun) {
            case 1: // create
                $funButton = "<button type='submit' class='btn btn-success'>Add</button>"; 
                break;
            case 2: // read
                $funButton = "";
                break;
            case 3: // update
                $funButton = "<button type='submit' class='btn btn-warning'>Update</button>";
                break;
            case 4: // delete
                $funButton = "<button type='submit' class='btn btn-danger'>Delete</button>"; 
                break;
            default: 
                echo "Error: Invalid function: generate_html_bottom()"; 
                exit();
                break;
        }
        echo " 
                            <div class='form-actions'>
                                $funButton
                                <a class='btn btn-secondary' href='action_redirect.php'>Back</a>
                            </div>
                        </form>
                    </div>

                </div> <!-- /container -->
            </body>
        </html>
                    ";
    } // end function generate_html_bottom()
    
	private function generate_select($label)
	{
		echo "<div class='form-group";
        echo !empty($labelError) ? ' alert alert-danger ' : '';
        echo "'>";
        echo "<label class='control-label'>$label &nbsp;</label>";
		echo "<select name=\"Event\">
				<option value=\"60m\">60m</option>
				<option value=\"60m Hurdles\">60m Hurdles</option>
				<option value=\"100m\">100m</option>				
				<option value=\"110m Hurdles\">110m Hurdles</option>
				<option value=\"200m\">200m</option>
				<option value=\"400m\">400m</option>
				<option value=\"400m Hurdles\">400m Hurdles</option>
				<option value=\"800m\">800m</option>
				<option value=\"1500m\">1500m</option>
				<option value=\"Mile\">Mile</option>
				<option value=\"3000m\">3000m</option>
				<option value=\"3000m Steeple\">3000m Steeple</option>
				<option value=\"5000m\">5000m</option>
				<option value=\"10000m\">10000m</option>
				<option value=\"4x1\">4x1</option>
				<option value=\"4x4\">4x4</option>
				<option value=\"DMR\">DMR</option>
				<option value=\"Pole Vault\">Pole Vault</option>
				<option value=\"High Jump\">High Jump</option>
				<option value=\"Long Jump\">Long Jump</option>
				<option value=\"Triple Jump\">Triple Jump</option>
				<option value=\"Shot Put\">Shot Put</option>
				<option value=\"Discus\">Discus</option>
				<option value=\"Hammer\">Hammer</option>
				<option value=\"Javelin\">Javelin</option>
				<option value=\"Pentathlon\">Pentathlon</option>
				<option value=\"Heptathlon\">Heptathlon</option>
				<option value=\"Decathlon\">Decathlon</option>
			  </select>";
		echo !empty($val) ? $val : '';
        if (!empty($labelError)) {
            echo "<span class='help-inline'>";
            echo "&nbsp;&nbsp;" . $labelError;
            echo "</span>";
        }
        //echo "</div>"; // end div: class='controls'
        echo "</div>"; // end div: class='form-group'
	}
	
	 private function generate_form_group ($label, $labelError, $val, $modifier="", $fieldType="text", $filler="") {
        echo "<div class='form-group";
        echo !empty($labelError) ? ' alert alert-danger ' : '';
        echo "'>";
        echo "<label class='control-label'>$label &nbsp;</label>";
        //echo "<div class='controls'>";
        echo "<input "
            . "name='$label' "
            . "type='$fieldType' "
            . "$modifier "
            . "placeholder='$filler' "
            . "value='";
        echo !empty($val) ? $val : '';
        echo "'>";
        if (!empty($labelError)) {
            echo "<span class='help-inline'>";
            echo "&nbsp;&nbsp;" . $labelError;
            echo "</span>";
        }
        //echo "</div>"; // end div: class='controls'
        echo "</div>"; // end div: class='form-group'
    } // end function generate_form_group()
	
	 private function fieldsAllValid() {
        $valid = true;
        
        if (empty($this->email)) {
            $this->emailError = 'Please enter Email Address';
            $valid = false;
        } 
        else if ( !filter_var($this->email,FILTER_VALIDATE_EMAIL) ) {
            $this->emailError = 'Please enter a valid email address: me@mydomain.com';
            $valid = false;
        }
		if (empty($this->firstname)) {
            $this->nameError = 'Please enter first name';
            $valid = false;
        }
		if (empty($this->lastname)) {
            $this->nameError = 'Please enter last name';
            $valid = false;
        }
        return $valid;
		
    } // end function fieldsAllValid()  */
    
    function list_records() {
        echo "<!DOCTYPE html>
        <html>
            <head>
				<link rel='icon' href='track_and_field.png' type='image/png'/>
                <title>Athletes</title>
                    ";
        echo "
                <meta charset='UTF-8'>
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>
                    ";  
        echo "
            </head>
            <body>
                <a href='https://github.com/cis355/PhpProject1' target='_blank'>Github</a><br />
                <div class='container'>
                    <p class='row'>
						
                        <h3><img width=50 height=50 src=\"track_and_field.png\"/>		Athletes</h3>
                    </p>
                    <p>
                        <a href='action_redirect.php?fun=add_race&id=$this->testID' class='btn btn-success'>Add a Result</a>
						<a href='action_redirect.php?fun=display_update_form&id=$this->testID' class='btn btn-success'>Update Account</a>
						<a href='logout.php' class='btn btn-warning'>Logout</a> 
						<a href='action_redirect.php?fun=display_delete_form&id=$this->testID' class='btn btn-danger'>Delete Account</a>
					</p>
                    <div class='row'>
                        <table class='table table-striped table-bordered'>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <!--<th>Email</th>-->
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                    ";
        $pdo = Database::connect();
        $sql = "SELECT * FROM $this->tableName ORDER BY last_name";
        foreach ($pdo->query($sql) as $row) {
            echo "<tr>";
			$fullName = $row["last_name"] . ", " . $row["first_name"];
			echo "<td style=\"text-align:left\">". $fullName . "</td>";
            //echo "<td>". $row["email"] . "</td>";
            echo "<td width=75>";
            echo "<a class='btn btn-info' href='action_redirect.php?fun=list_races&id=".$row["id"]."'>View</a>";
            //echo "&nbsp;";
            //echo "<a class='btn btn-warning' href='action_redirect.php?fun=display_update_form&id=".$row["id"]."'>Update</a>";
            //echo "&nbsp;";
            //echo "<a class='btn btn-danger' href='action_redirect.php?fun=display_delete_form&id=".$row["id"]."'>Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
        Database::disconnect();        
        echo "
                            </tbody>
                        </table>
                    </div>
                </div>

            </body>

        </html>
                    ";  
    } // end function list_records()
    
} // end class Customer