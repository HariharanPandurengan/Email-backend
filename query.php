<?php

session_start();

require("database.php"); 

$getUsers = "SELECT * FROM users";
$resultUsers = $conn->query($getUsers);

if ($resultUsers->num_rows > 0) {
    $dataUsers = array();
    while ($rowUsers = $resultUsers->fetch_assoc()) {
        $userData = $rowUsers;

        $imageData = base64_encode($rowUsers['image']);

        unset($userData['image']);

        $userData['imageData'] = $imageData;

        $dataUsers[] = $userData;
    }
} else {
    $dataUsers = [];
}


if (isset($_POST['registerCheck']) && isset($_POST['registerUsername']) && isset($_POST['registerEmail'])) {

    $username = $_POST['registerUsername'];
    $email = $_POST['registerEmail'];

    $filteredUsers = array_filter($dataUsers, function ($user) use ($username) {
        return $user['username'] === $username;
    });

    $emailCheck = array_filter($dataUsers, function ($user) use ($email) {
        return $user['email'] === $email;
    });

    if (!empty($filteredUsers)) {
        echo json_encode(array('status' => 'failed' , 'message' => 'found'));
    }
    else if(!empty($emailCheck)){
        echo json_encode(array('status' => 'failed' , 'message' => 'emailfound'));
    }
    else {
        echo json_encode(array('status' => 'success' , 'message' => 'notfound'));
    }     

}

if(isset($_POST['createUser'])){

    $jsonData = json_decode($_POST['user']);

    $name = $jsonData->{'name'};
    $email = $jsonData->{'email'};
    $username = $jsonData->{'username'};
    $password = $jsonData->{'password'};
    
    $post = "INSERT INTO users (name,email,username,password) VALUES ('$name','$email','$username','$password')";
    $conn->query($post); 
    echo json_encode(array('status' => 'success' , 'message' => 'registerd'));
}

if (isset($_POST['loginCheck'])) {
    
    $loginData = json_decode($_POST['loginData'], true);

    $name = $loginData['name'];
    $password = $loginData['password'];

    $filteredUsername = array_filter($dataUsers, function ($user) use ($name) {
        return $user['username'] === $name ;
    });

    $filteredUserandPassword = array_filter($dataUsers, function ($user) use ($name,$password) {
        
        return $user['username'] === $name && $user['password'] === $password;
    });
    foreach($dataUsers as $user){
        if($user['username'] === $name && $user['password'] === $password){
            $_SESSION['email'] = $user['email'];
            $_SESSION['image'] = $user['imageData'];
            $_SESSION['userName'] = $user['username'];
            
        }
    }

    if(empty($filteredUsername)){
        echo json_encode(array('status' => 'failed' , 'message' => 'usernameNotfound'));
    }
    else if(empty($filteredUserandPassword)){
        echo json_encode(array('status' => 'failed' , 'message' => 'passwordWorng'));
    }
    else if(!empty($filteredUserandPassword)){
        echo json_encode(array('status' => 'success' , 'message' => 'loginSuccess','username' => isset($_SESSION['userName']) ? $_SESSION['userName'] : null , 'email' => isset($_SESSION['email']) ? $_SESSION['email'] : null));
    }
    
}

if(isset($_POST['compose'])){

    $jsonData = json_decode($_POST['composeData']);

    $from = $jsonData->{'from'};
    $to = $jsonData->{'to'};
    $subject = $jsonData->{'subject'};
    $status = $jsonData->{'status'};
    $currentDate = date("Y-m-d");
    
    $post = "INSERT INTO emaillist (fromEmailID,toEmailID,subject,status,date) VALUES ('$from','$to','$subject','$status','$currentDate')";
    $conn->query($post); 

    echo json_encode(array('status' => 'success' , 'message' => 'sent Successfully')); 
   
}


if(isset($_POST['update'])){

    $update_id = $_POST['UpdatedID'];
    $userName = $_POST['username'];

    $existingStatusQuery = $conn->query("SELECT status FROM emaillist WHERE id = $update_id");
    $existingStatus = $existingStatusQuery->fetch_assoc()['status'];

    if (!empty($existingStatus)) {
        $newStatus = $userName . $existingStatus;
    } else {
        $newStatus = $userName . 'trash';
    }

    $updateQuery = "UPDATE emaillist SET status = '$newStatus' WHERE id = $update_id";
    $conn->query($updateQuery);

    echo json_encode(array('status' => 'success' , 'message' => 'Mail Deleted Successfully'));
   
}

if(isset($_POST['EditProfile'])){

    $username = $_POST['username'];

    foreach($dataUsers as $user){

        if($username === $user['username']){
            $fullname = $user['name'];
            $country = $user['country'];
            $address = $user['address'];
            $phone = $user['phoneNumber'];
        }

    }

        echo json_encode(array('status' => 'success' , 'message' => 'loginSuccess','fullname' => $fullname , 'country' => $country, 'address' => $address , 'phone' => $phone));
    
}


$conn->close();
?>