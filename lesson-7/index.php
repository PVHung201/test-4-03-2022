<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Bài 7: Gửi email trong php bằng SMTP Gmail</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body{
                font-family: arial;
            }
            .container{
                width: 800px;
                margin: 0 auto;
            }
            #send-email-form label {
                width: 150px;
                display: inline-block;
            }
            #send-email-form input {
                margin-bottom: 10px;
                line-height: 32px;
            }
            #send-email-form textarea {
                width: 500px;
                height: 200px;
            }
            #send-email-form input[type=submit] {
                margin-top: 35px;
                height: 32px;
                margin-left: 150px;
            }
            .g-recaptcha{
                margin-left: 153px;
            }
            #cke_email-content{
                float: right;
                width: 700px;
            }
        </style>
            <script src="https://www.google.com/recaptcha/api.js" a sync defer></script>
            <script src="resources/ckeditor/ckeditor.js"></script>
    </head>
    <body>
        <?php

         use PHPMailer\PHPMailer\PHPMailer;
         use PHPMailer\PHPMailer\Exception;

        require 'D:/My Folder/Learn Information Techcology/PHP core/Source File/lesson-7/PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
		require 'D:/My Folder/Learn Information Techcology/PHP core/Source File/lesson-7/PHPMailer-master/PHPMailer-master/src/Exception.php';
		require 'D:/My Folder/Learn Information Techcology/PHP core/Source File/lesson-7/PHPMailer-master/PHPMailer-master/src/OAuth.php';
		require 'D:/My Folder/Learn Information Techcology/PHP core/Source File/lesson-7/PHPMailer-master/PHPMailer-master/src/POP3.php';
		require 'D:/My Folder/Learn Information Techcology/PHP core/Source File/lesson-7/PHPMailer-master/PHPMailer-master/src/SMTP.php';
        include './function.php';
        if (isset($_GET['action']) && $_GET['action'] == "send") {
          //  var_dump($_POST);
         //   var_dump($_FILES);exit;
         $secret = '6Lfb2KMeAAAAAEiBjLXbM27bpQdCPN2qfpHZZzNH';
         $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['g-recaptcha-response']);
        $responseData = json_decode($verifyResponse);
                    if (!$responseData->success) {
                        $error = "Bạn chưa xác minh Captcha";
                    }
                      elseif (empty($_POST['email'])) { //Kiểm tra xem trường email có rỗng không?
                         $error = "Bạn phải nhập địa chỉ email";
                     } elseif (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                         $error = "Bạn phải nhập email đúng định dạng";
                     } elseif (empty($_POST['content'])) { //Kiểm tra xem trường content có rỗng không?
                         $error = "Bạn phải nhập nội dung";
                     }
                     if (isset($_FILES['file_upload'])) {

                        $uploadedFiles = $_FILES['file_upload'];
                        $result = uploadFiles($uploadedFiles);
                        if (!empty($result['errors'])) {
                            $error = $result['errors'];
                        } else {
                            $uploadedFiles = $result['uploaded_files'];

                        }
                    }

                     if (!isset($error)) {
                         include 'library.php'; // include the library file
                      //   require 'vendor/autoload.php';
                    
                         $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
                         try {
                             //Server settings
                             $mail->CharSet = "UTF-8";
                             $mail->SMTPDebug = 0;                                 // Enable verbose debug output
                             $mail->isSMTP();                                      // Set mailer to use SMTP
                             $mail->Host = SMTP_HOST;  // Specify main and backup SMTP servers
                             $mail->SMTPAuth = true;                               // Enable SMTP authentication
                             $mail->Username = SMTP_UNAME;                 // SMTP username
                             $mail->Password = SMTP_PWORD;                           // SMTP password
                             $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
                             $mail->Port = SMTP_PORT;                                    // TCP port to connect to
                             //Recipients
                             $mail->setFrom(SMTP_UNAME, "Tên người gửi");
                             $mail->addAddress($_POST['email'], 'Tên người nhận');     // Add a recipient | name is option
                             $mail->addReplyTo(SMTP_UNAME, 'Tên người trả lời');
         //                    $mail->addCC('CCemail@gmail.com');
         //                    $mail->addBCC('BCCemail@gmail.com');
         //                    Attachments
       //  
                            if (!empty($uploadedFiles)) {
                                foreach ($uploadedFiles as $file) {
                  //                  var_dump(realpath('.') . $file);exit;
                                    $mail->addAttachment(realpath('.') . $file);
                                }
                            }
                             $mail->isHTML(true);                                  // Set email format to HTML
                             $mail->Subject = $_POST['title'];
                             $mail->Body = $_POST['content'];
                             $mail->AltBody = $_POST['content']; //None HTML
                             $result = $mail->send();
                             if (!$result) {
                                 $error = "Có lỗi xảy ra trong quá trình gửi mail";
                             }
                         } catch (Exception $e) {
                             echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
                         }
                     }
                     ?>
                     <div class = "container">
                         <div class = "error"><?= isset($error) ? $error : "Gửi email thành công" ?></div>
                         <a href = "index.php">Quay lại form gửi mail</a>
                     </div>
                 <?php } else {
                     ?>
                     <div class="container">
                         <h1>Send Email Form</h1>
                         <form id="send-email-form" method="POST" action="?action=send"  enctype="multipart/form-data">
                             <label>Gửi đến email: </label>
                             <input type="text" name="email" value="" /><br/>
                             <label>Tiêu đề: </label>
                             <input type="text" name="title" value="" /><br/>
                             <lable>File: </lable>
                             <input multiple type="file" name="file_upload[]" /><br> 
                             <div class="g-recaptcha" data-sitekey="6Lfb2KMeAAAAAP8vlWDCEd8TGEPb9zDREzKzRDN9"></div> 
                             <label>Nội dung: </label>
                             <textarea name="content" id="email-content"></textarea><br/>
                             <input type="submit" value="Send Email" />
                         </form>
                     </div>
         <?php }  ?>
        <script>
            // Replace the <textarea id="editor1"> with a CKEditor
            // instance, using default configuration.
            CKEDITOR.replace('email-content');
        </script>
    </body>
</html>
