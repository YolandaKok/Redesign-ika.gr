<?php header("content-type: text/html;charset=utf-8") ?>
<?php
/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
require_once 'login.php';
$conn = new mysqli($cleardb_server,$cleardb_username,$cleardb_password,$cleardb_db);
// Check Connection
if ($conn->connect_error) die ($conn->connect_error);
// Escape user inputs for security
$username = mysqli_real_escape_string($conn, $_REQUEST['username']);
$password = mysqli_real_escape_string($conn, $_REQUEST['password']);
$firstname = mysqli_real_escape_string($conn, $_REQUEST['firstname']);
$lastname = mysqli_real_escape_string($conn, $_REQUEST['lastname']);
$email = mysqli_real_escape_string($conn, $_REQUEST['email']);
$ama = mysqli_real_escape_string($conn, $_REQUEST['ama']);
$amka = mysqli_real_escape_string($conn, $_REQUEST['amka']);
$HaveInsurance = mysqli_real_escape_string($conn, $_REQUEST['HaveInsurance']);

if($HaveInsurance == 1) {
  $HaveInsurance = 1;
  $HaveRetirement = 0;
}
else {
  $HaveInsurance = 0;
  $HaveRetirement = 1;
}

// Hash password for security
$password = password_hash ( $password , PASSWORD_BCRYPT);

$money = 700;

// Insert greek characters in mysql database
mysqli_query($conn, "SET NAMES 'utf8'");

// attempt insert query execution
$sql = "INSERT INTO users (username, password, firstname, lastname, email, ama, amka, HaveInsurance, HaveRetirement, money) VALUES ('$username', '$password', '$firstname', '$lastname', '$email', '$ama', '$amka', '$HaveInsurance', '$HaveRetirement', '$money')";
if(mysqli_query($conn, $sql)){
    echo "Records added successfully.";
} else {
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
}

$query = "SELECT * FROM users WHERE username = '$username'";
$res= $conn->query($query);
$res->data_seek(0);
$row = $res->fetch_assoc();

$id = $row['id'];

session_start();
$_SESSION['user'] = $username;
$_SESSION['id'] = $id;
// close connection
mysqli_close($conn);
// Redirect to homepage
header("Location: index.php");
?>
