<?php
header("Access-Control-Allow-Origin: *");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "emailapp";

global $conn;

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_POST['UpdateProfile'])) {
    $fullname = $_POST['fullname'];
    $country = $_POST['country'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $username = $_POST['username'];

    if(isset($_FILES['image'])){
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageData = file_get_contents($imageTmpName);
        $query = "UPDATE users SET image = ? WHERE username = '$username'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $imageData);
        $stmt->execute();
    
        if ($stmt->affected_rows > 0) {
            echo json_encode(array('status' => 'success', 'message' => 'image Updated Successfully'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Error updating profile'));
        }
    
        $stmt->close();
    }


    $query = "UPDATE users SET country = ?, name = ?, address = ?, phoneNumber = ? WHERE username = ?";
    
    $stmt = $conn->prepare($query);

    $stmt->bind_param("sssss", $country, $fullname, $address, $phone, $username);

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(array('status' => 'success', 'message' => 'Updated Successfully'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error updating profile'));
    }

    $stmt->close();
}




if(isset($_POST['getting'])){
    $username = $_POST['username'];

    $getUsers = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($getUsers);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fullname = $row['name'];
        $country = $row['country'];
        $address = $row['address'];
        $phone = $row['phoneNumber'];
        $imageData = $row['image'];
        echo json_encode(array('status' => 'success', 'image' =>base64_encode($imageData),'fullname'=>$fullname,'country'=>$country,'address'=>$address,'phone'=>$phone));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error fetching image'));
    }
}

if(isset($_POST['DeleteImg'])){
    $username = $_POST['username'];

    // Update the SQL query to use placeholders
    $updateQuery = "UPDATE users SET image = null WHERE username = ?";
    
    // Prepare the statement
    $stmt = $conn->prepare($updateQuery);

    // Bind parameter
    $stmt->bind_param("s", $username);

    // Execute the statement
    $stmt->execute();

    // Check for affected rows
    if ($stmt->affected_rows > 0) {
        echo json_encode(array('status' => 'success', 'message' => 'Image deleted successfully'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error deleting image'));
    }

    // Close the statement
    $stmt->close();
}



$conn->close();
?>
