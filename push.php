<?php

$status_image = false;
$status_insert = false;
$status_notification = false;

//Random image name and upload image
$nameImage = rand(1,1000000).time();
$status_image = move_uploaded_file($_FILES["image"]["tmp_name"],"image/".$nameImage);

require_once ("db_config.php");
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
$sql = "INSERT INTO ".$tbname." (id ,title, image, content, type) VALUES ('','".$_POST['title']."', '".$nameImage."', '".$_POST['content']."', '".$_POST['type']."')";
$status_insert = $conn->query($sql);


//Select last record for temp "id"
$conn->set_charset("utf8");
$sql = "SELECT * FROM ".$tbname." ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$lastID = $row['id'];

$conn->close();


if ($_POST['notification'] == "on") {
    // Push The notification with parameters
    require_once('PushBots.class.php');
    $pb = new PushBots();
    $appID = '59ff54349b823a17668b4574';
    $appSecret = 'da8d567ba67a8e842aaf6af0aba61b39';
    $pb->App($appID, $appSecret);

    $pb->Alert($_POST['title']);
    $pb->Platform(1); // 1=android , 2=ios
    $pb->Badge("+2");
    $pb->Tags($_POST['type']);

    $customfields = array("newsId" => $lastID, "nextActivity" => "com.adv.newsnotification.ReadActivity");
    $pb->Payload($customfields);
    $pb->Push();

    $status_notification = true;
}

?>

<!doctype html>
<html lang="en">
<head>
    <title>Push - News Notification</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css"
          integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <style>
        .vertical-center {
            min-height: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .checkmark {
            width: 150px;
            margin: 0 auto;
        }

        .path {
            stroke-dasharray: 1000;
            stroke-dashoffset: 0;
            animation: dash 2s ease-in-out;
            -webkit-animation: dash 2s ease-in-out;
        }

        .spin {
            animation: spin 2s;
            -webkit-animation: spin 2s;
            transform-origin: 50% 50%;
            -webkit-transform-origin: 50% 50%;
        }

        @-webkit-keyframes dash {
            0% {
                stroke-dashoffset: 1000;
            }
            100% {
                stroke-dashoffset: 0;
            }
        }

        @keyframes dash {
            0% {
                stroke-dashoffset: 1000;
            }
            100% {
                stroke-dashoffset: 0;
            }
        }

        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @-webkit-keyframes text {
            0% {
                opacity: 0; }
            100% {
                opacity: 1;
            }

    </style>
</head>
<body class="text-white bg-dark">
<div class="jumbotron vertical-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 bg-success rounded p-5">
                <h4 class="text-center">เพิ่มข่าวเรียบร้อยแล้ว</h4>
                <div class="checkmark pt-4">
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                         xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                         viewBox="0 0 161.2 161.2" enable-background="new 0 0 161.2 161.2" xml:space="preserve">
                        <path class="path" fill="none" stroke="#fff" stroke-miterlimit="10" d="M425.9,52.1L425.9,52.1c-2.2-2.6-6-2.6-8.3-0.1l-42.7,46.2l-14.3-16.4c-2.3-2.7-6.2-2.7-8.6-0.1c-1.9,2.1-2,5.6-0.1,7.7l17.6,20.3c0.2,0.3,0.4,0.6,0.6,0.9c1.8,2,4.4,2.5,6.6,1.4c0.7-0.3,1.4-0.8,2-1.5c0.3-0.3,0.5-0.6,0.7-0.9l46.3-50.1C427.7,57.5,427.7,54.2,425.9,52.1z"/>
                        <circle class="path" fill="none" stroke="#fff" stroke-width="4" stroke-miterlimit="10" cx="80.6" cy="80.6" r="62.1"/>
                        <polyline class="path" fill="none" stroke="#fff" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="113,52.8 74.1,108.4 48.2,86.4 "/>
                        <circle class="spin" fill="none" stroke="#fff" stroke-width="4" stroke-miterlimit="10" stroke-dasharray="12.2175,12.2175" cx="80.6" cy="80.6" r="73.9"/>
                    </svg>
                </div>
                <div class="row justify-content-center pt-4">
                    <div class="col-4 text-center">
                        <div class="row">
                            <?php
                                if (!$status_image)
                                    echo '<div class="col-12 bg-warning p-1 m-1">ไม่ได้อัพโหลดรูปภาพ</div>';
                                if (!$status_insert)
                                    echo '<div class="col-12 bg-warning p-1 m-1">ไม่ได้บันทึกข่าว</div>';
                                if (!$status_notification)
                                    echo '<div class="col-12 bg-warning p-1 m-1">ไม่ส่งแจ้งเตือน</div>';
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center pt-4">
                    <div class="col-6">
                        <a href="add.html" class="btn btn-primary btn-block" role="button" aria-pressed="true">ย้อนกลับ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
</body>
</html>





