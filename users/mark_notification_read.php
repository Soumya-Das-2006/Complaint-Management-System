<?php
session_start();
include('config.php');

if(isset($_POST['notification_id']) && isset($_SESSION['id'])) {
    $notification_id = $_POST['notification_id'];
    $user_id = $_SESSION['id'];
    
    $update = mysqli_query($bd, "UPDATE notifications SET is_read = 1 WHERE id = '$notification_id' AND to_user = '$user_id'");
    
    if($update) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($bd)]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>