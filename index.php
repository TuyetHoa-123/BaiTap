<?php
function getSearchQuery($searchKeyword) {
    $baseQuery = "SELECT * FROM m_user WHERE Deleted = 0";

    if (!empty($searchKeyword)) {
        $baseQuery .= " AND (
            LoginId LIKE '%$searchKeyword%' OR 
            UserName LIKE '%$searchKeyword%' OR 
            Mail LIKE '%$searchKeyword%' OR 
            (CASE WHEN Male = 0 THEN '男' ELSE '女' END) LIKE '%$searchKeyword%' OR 
            (CASE WHEN UserRole = 9 THEN 'Admin' ELSE 'User' END) LIKE '%$searchKeyword%' OR 
            Address LIKE '%$searchKeyword%' OR 
            DATE_FORMAT(BirthDate, '%Y-%m-%d') LIKE '%$searchKeyword%'
        )";
    }

    return $baseQuery;
}

include("class/class.php");
$p = new user();
// Xử lý xóa người dùng
if (isset($_REQUEST['delete'])) {
    $delete = $_REQUEST['delete'];
    $p->deleteUser($delete);
}

$searchKeyword = isset($_POST['search']) ? trim($_POST['search']) : '';

// Sử dụng hàm tìm kiếm để có được truy vấn SQL
$query = getSearchQuery($searchKeyword);

// Lấy dữ liệu từ CSDL
$data = $p->layDuLieu($query);
?>
<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="./css/reponsive.css">
    <title>Search</title>
</head>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("exportXMLButton").addEventListener("click", function() {
        // Sử dụng XMLHttpRequest để gọi file PHP xuất XML
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "xml.php", true);
        xhr.responseType = "blob"; // Đặt kiểu dữ liệu trả về là blob
        xhr.send();

        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Tạo một đối tượng blob từ dữ liệu XML
                    var blob = new Blob([xhr.response], { type: "application/xml" });

                    // Tạo URL cho blob
                    var url = window.URL.createObjectURL(blob);

                    // Tạo một thẻ a để tạo và kích vào nó để tải xuống
                    var a = document.createElement("a");
                    a.href = url;
                    a.download = "exported.xml";
                    document.body.appendChild(a);
                    a.click();

                    // Giải phóng URL và thẻ a
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                } else {
                    console.error("Lỗi khi tải xuống file XML");
                }
            }
        };
    });
});

</script>

<body>
    <div class="title">
        <div class="heading">
            <h1>ユーザー</h1>
        </div>
        <div class="btn-insert">
            <a href="./register.php">
                <button>
                    <div class="btn-insert-icon">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    新規作成
                </button>
            </a> 
        </div>
        <div class="setting">
            <p>管理  > ユーザー</p>
            <i class="fa-solid fa-gear"></i>
        </div>
    </div>
    <div class="container">
        <div class="titlelist">
            <p>ユーザ一覧</p>
        </div>
        <div class="Content">
            <div class="search">
                <form method="post" enctype="multipart/form-data" name="searchForm" id="searchForm" action="index.php">
                    <input type="text" name="search" placeholder="キーワード" value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <div class="XML">
                <button id="exportXMLButton">XML出力</button>
            </div>
            <form method="post" enctype="multipart/form-data" name="form" id="form" class="deleteForm">
                <table id="myTable">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ログインID</th>
                            <th>ユーザー名</th>
                            <th>メール</th>
                            <th>自社ユーザーロール</th>
                            <th>性別</th>
                            <th>誕生日</th>
                            <th>住所</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                    
                        ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
   
<div class="pagination" id="paginationContainer">
    <div class="pagination-right final" onclick="handlePaginationClick('last')">
        <i class="fa-solid fa-angles-right"></i>
    </div>
    <div class="pagination-right" onclick="handlePaginationClick(currentPage + 1)">
        <i class="fa-solid fa-angle-right"></i>
    </div>
    <div id="paginationPages"></div>
    <div class="pagination-left" onclick="handlePaginationClick(currentPage - 1)">
        <i class="fa-solid fa-angle-left"></i>
    </div>
    <div class="pagination-left" onclick="handlePaginationClick('first')">
        <i class="fa-solid fa-angles-left"></i>
    </div>
</div>

<script>
    const itemsPerPage = 3;
    let currentPage = 1;
    let pageJump = 2; // Khoảng cách giữa các trang khi bấm vào trang sau dấu ba chấm

    const tableBody = document.getElementById('myTable').getElementsByTagName('tbody')[0];
    const data = <?php echo json_encode($data); ?>;

    function renderTable() {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const slicedData = data.slice(startIndex, endIndex);

        tableBody.innerHTML = '';
        slicedData.forEach(user => {
            const row = document.createElement('tr');
            // Chuyển đổi ngày từ chuỗi sang đối tượng Date
            const birthDate = new Date(user['BirthDate']);

            // Định dạng ngày tháng năm
            const formattedBirthDate = birthDate.getFullYear() + '/' + (birthDate.getMonth() + 1) + '/' + birthDate.getDate();

            row.innerHTML =
                '<td style= "">' +
                '<a href="./Edit.php?layid=' + user['UserId'] + '" style="padding-left: 10px; text-decoration: none;float:left;">' +
                '<i class="fa-solid fa-pen iconEdit"></i>' +
                '</a>' +
                '<a href="./Copy.php?layid=' + user['UserId'] + '" style="text-decoration: none; float:left;">' +
                '<i class="fa-regular fa-copy iconCopy"></i>' +
                '</a>' +
                '<a href="index.php?delete=' + user['UserId'] + '" style="float:left;"onclick="return confirm(\'Bạn có chắc chắn xóa!\')" style="text-decoration: none;">' +
                '<i class="fa-solid fa-trash-can iconDelete"></i>' +
                '</a>' +
                '</td>' +
                '<td>' + user['LoginId'] + '</td>' +
                '<td>' + user['UserName'] + '</td>' +
                '<td>' + user['Mail'] + '</td>' +
                '<td>' + (user['UserRole'] == 0 ? 'User' : 'Admin') + '</td>' +
                '<td>' + (user['Male'] == 1 ? '女' : '男') + '</td>' +
                '<td>' + formattedBirthDate + '</td>' +
                '<td>' + user['Address'] + '</td>';

            tableBody.appendChild(row);
        });

        // Hiển thị trang pagination
        const paginationPages = document.getElementById('paginationPages');
        paginationPages.innerHTML = generatePagination();

        // Add condition to change color of pagination-left buttons on the first page
        const paginationLeftButtons = document.querySelectorAll('.pagination-left');
        if (currentPage === 1) {
            paginationLeftButtons.forEach(button => {
                button.classList.add('first-page');
            });
        } else {
            paginationLeftButtons.forEach(button => {
                button.classList.remove('first-page');
            });
        }
        const paginationRightButtons = document.querySelectorAll('.pagination-right');
        if (currentPage === Math.ceil(data.length / itemsPerPage)) {
            paginationRightButtons.forEach(button => {
                button.classList.add('last-page');
            });
        } else {
            paginationRightButtons.forEach(button => {
                button.classList.remove('last-page');
            });
        }
        
    }
    function generatePagination() {
    const totalPages = Math.ceil(data.length / itemsPerPage);
    let paginationHTML = '';

    if (totalPages <= 5) {
        for (let i = totalPages; i >= 1; i--) {
            const isActive = i === currentPage ? 'active' : '';
            paginationHTML += `<div class="pagination-child ${isActive}" onclick="handlePaginationClick(${i})">${i}</div>`;
        }
    } else {
        // Hiển thị 2 trang cuối
        for (let i = totalPages; i >= totalPages - 1; i--) {
            const isActive = i === currentPage ? 'active' : '';
            paginationHTML += `<div class="pagination-child ${isActive}" onclick="handlePaginationClick(${i})">${i}</div>`;
        }

        if (currentPage < totalPages - 3) {
            paginationHTML += '<div class="pagination-child">...</div>';
        }

        // Hiển thị số trang cố định trước và sau trang hiện tại
        for (let i = Math.min(totalPages - 2, currentPage + 1); i >= Math.max(3, currentPage - 1); i--) {
            const isActive = i === currentPage ? 'active' : '';
            paginationHTML += `<div class="pagination-child ${isActive}" onclick="handlePaginationClick(${i})">${i}</div>`;
        }

        if (currentPage > 4) {
            paginationHTML += '<div class="pagination-child">...</div>';
        }

        // Hiển thị 2 trang đầu theo thứ tự ngược
        for (let i = 2; i >= 1; i--) {
            const page = i; // Adjust the page number
            const isActive = page === currentPage ? 'active' : '';
            paginationHTML += `<div class="pagination-child ${isActive}" onclick="handlePaginationClick(${page})">${page}</div>`;
        }

    }

    return paginationHTML;
}



    function handlePaginationClick(pageNumber) {
        const totalPages = Math.ceil(data.length / itemsPerPage);

        // Nếu là nút "Cuối cùng", chuyển đến trang cuối cùng
        if (pageNumber === 'last') {
            pageNumber = totalPages;
        }

        // Nếu là nút "Đầu tiên", chuyển đến trang đầu tiên
        if (pageNumber === 'first') {
            pageNumber = 1;
        }

        // Giới hạn số trang từ 1 đến totalPages
        pageNumber = Math.min(Math.max(1, pageNumber), totalPages);

        if (pageNumber !== currentPage) {
            currentPage = pageNumber;

            // Nếu currentPage là một trong 2 trang đầu hoặc 2 trang cuối, không cần thay đổi cách hiển thị
            if (currentPage <= 2 || currentPage >= totalPages - 1) {
                renderTable();
            } else {
                // Cập nhật cách hiển thị khi currentPage là một trong 2 trang giữa
                const paginationPages = document.getElementById('paginationPages');
                paginationPages.innerHTML = generatePagination();
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        renderTable();
    });
</script>
    
</body>
</html>