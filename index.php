<?php
    require_once("db_config.php");
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    //Delete news
    if (isset($_GET['id'])) {
        $sql = "DELETE FROM ".$tbname." WHERE id = '".$_GET['id']."'";
        if ($conn->query($sql) === TRUE)
            echo '<div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                    <strong>สำเร็จ !</strong> - ข่าวหมายเลข '.$_GET['id'].' ลบออกจากฐานข้อมูลเรียบร้อยแล้ว
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                   </div>';
    }

    //Send notification
    if (isset($_GET['notification'])) {
        $sql = "SELECT id, title, type FROM ".$tbname." WHERE id = '".$_GET['notification']."'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();

        require_once('PushBots.class.php');
        $pb = new PushBots();
        $appID = '59ff54349b823a17668b4574';
        $appSecret = 'da8d567ba67a8e842aaf6af0aba61b39';
        $pb->App($appID, $appSecret);
        $pb->Alert($row['title']);
        $pb->Platform(1);
        $pb->Badge("+2");
        $pb->Tags($row['type']);
        $customfields = array("newsId" => $row['id'], "nextActivity" => "com.adv.newsnotification.ReadActivity");
        $pb->Payload($customfields);

        $pb->Push();
        echo '<div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                    <strong>สำเร็จ !</strong> - ข่าวหมายเลข '.$_GET['notification'].' ถูกส่ง Notification เรียบร้อยแล้ว
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                   </div>';
    }

    //Load all news
    $sql = "SELECT * FROM ".$tbname." ORDER BY id DESC";
    $result = $conn->query($sql);
    $conn->close();

    function typeName($type) {
        if ($type == 1) return "ข่าวเศรษกิจ";
        elseif ($type == 2) return "ข่าวการเมือง";
        elseif ($type == 3) return "ข่าวเทคโนโลยี";
        elseif ($type == 4) return "ข่าวบันเทิง";
        elseif ($type == 5) return "ข่าวการศึกษา";
        elseif ($type == 6) return "ข่าวอาชญากรรม";
        elseif ($type == 7) return "ข่าวกีฬา";
        elseif ($type == 8) return "ข่าวสังคม";
        else return "อื่นๆ";
    }

?>

<!doctype html>
<html lang="en">
<head>
    <title>News Notification</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
</head>
<body>

<div class="container">
    <div class="row p-4">
        <div class="col-sm-12 col-md-10">
            <h2 class="text-center text-info">
                News Notification
            </h2>
        </div>
        <div class="col-sm-12 col-md-2">
            <a href="add.html" class="btn btn-primary btn-block" role="button" aria-pressed="true">เพิ่มข่าว</a>
        </div>
    </div>

    <table class="table table-hover">
        <thead class="bg-light">
            <tr>
                <th scope="col" class="text-center" >หมายเลข</th>
                <th scope="col" class="text-center ">รูปภาพ</th>
                <th scope="col">หัวข้อข่าว</th>
                <th scope="col">ประเภทข่าว</th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
            </thead>
        <tbody>
        <?php
            while($row = $result->fetch_assoc()) {
                echo '<tr>
                        <th scope="row" class="text-center">'.$row['id'].'</th>
                        <td><img style="height: 50px;" src="http://localhost:8888/newsnotification/image/'.$row['image'].'" class="rounded mx-auto d-block"></td>
                        <td>'.$row['title'].'</td>
                        <td>'.typeName($row['type']).'</td>
                        <td><a href="index.php?notification='.$row['id'].'" class="btn btn-warning btn-block text-white" role="button" aria-pressed="true">Notification</a></td>
                        <td>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target=".bd-confirm-'.$row['id'].'">ลบ</button>
                            <div class="modal fade bd-confirm-'.$row['id'].'" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content p-4 text-center">
                                    <h5 class="pb-3"><u>ยืนยัน</u>การลบข่าว ['.$row['id'].']</h5>
                                    <div class="row">
                                        <div class="col-6">
                                            <a href="index.php?id='.$row['id'].'" class="btn btn-danger btn-block" role="button" aria-pressed="true">ลบ</a>
                                        </div>
                                        <div class="col-6">
                                            <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">ยกเลิก</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          </td>
                       </tr>
                ';
            }
        ?>
        </tbody>
    </table>
    </div>


<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
</body>
</html>