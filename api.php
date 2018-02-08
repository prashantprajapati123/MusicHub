<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "musicbox";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(array('status' => 'error', 'msg' => 'database not connected'));
} 
else 
{
    if(function_exists($_GET['f'])) {
	   $_GET['f']();
	}	
}


function index() {
	echo json_encode(array('status' => 'success', 'msg' => 'this is index page.'));
}

function getPlayList() {
	echo json_encode(array('status' => 'success', 'msg' => 'this is index page.'));
}

function getAlbum() {
	echo json_encode(array('status' => 'success', 'msg' => 'this is index page.'));
}

function getPost() {

	$sql = "SELECT `post_title` FROM wp_posts";
    $result = $GLOBALS['conn']->query($sql);
    $data = array();
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(array('status' => 'success', 'data' => $data));
    } else {
        $data = array();
        echo json_encode(array('status' => 'success', 'data' => $data));
    }
	//echo json_encode(array('status' => 'success', 'msg' => 'this is index page.'));
}

function getArtist() {
	$user_id = $_REQUEST['id'];

	$userinfo = array('first_name' => 'Rohit', 'last_name' => 'Kapoor');
	echo json_encode(array('status' => 'success', 'userinfo' => $userinfo));
}

function getLatestVideos() {
	echo json_encode(array('status' => 'success', 'msg' => 'this is index page.'));
}

$conn->close();
?>

