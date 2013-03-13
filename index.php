<?php
/***************************************************

 Smooth Student Request System (SSRS)

 This file is part of SSRS, which is licensed
 under the Creative Commons: Attribution,
 Non-Commercial, Share Alike license (see
 http://creativecommons.org/licenses/by-nc-sa/3.0/)
 
 The first version was developed by
 Gjalt-Jorn Peters for the Dutch Open University
 in March 2013.
 
***************************************************/

  // Use $maintenance to take the site offline
  $maintenance = false;

  define("FILES_PATH", "files/");
  define("TEMPFILE_PATH", "temp/");
  define("APPNAME", "SSRS");
  
  include('db.php'); /* Looks like this:
<?php
  define("MYSQL_HOST", "hostname");
  define("MYSQL_DB", "databasename");
  define("MYSQL_USER", "username");
  define("MYSQL_PASSWORD", "password");
?>*/

  include('admin_emails.php'); /* Looks like this:
<?php
  define("ERRORS_EMAIL", "email@address.com");
  define("REPORTS_EMAIL", "email@address.com");
  define("FROM_EMAIL", "email@address.com");
  define("FROM_NAME", "email@address.com");
?>*/

  require_once('class.phpmailer.php');
  require_once('class.random.php');
  
  class Course {
    public $id;
    public $name;
    public $records;
    public $importsyntax;
  }

  class Datafile {
    public $id;
    public $file;
    public $courses_id;
  }

  function ErrorHandler ($error) {
    mail(ERRORS_EMAIL, "Error in ".APPNAME." script", $error);
    echo($error);
  }
  
  // Connect to database and load submissions per supervisor

  try {
    # MySQL with PDO_MYSQL
    $dbHandle = new PDO("mysql:host=".MYSQL_HOST.";dbname=".MYSQL_DB, MYSQL_USER, MYSQL_PASSWORD);

    $getCourses = $dbHandle->prepare("SELECT * FROM `courses`;");
    $getCourses->setFetchMode(PDO::FETCH_CLASS, 'Course');

    $getDatafiles = $dbHandle->prepare("SELECT * FROM `datafiles` WHERE `courses_id` = :courses_id;");
    $getDatafiles->setFetchMode(PDO::FETCH_CLASS, 'Datafile');

    $setRequest = $dbHandle->prepare("INSERT INTO requests (name, nr, email, courses_id) VALUE (:name, :nr, :email, :courses_id);");
    
    // Get the teachers and the courses
    $getCourses->execute();
    while($obj = $getCourses->fetch()) {
      $courses[$obj->id] = $obj;
    }
  }
  catch(PDOException $e) {
    errorHandler($e->getMessage());
  }
  
  // Initialize log and reset errorBlock
  $log = APPNAME." request\n\nCurrent date is ".date("d-n-Y").".\n\n";
  $errorBlock = "";

  if (isset($_GET['action']) && ($_GET['action']=="submit")) {

    if (isset($_POST['course']) &&
        isset($_POST['name']) &&
        isset($_POST['nr']) &&
        isset($_POST['email']) ) {
      $course=trim($_POST['course']);
      $name = trim($_POST['name']);
      $nr = trim($_POST['nr']);
      $email = trim($_POST['email']);
      if (array_key_exists($course, $courses)) {
        $infoBlock = "<li>Cursus: {$courses[$course]->name}</li>";
        $infoBlock .= "<li>Naam: $name</li>";
        $infoBlock .= "<li>Nummer: $nr</li>";
        $infoBlock .= "<li>Email: $email</li>";
        $log .= "Received request for course: $course ({$courses[$course]->name}) from $name ($nr, $email).\n";
        $selectedCourse = $courses[$course];
      }
      else {
        $errorBlock .= "<li>Er is een cursus geselecteerd die niet bestaat (nummer: $course).</li>";
        errorHandler("Non-existant course POSTed (number: $course)!");
      }
    }
    else {
      $errorBlock .= "<li>Er is geen cursus geselecteerd.</li>";
    }
    
    if (!($errorBlock)) {
    
      // We can select a datafile and then take a sample.
      // First, load the datafiles and check how many we have.
      
      // Generate an empty array
      $datafiles = array();
      // Reset key counter for array
      $currentDatafile = 0;
      // Execute query
      $getDatafiles->execute(array(':courses_id' => $selectedCourse->id));
      // Fetch results and store in array
      while($obj = $getDatafiles->fetch()) {
        // Increment key counter for array
        $currentDataFile++;
        // Store datafile object in array
        $datafiles[$currentDataFile] = $obj;
      }
      // Store number of datafiles
      $nrOfDatafiles = count($datafiles);
      
      // Extract the last two numbers of the student id
      $lastNr = substr($nr, -2, 2);
      
      // Then use this number to select a datafile
      $selectedDatafile = $datafiles[($lastNr % $nrOfDatafiles) + 1];
      
      // Load the selected datafile
      $datafile = file(FILES_PATH.$selectedDatafile->file);
      if ($datafile === false) {
        // Error: file didn't exist!
        $errorBlock .= "<li>Het gespecificeerde bestand bestaat niet (".FILES_PATH.$selectedDatafile->file.")!</li>";
        errorHandler("Non-existant file specified (".FILES_PATH.$selectedDatafile->file.")!");
      }
      else {
        $nrOfRecordsInFile = count($datafile);
        // Check whether number of records in the file is larger than
        // $records (number of records to extract)
        if ($nrOfRecordsInFile <= $selectedCourse->records) {
          $errorBlock .= "<li>Het gespecificeerde bestand bevat onvoldoende records ($nrOfRecordsInFile, \$records = ".$selectedCourse->records.")!</li>";
          errorHandler("Specified file contains too few records ($nrOfRecordsInFile, \$records = ".$selectedCourse->records.")!");
        }
        else {
          // Set student id as random number seed
          $randomNrGenerator = new Random();
          $randomNrGenerator->seed($nr);
          
          // Generate an array with $records unique keys from the $datafile array
          // First generate an empty array
          $randomKeys = array();
          $selectedFile = array();
          // Loop $records times
          for ($i = 0; $i <= $selectedCourse->records - 1; $i++) {
            // Generate a random number (line in file) between
            // 0 and $nrOfRecordsInFile and store it in the array
            $newKey = $randomNrGenerator->num(0, $nrOfRecordsInFile-1);
            // If we already selected this line from the file,
            // select a different key until we have a new unique one
            while (in_array($newKey, $randomKeys)) {
              $newKey = $randomNrGenerator->num(0, $nrOfRecordsInFile-1);
            }
            // Add new key to array
            $randomKeys[$i] = $newKey;
            // Add selected line to new array
            $selectedFile[] = $datafile[$randomKeys[$i]];
          }

          // Save file and store path in $storedDataFile
          $storedDataFileName = "Data ".$selectedCourse->name." ($nr).csv";
          $storedDataFile = TEMPFILE_PATH.$storedDataFileName;
          if (!file_put_contents($storedDataFile, $selectedFile)) {
            $errorBlock .= "<li>Fout bij wegschrijven datafile naar \"$storedDataFile\"!</li>";
            errorHandler("Error while storing datafile in \"$storedDataFile\"!");
          }
          
        }
      }

      // Load syntax file for import
      if (!($errorBlock)) {
        $syntaxfile = file_get_contents(FILES_PATH.$selectedCourse->importsyntax);
        if ($syntaxfile === false) {
          // Error: file didn't exist!
          $errorBlock .= "<li>Het syntaxbestand voor import bestaat niet (".FILES_PATH.$selectedCourse->importsyntax.")!</li>";
          errorHandler("Non-existant syntax file for import (".FILES_PATH.$selectedCourse->importsyntax.")!");
        }
        else {
          // Replace filename token with filename to import
          $syntaxfile = str_replace("[GJYP_FILENAME]", $storedDataFileName, $syntaxfile);
          // Store syntax file
          $storedSyntaxFile = TEMPFILE_PATH.$selectedCourse->importsyntax;
          if (!file_put_contents($storedSyntaxFile, $syntaxfile)) {
            $errorBlock .= "<li>Fout bij wegschrijven syntaxfile naar \"$storedSyntaxFile\"!</li>";
            errorHandler("Error while storing syntaxfile in \"$storedSyntaxFile\"!");
          }
        }
      }
      
      if (!($errorBlock)) {
        
        // Email file to participant
        $mail = new PHPMailer(true);
        try {
          $mail->AddAddress($email, $name);
          $mail->AddReplyTo(FROM_EMAIL, FROM_NAME);
          $mail->SetFrom(FROM_EMAIL, FROM_NAME);
          $mail->Subject = "Datafile voor ".$selectedCourse->name;
          $mail->AddAttachment($storedDataFile);
          $mail->AddAttachment($storedSyntaxFile);
          $mail->Body = "Beste {$name},

Bij deze de datafile voor {$selectedCourse->name}.

Deze datafile is voor $name (nummer $nr).

Met vriendelijke groet,

De website op http://oupsy.nl
";
        
          if (!(isset($_GET['debug']))) {
            $mail->Send();
            $log .= "Mail sent.\n";
          }
          else {
            $log .= "Mail NOT sent because \$_GET['debug'] was set.\n";
          }
          
          $infoBlock .= $successMessage;

          // Store request in database
          try {
            $setRequest->execute(array("name" => $name, "nr" => $nr, "email" => $email, "courses_id" => $selectedCourse->id));
            // Handle errors
            if(!($setRequest->errorCode() == 0)) {
              $errors = $setRequest->errorInfo();
              $infoBlock .= "<li>Fout in de opslag in de database: {$errors[2]}</li>";
              $log .= "Error while saving request in the database! Error: '{$errors[2]}'.\n";
            }
            else {
              $infoBlock .= "<li>Verzoek succesvol opgeslagen in de database!</li>";
              $log .= "Request succesfully saved in the database!\n";
            }
          }
          catch(PDOException $e) {
            errorHandler($e->getMessage());
          }
        }
        catch (phpmailerException $e) {
          $errorBlock .= "<li>Het versturen van de email is mislukt! De fout die de server gaf was: '".$e->errorMessage()."'</li>";
          $log .= "Error while sending mail! Error message: '".$e->errorMessage()."'.\n";
        }
        catch (Exception $e) {
          $errorBlock .= "<li>Het versturen van de email is mislukt! De fout die de server gaf was: '".$e->getMessage()."'</li>";
          $log .= "Error while sending mail! Error message: '".$e->getMessage()."'.\n";
        }
        
        // This statement shows the log on the screen.
        if (isset($_GET['debug'])) {
          echo(nl2br($log));
        }
        // This statement emails the log.
        mail(REPORTS_EMAIL, "Report of ".APPNAME." script", $log);

      }
      
      // Delete $storedDataFile
      unlink($storedDataFile);
      unlink($storedSyntaxFile);
    }
    
    // Generate html page

    include("view_header.php");
    
    if ($infoBlock) {
      echo("<div class=\"infoblock\"><strong>Ontvangen informatie:</strong><ul>".$infoBlock."</ul></div>");
    }
    if ($errorBlock) {
      echo("<div class=\"errorblock\">FOUT:<ul>".$errorBlock."</ul></div>");
      include("view_intro.php");
      include("view_form.php");
    }

  }
  else if (($maintenance) && !(isset($_GET['admin']))) {
    include("view_header.php");
    include("view_maintenance.php");
  }
  else {
    include("view_header.php");
    include("view_intro.php");
    include("view_form.php");
  }

  if (isset($_GET['debug'])) {
    echo("NOTE: IN DEBUG MODE!");
  }

  include("view_footer.php");

  // Close connection with database
  $dbHandle = null;
  
?>