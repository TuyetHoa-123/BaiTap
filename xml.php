<?php
include("class/class.php");

function getSearchQuery($searchKeyword) {
    $baseQuery = "SELECT * FROM m_user WHERE Deleted = 0";

    if (!empty($searchKeyword)) {
        $baseQuery .= " AND (LoginId LIKE '%$searchKeyword%' OR UserName LIKE '%$searchKeyword%' OR Mail LIKE '%$searchKeyword%')";
    }

    $query = $baseQuery;

    return $query;
}

$p = new user();

$searchKeyword = isset($_POST['search']) ? trim($_POST['search']) : '';
$query = getSearchQuery($searchKeyword);

// Lấy dữ liệu từ CSDL
$data = $p->layDuLieu($query);

// Tạo một đối tượng DOMDocument
$dom = new DOMDocument('1.0', 'utf-8');

// Tạo phần tử gốc
$root = $dom->createElement('users');
$dom->appendChild($root);

// Thêm các phần tử con vào phần tử gốc
foreach ($data as $row) {
    $userElement = $dom->createElement('user');

    // Duyệt qua tất cả các trường và thêm chúng vào phần tử user
    foreach ($row as $fieldName => $fieldValue) {
        $fieldElement = $dom->createElement($fieldName, $fieldValue);
        $userElement->appendChild($fieldElement);
    }

    $root->appendChild($userElement);
}

// Set formatOutput to true for formatting with line breaks and indentation
$dom->formatOutput = true;

// Output XML content to the client
header('Content-type: application/xml');
header('Content-Disposition: attachment; filename="exported.xml"');
echo $dom->saveXML();

// Stop further execution
exit;
?>