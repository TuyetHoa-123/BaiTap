<?php
include("class/class.php");
$p = new user();
?>
<?php

if (isset($_REQUEST['layid'])) {
    $layid = $_REQUEST['layid'];

    $sql = "SELECT * FROM m_user WHERE UserId = $layid";
    $p->connect();
    $result = mysqli_query($p->connect(), $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $userData = mysqli_fetch_assoc($result);

        $loginId = $userData['LoginId'];
        $userName = $userData['UserName'];
        $mail = $userData['Mail'];
        $userRole = $userData['UserRole'];
        $gender = $userData['Male'];
        $birthdate = $userData['BirthDate'];
        $address = $userData['Address'];
        $birthDate = date('Y-m-d', strtotime($birthdate));

        $sqlDates = "SELECT StartDt, EndDt FROM m_user_period WHERE UserId = $layid";
        $resultDates = mysqli_query($p->connect(), $sqlDates);

        $dateFirst = '';
        $dateAfter = '';

        if ($resultDates && mysqli_num_rows($resultDates) > 0) {
            $datePairs = [];
        
            while ($datesData = mysqli_fetch_assoc($resultDates)) {
                $dateFirst = date('Y-m-d', strtotime($datesData['StartDt']));
                $dateAfter = date('Y-m-d', strtotime($datesData['EndDt']));
                $datePairs[] = "$dateFirst~$dateAfter";
            }
        }
    }
}
?>
<?php
// Fetch UserPerId along with StartDt and EndDt
$sqlDates = "SELECT UserPerId, StartDt, EndDt FROM m_user_period WHERE UserId = $layid";
$resultDates = mysqli_query($p->connect(), $sqlDates);

$dateFirst = '';
$dateAfter = '';

if ($resultDates && mysqli_num_rows($resultDates) > 0) {
    $datePairs = [];

    while ($datesData = mysqli_fetch_assoc($resultDates)) {
        $userPerId = $datesData['UserPerId'];
        $dateFirst = date('Y-m-d', strtotime($datesData['StartDt']));
        $dateAfter = date('Y-m-d', strtotime($datesData['EndDt']));
        $datePairs[] = "$userPerId~$dateFirst~$dateAfter";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/reponsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/css/pikaday.min.css" integrity="sha384-..." crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha384-..." crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha384-..." crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/pikaday.min.js" integrity="sha384-..." crossorigin="anonymous"></script>

    <title>Copy</title>
    <script>
    $(document).ready(function() {
        $("#form1").submit(function() {
            var loginId = $("#txtloginId").val();
            var userName = $("#txtuserName").val();
            var userRole = $("input[name='txtuserRole']:checked").val();
            var gender = $("input[name='txtgender']:checked").val();
            var email = $("#txtmail").val();
            var birthDate = $("#txtbirthDate").val();
            var address = $("#address").val();
            // Check if loginId is empty
            if (loginId == "") {
                $("#tbloginId").text("ログインIDを入力してください。");
                return false; 
            } else {
                $("#tbloginId").text(""); 
            }
             // Check if username is empty
             if (userName == "") {
                $("#tbuserName").text("ユーザー名を入力してください。");
                return false; 
            } else {
                $("#tbuserName").text(""); 
            }

            // Check if email is empty
            if (email == "") {
                $("#tbmail").text("メールを入力してください。");
                return false; 
            } else {
                $("#tbmail").text("");
            }
             // Check if email has a valid format
             var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                $("#tbmail").text("有効なメールアドレスの形式で入力してください。");
                return false; 
            } else {
                $("#tbmail").text("");
            }
            // Check if userRole is not selected
            if (!userRole) {
                $("#tbUserRole").text("ユーザーロールを選択してください。");
                return false; 
            } else {
                $("#tbUserRole").text(""); 
            }

             // Check if gender is not selected
             if (!gender) {
                $("#tbgender").text("性別を選択してください。");
                return false; 
            } else {
                $("#tbgender").text(""); 
            }

            // Check if birthDate is empty
            if (birthDate == "") {
                $("#tbbirthDate").text("誕生日を入力してください。");
                return false;
            } else {
                $("#tbbirthDate").text(""); 
            }

            // Check if birthDate is before the current date
            var currentDate = new Date().toISOString().split('T')[0];
            if (birthDate >= currentDate) {
                $("#tbbirthDate").text("誕生日は現在の日付より前である必要があります。");
                return false;
            } else {
                $("#tbbirthDate").text(""); 
            }

            if (address == "") {
                $("#tbaddress").text("住所を入力してください。");
                return false; 
            } else {
                $("#tbaddress").text(""); 
            }
            return true;
        });
    });
</script>
<style>
    .datepicker-container {
      position: relative;
      display: inline-block;
    }
/* 
    .datepicker-container input {
      padding-right: 30px; 
    } */

    .datepicker-container .icon {
      position: absolute;
      top: 50%;
      right: 10px;
      transform: translateY(-50%);
      cursor: pointer;
      font-size: 1.4em;
    }
    .period{
        width: 35%;
    }
</style>
</head>
<body>
    <div class="register">
        <div class="heading-title">
            <h2>ユーザー登録</h2>
        </div>
        <form method="post" enctype="multipart/form-data" name="form1" id="form1">
        <input type="hidden" name="idcancopy" id="idcancopy" value="<?php echo $layid; ?>"></td>
            <div class="main">
                <div class="form-row">
                    <label >ログインID</label>
                    <input type="text" placeholder="ログインID" name="txtloginId" id="txtloginId">
                    <span id="tbloginId" style="color: red;"></span>
                </div>
                <div class="form-row">
                    <label >ユーザー名</label>
                    <input type="text" placeholder="ユーザー名" name="txtuserName" id="txtuserName" value="<?php echo isset($userName) ? $userName : ''; ?>">
                    <span id="tbuserName" style="color: red;"></span>
                </div>
                <div class="form-row">
                    <label>メール</label>
                    <input type="text" placeholder="メール" name="txtmail" id="txtmail" value="<?php echo isset($mail) ? $mail : ''; ?>">
                    <span id="tbmail" style="color: red;"></span>
                </div>
                <div class="form-row">
                    <label >自社ユーザーロール</label> <br>
                    <input type="radio" name="txtuserRole" id="admin" value="9" <?php echo isset($userRole) && $userRole == 9 ? 'checked' : ''; ?>>
                    <label >管理者</label> &emsp;  &emsp;
                    <input type="radio" name="txtuserRole" id="normal" value="0" <?php echo isset($userRole) && $userRole == 0 ? 'checked' : ''; ?>>
                    <label >一般</label>
                    <span id="tbUserRole" style="color: red; display: block;"></span>
                </div>
                <div class="form-row">
                    <label >性別</label> <br>
                    <input type="radio" name="txtgender" id="male" value="0" <?php echo isset($gender) && $gender == 0 ? 'checked' : ''; ?>>
                    <label>男</label> &emsp;  &emsp;
                    <input type="radio" name="txtgender" id="female" value="1" <?php echo isset($gender) && $gender == 1 ? 'checked' : ''; ?>>
                    <label >女</label>
                    <span id="tbgender" style="color: red; display: block;"></span>
                </div>
                <!-- <div class="form-row"> 
                    <label >誕生日</label><br>
                    <input type="date" name="txtbirthDate" id="txtbirthDate" value="<?php echo isset($birthDate) ? $birthDate : ''; ?>">
                    <span id="tbbirthDate" style="color: red; display: block;"></span>
                </div> -->
                <div class="form-row">
                    <label>誕生日</label><br>
                    <div class="datepicker-container">
                        <input type="text" id="datepicker" name="txtbirthDate"  placeholder="yyyy/mm/dd">
                        <i class="icon fas fa-calendar-alt" id="datepicker-icon"></i>
                    </div>
                    <span id="tbbirthDate" style="color: red; display: block;"></span>
                </div>
                <div class="form-row">
                    <label >住所</label>
                    <input type="text" placeholder="住所" name="address" id="address" value="<?php echo isset($address) ? $address : ''; ?>">
                    <span id="tbaddress" style="color: red;"></span>
                </div>
                <div class="form-row">
                    <label >使用期間</label>
                    <div class="duration">
                        <div class="duration-inpdate">
                            <!-- <input type="date" name="" id="" class="inp-dateFirst">
                            <input type="date" name="" id="" class="inp-dateAfter"> -->
                            <div class="datepicker-container period">
                                <input type="text" id="datepicker2"   placeholder="yyyy/mm/dd" class="inp-dateFirst">
                                <i class="icon fas fa-calendar-alt" id="datepicker2-icon"></i>
                            </div>
                            <div class="datepicker-container period">
                                <input type="text" id="datepicker3"   placeholder="yyyy/mm/dd" class="inp-dateAfter">
                                <i class="icon fas fa-calendar-alt" id="datepicker3-icon"></i>
                            </div>
                            <button id="dateButton">住所</button>
                        </div>
                        <div class="all-durations">
                            <span id="tbduration" style="color: red;"></span>
                            <?php
                        if (isset($datePairs) && count($datePairs) > 0) {
                            foreach ($datePairs as $datePair) {
                                $dates = explode('~', $datePair);
                                $userPerId = $dates[0];
                            ?>
                                <div class="duration-date">
                                    <div class="dateFirst"><?php echo $dates[1]; ?></div>
                                    <p>~</p>
                                    <div class="dateAfter" ><?php echo $dates[2]; ?></div>
                                    <i class="fa-solid fa-trash-can iconDelete" data-userperid="<?php echo $userPerId; ?>"></i>
                                    
                                </div>
                                <input type="hidden" name="userPerIdToDelete" class="userPerIdInput" value="<?php echo $userPerId; ?>" readonly>
                                <input type="hidden" name="oldDateFirst[]" class="date-input" value="<?php echo $dates[1]; ?>" id="dateFirst_<?php echo $userPerId; ?>">
                                <input type="hidden" name="oldDateAfter[]" class="date-input" value="<?php echo $dates[2]; ?>" id="dateAfter_<?php echo $userPerId; ?>">
                                <?php
                                        }
                                    }
                                ?>
                        </div>
                        <input type="hidden" name="datesArray" id="datesArray" value="">
                    </div>
                </div>
            </div>
        <div class="Registerbtn">
            <input type="submit" name="nut" id="nut" value="閉じる" class="btnCancel">
            <input type="submit" name="nut" id="nut" value="保存" class="btnSave">
        </div>   
        <?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nut'])) {
    switch ($_POST['nut']) {
        case '保存':
            // Lấy thông tin người dùng từ form
            $loginId = $_REQUEST['txtloginId'];
            $userName = $_REQUEST['txtuserName'];
            $mail = $_REQUEST['txtmail'];
            $userRole = $_REQUEST['txtuserRole'];
            $gender = $_REQUEST['txtgender'];
            $birthDate = $_REQUEST['txtbirthDate'];
            $address = $_REQUEST['address'];

            // Thêm thông tin người dùng vào bảng m_user
            $sqlUser = "INSERT INTO m_user (LoginId, InsBy, UpdBy, UserName, Mail, UserRole, Male, BirthDate, Address)
                        VALUES ('$loginId', 'Admin', 'Admin', '$userName', '$mail', '$userRole', '$gender', '$birthDate', '$address')";

            // Thực hiện truy vấn thêm thông tin người dùng
            $resultUser = $p->themxoasua($sqlUser);

            if ($resultUser === 1) {
                // Lấy ID của người dùng vừa được thêm vào
                $userId = $p->mysqli_insert_id();

                // Lấy và giải mã mảng ngày từ dữ liệu được gửi lên
                $datesArray = json_decode($_POST['datesArray'], true);

                if (is_array($datesArray)) {
                    foreach ($datesArray as $dates) {
                        $dateFirst = $dates['dateFirst'];
                        $dateAfter = $dates['dateAfter'];

                        // Thêm ngày vào bảng m_user_period
                        $sqlUserPeriod = "INSERT INTO m_user_period (UserId, StartDt, EndDt)
                                        VALUES ('$userId', '$dateFirst', '$dateAfter')";

                        // Thực hiện truy vấn thêm ngày và kiểm tra kết quả
                        $resultUserPeriod = $p->themxoasua($sqlUserPeriod);

                        if ($resultUserPeriod !== 1) {
                            // Xử lý khi thêm ngày không thành công
                            // Ví dụ: Ghi log, hiển thị thông báo...
                        }
                    }
                }

                // Insert old date pairs into m_user_period
                $oldDateFirstArray = $_POST['oldDateFirst'];
                $oldDateAfterArray = $_POST['oldDateAfter'];

                if (is_array($oldDateFirstArray) && is_array($oldDateAfterArray)) {
                    foreach ($oldDateFirstArray as $key => $oldDateFirst) {
                        $oldDateAfter = $oldDateAfterArray[$key];

                        // Thêm ngày cũ vào bảng m_user_period
                        $sqlOldUserPeriod = "INSERT INTO m_user_period (UserId, StartDt, EndDt)
                                            VALUES ('$userId', '$oldDateFirst', '$oldDateAfter')";

                        // Thực hiện truy vấn thêm ngày cũ và kiểm tra kết quả
                        $resultOldUserPeriod = $p->themxoasua($sqlOldUserPeriod);

                        if ($resultOldUserPeriod !== 1) {
                            // Xử lý khi thêm ngày cũ không thành công
                            // Ví dụ: Ghi log, hiển thị thông báo...
                        }
                    }
                }
             
                echo '<script language="javascript">alert("Thêm thành công");</script>';
            } else {
                echo '<script language="javascript">alert("Thêm thất bại khi thêm vào bảng m_user");</script>';
            }
            break;
    }
}
?>

    </form>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
      var picker = new Pikaday({
        field: document.getElementById('datepicker'),
        yearRange: [moment().subtract(1, 'year').year(), moment().year()],
        format: 'YYYY/MM/DD',
        showYearDropdown: true,
        showMonthAfterYear: true,
        i18n: {
          previousMonth: 'Tháng trước',
          nextMonth: 'Tháng sau',
          months: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
          weekdays: ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'],
          weekdaysShort: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7']
        }
      });
    // Initialize datepicker for datepicker2
    var picker2 = new Pikaday({
                field: document.getElementById('datepicker2'),
                yearRange: [moment().subtract(1, 'year').year(), moment().year()],
                format: 'YYYY/MM/DD',
                showYearDropdown: true,
                showMonthAfterYear: true,
                i18n: {
                    previousMonth: 'Tháng trước',
                    nextMonth: 'Tháng sau',
                    months: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                    weekdays: ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'],
                    weekdaysShort: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7']
                }
            });

            // Initialize datepicker for datepicker3
            var picker3 = new Pikaday({
                field: document.getElementById('datepicker3'),
                yearRange: [moment().subtract(1, 'year').year(), moment().year()],
                format: 'YYYY/MM/DD',
                showYearDropdown: true,
                showMonthAfterYear: true,
                i18n: {
                    previousMonth: 'Tháng trước',
                    nextMonth: 'Tháng sau',
                    months: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                    weekdays: ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'],
                    weekdaysShort: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7']
                }
            });

            // Bắt sự kiện click vào icon để hiển thị lịch
            document.getElementById('datepicker-icon').addEventListener('click', function () {
                picker.show();
            });
            // Bắt sự kiện click vào icon để hiển thị lịch cho picker2
            document.getElementById('datepicker2-icon').addEventListener('click', function () {
                        picker2.show();
            });

            // Bắt sự kiện click vào icon để hiển thị lịch cho picker3
            document.getElementById('datepicker3-icon').addEventListener('click', function () {
                picker3.show();
            });
    });
  </script>
    <script>
$(document).ready(function() {
    $('.iconDelete').click(function() {
        var userPerId = $(this).data('userperid');
        
        // Vô hiệu hóa trường input có id tương ứng
        $('#dateFirst_' + userPerId).prop('disabled', true);
        $('#dateAfter_' + userPerId).prop('disabled', true);
    });
});
</script>
    <script>
    document.getElementById('dateButton').addEventListener('click', function(event) {
        event.preventDefault();
        var dateFirst = document.querySelector('.inp-dateFirst').value;
        var dateAfter = document.querySelector('.inp-dateAfter').value;

        // Check if dateAfter is later than dateFirst
        if (dateFirst >= dateAfter) {
            document.getElementById('tbduration').innerText = "日付の範囲が無効です。";
            return;
        } else {
            document.getElementById('tbduration').innerText = ""; // Clear the error message
        }

        // Save selected dates to the array
        var datesArray = JSON.parse(document.getElementById('datesArray').value || '[]');
        datesArray.push({ dateFirst: dateFirst, dateAfter: dateAfter });
        document.getElementById('datesArray').value = JSON.stringify(datesArray);

        // Display selected dates on the interface
        var allDurations = document.querySelector('.all-durations');
        var newDuration = document.createElement('div');
        newDuration.classList.add('duration-date');
        newDuration.innerHTML = '<div class="dateFirst">' + dateFirst + '</div><p>~</p><div class="dateAfter">' + dateAfter + '</div><i class="fa-solid fa-trash-can iconDelete"></i>';

        allDurations.appendChild(newDuration);
        allDurations.appendChild(document.createElement('br'));
    });
</script>

    <script>
    document.addEventListener('click', function (event) {
    if (event.target.classList.contains('iconDelete')) {
        // Xác định phần tử cha gần nhất chứa thông tin về kỳ hạn
        var durationDate = event.target.closest('.duration-date');

        // Xóa dữ liệu tương ứng khỏi mảng chứa các ngày đã chọn
        var dateFirst = durationDate.querySelector('.dateFirst').innerText;
        var dateAfter = durationDate.querySelector('.dateAfter').innerText;
        var datesArray = JSON.parse(document.getElementById('datesArray').value || '[]');

        // Tìm và xóa ngày khỏi mảng
        datesArray = datesArray.filter(function (dates) {
            return !(dates.dateFirst === dateFirst && dates.dateAfter === dateAfter);
        });

        // Cập nhật lại giá trị của input ẩn chứa mảng các ngày đã chọn
        document.getElementById('datesArray').value = JSON.stringify(datesArray);

        // Xóa phần tử chứa thông tin kỳ hạn khỏi giao diện
        durationDate.remove();
        }
    });
    </script>
</body>
</html>

