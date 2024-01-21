<?php
include("class/class.php");
$p = new User();

if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];
    $searchResults = $p->searchUsers($searchTerm);
} else {
    // If no search term is provided, redirect to the main page or display an error message.
    // You can customize this part based on your requirements.
    header("Location: index.php");
    exit();
}

// Include the HTML code for displaying the search results
include("search.php");
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
    <title>Trang chủ</title>
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
            <form method="post" enctype="multipart/form-data" name="searchForm" id="searchForm" action="search.php">
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
                    <tbody id="myTableBody">
                    <?php
                        foreach ($data as $row) {
                            echo '<tr >';
                            echo '<td "style="float: left; border: none; ">
                                    <a href="./Edit.php?layid='.$row['UserId'].'" style="padding-left: 2px; text-decoration: none;">
                                        <i class="fa-solid fa-pen iconEdit"></i>
                                    </a>
                                    <a href="./Copy.php?layid='.$row['UserId'].'" style="text-decoration: none;">
                                    <i class="fa-regular fa-copy iconCopy"></i>
                                </a>
                                    <a href="index.php?delete='.$row['UserId'].'" onclick="return confirm(\'Bạn có chắc chắn xóa!\')" style="text-decoration: none;">
                                        <i class="fa-solid fa-trash-can iconDelete"></i>
                                    </a>
                                  </td>';
                            echo '<td>'.$row['LoginId'].'</td>';
                            echo '<td>'.$row['UserName'].'</td>';
                            echo '<td>'.$row['Mail'].'</td>';
                            echo '<td>'.($row['UserRole'] == 0 ? '一般' : '管理者').'</td>';
                            echo '<td>'.($row['Male'] == 1 ? '女' : '男').'</td>';
                            // Định dạng ngày sinh 
                            $formattedBirthDate = date('Y/m/d', strtotime($row['BirthDate']));
                            echo '<td>'.$formattedBirthDate.'</td>';
                            echo '<td>'.$row['Address'].'</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    <ul class="pagination" id="pagination" style="float: right;margin-right:80px;"></ul>
</div>
<script>
     const itemsPerPage = 3; // Hiển thị 3 mục trên mỗi trang
    let currentPage = 1;
    const tableBody = document.getElementById('myTableBody');
    const paginationContainer = document.getElementById('pagination');
    // const data = <?php echo json_encode($p->layDuLieu('SELECT * FROM M_User WHERE Deleted = 0')); ?>;
    const searchData = <?php echo isset($searchResults) ? json_encode($searchResults) : '[]'; ?>;
    const data = searchData.length > 0 ? searchData : <?php echo json_encode($p->layDuLieu('SELECT * FROM M_User WHERE Deleted = 0')); ?>;
    function renderTable() {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const slicedData = data.slice(startIndex, endIndex);
        tableBody.innerHTML = '';
        slicedData.forEach(user => {
            const row = document.createElement('tr');
            const birthDate = new Date(user.BirthDate);
            const formattedBirthDate = `${birthDate.getFullYear()}/${(birthDate.getMonth() + 1).toString().padStart(2, '0')}/${birthDate.getDate().toString().padStart(2, '0')}`;

            row.innerHTML = `<td style="text-align: left;">
                <a href="Edit.php?layid=${user.UserId}"><i class="fa-solid fa-pen" style="color: #ff9f4a;"></i></a>
                <a href="Copy.php?layid=${user.UserId}"><i class="fa-regular fa-copy iconCopy" aria-hidden="true" style="color: #8c8f96;"></i></a>
                <a href="index.php?delete=${user.UserId}" onclick="return confirm('Bạn có chắc xóa!')"><i class="fa fa-trash" aria-hidden="true" style="color: #f42f2f;"></i></a>
                &emsp;
            </td>
            <td>${user.LoginId}</td>
            <td>${user.UserName}</td>
            <td>${user.Mail}</td>
            <td>${user.UserRole == 0 ? 'User' : (user.UserRole == 9 ? 'Admin' : user.UserRole)}</td>
            <td>${user.Male == 0 ? '男' : (user.Male == 1 ? '女' : user.Male)}</td>
            <td>${formattedBirthDate}</td>
            <td>${user.Address}</td>`;
            tableBody.appendChild(row);
        });
    }
    function renderPagination(totalPages) {
    paginationContainer.innerHTML = '';
    const createPaginationButton = (html, clickHandler, isDisabled = false) => {
        const button = document.createElement('li');
        button.innerHTML = html;
        button.addEventListener('click', clickHandler);
        if (isDisabled) {
            button.classList.add('disabled');
        }
        if (html === currentPage || (html === '...' && currentPage > totalPages - 3)) {
            button.classList.add('active');
        }
        paginationContainer.appendChild(button);
    };

    const showEllipsis = totalPages > 5;
    // Show "<<"
    createPaginationButton('<i class="fa fa-angle-double-left" aria-hidden="true"></i>', () => handlePaginationClick(1), currentPage === 1);
    createPaginationButton('<i class="fa fa-angle-left" aria-hidden="true"></i>', () => handlePaginationClick(currentPage - 1), currentPage === 1);

    if (currentPage === totalPages - 1 || currentPage === totalPages) {
        // Show the first two pages
        for (let i = 1; i <= 2; i++) {
            createPaginationButton(i, () => handlePaginationClick(i), i == currentPage);
        }
        // Show "..."
        if (showEllipsis) {
            const ellipsisItem = document.createElement('li');
            ellipsisItem.textContent = '...';
            paginationContainer.appendChild(ellipsisItem);
        }
        // Show the last two pages
        for (let i = totalPages - 1; i <= totalPages; i++) {
            createPaginationButton(i, () => handlePaginationClick(i), i === currentPage);
        }
    } else {
        // Show pages close to the current page
        if (currentPage <= totalPages - 3) {
            for (let i = currentPage; i <= currentPage + 1; i++) {
                createPaginationButton(i, () => handlePaginationClick(i), i == currentPage);
            }
            // Show "..."
            if (showEllipsis) {
                const ellipsisItem = document.createElement('li');
                ellipsisItem.textContent = '...';
                paginationContainer.appendChild(ellipsisItem);
            }
        } else {
            // Show pages up to the current page
            for (let i = totalPages - 3; i <= totalPages - 2; i++) {
                createPaginationButton(i, () => handlePaginationClick(i), i === currentPage);
            }
        }
        // Show last two pages
        if (currentPage === totalPages - 1) {
            createPaginationButton(currentPage, () => handlePaginationClick(totalPages - 1));
        } else {
            createPaginationButton(totalPages - 1, () => handlePaginationClick(totalPages - 1));
        }
        if (currentPage === totalPages) {
            createPaginationButton(currentPage, () => handlePaginationClick(totalPages));
        } else {
            createPaginationButton(totalPages, () => handlePaginationClick(totalPages));
        }
    }

    // Show ">>"
    createPaginationButton('<i class="fa fa-angle-right" aria-hidden="true"></i>', () => handlePaginationClick(currentPage + 1), currentPage === totalPages);
    // Show ">>>"
    createPaginationButton('<i class="fa fa-angle-double-right" aria-hidden="true"></i>', () => handlePaginationClick(totalPages), currentPage === totalPages);

    renderTable();
}


    function handlePaginationClick(pageNumber) {
        if (pageNumber >= 1 && pageNumber <= totalPages) {
            currentPage = pageNumber;
            renderPagination(totalPages);
        }
    }

    const totalPages = Math.ceil(data.length / itemsPerPage);
    renderPagination(totalPages);
   
</script>
</body>
</html>