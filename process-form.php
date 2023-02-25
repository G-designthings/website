<?php

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "manda.offline@gmail.com";
    $subject = "New form submission";

    $headers = "From: " . $_POST["email"] . "\r\n";
    $headers .= "Reply-To: " . $_POST["email"] . "\r\n";
    $headers .= "CC: " . $_POST["cc"] . "\r\n";
    $headers .= "BCC: " . $_POST["bcc"] . "\r\n";

    $message = "Name: " . $_POST["name"] . "\r\n";
    $message .= "Email: " . $_POST["email"] . "\r\n";
    $message .= "Message: " . $_POST["message"] . "\r\n";

    // Process file uploads
    if(isset($_FILES['file-upload'])) {
        $total_files = count($_FILES['file-upload']['name']);

        for($i=0; $i<$total_files; $i++) {
            $tmpFilePath = $_FILES['file-upload']['tmp_name'][$i];
            $file_name = $_FILES['file-upload']['name'][$i];
            $file_size = $_FILES['file-upload']['size'][$i];
            $file_type = $_FILES['file-upload']['type'][$i];

            if($tmpFilePath != "") {
                $path_parts = pathinfo($file_name);
                $new_file_name = $path_parts['filename'] . "_" . time() . "." . $path_parts['extension'];
                $new_file_path = "uploads/" . $new_file_name;

                // Move the file to the uploads directory
                move_uploaded_file($tmpFilePath, $new_file_path);

                // Add the file attachment to the email
                $file = file_get_contents($new_file_path);
                $file_content = chunk_split(base64_encode($file));
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: multipart/mixed; boundary=\"mixed-" . md5($new_file_name) . "\"\r\n\r\n";
                $headers .= "--mixed-" . md5($new_file_name) . "\r\n";
                $headers .= "Content-Disposition: attachment; filename=\"" . $new_file_name . "\"\r\n";
                $headers .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $headers .= $file_content . "\r\n\r\n";
            }
        }
    }

    // Send the email
    mail($to, $subject, $message, $headers);

    // Redirect to thank you page
    header("Location: thank-you.html");
    exit();
}

?>