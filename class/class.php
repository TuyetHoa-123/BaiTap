<?php
class user
{ 
    private $con;

    public function connect()
    {
        $con = mysqli_connect("localhost", "root", "");

        if (!$con) {
            die('Không kết nối được csdl: ' . mysqli_connect_error());
            exit();
        }else{
            mysqli_select_db($con, "db_user");
            mysqli_query($con, "SET NAMES UTF8");
            return $con;
        }

       
    }

    public function themxoasua($sql)
    {
        $link = $this->connect();
        $result = mysqli_query($link, $sql); 

        if ($result) {
            return 1;
        } else {
            return 0;
        }
		mysqli_close($link);
    }

    

	public function mysqli_insert_id() {
		$link = $this->connect(); // Lấy kết nối CSDL
		// Thực hiện lấy ID của bản ghi vừa thêm vào
		return mysqli_insert_id($link);
	}

    public function deleteUser($delete) {
        if (isset($_REQUEST['delete'])) {
        $delete = $_REQUEST['delete'];
        $link = $this->connect();
        $sql = "UPDATE m_user
        SET Deleted = 1
        WHERE UserId = $delete";
        
        $ketqua = mysqli_query($link, $sql);
        
        if ($ketqua) {
        echo "<script>alert('Xóa thành công');</script>";
        header('Content-Type: text/html; charset=utf-8');
        echo header("refresh:0;url='index.php'");
        exit();
        } else {
        echo 'Xóa thất bại.';
        }
        
        mysqli_close($link);
        }
        }

   

        public function countUsers()
        {
            $link = $this->connect();
            $sql = "SELECT COUNT(*) FROM m_user WHERE Deleted = 0";
            $result = mysqli_query($link, $sql);
    
            if ($result) {
                $row = mysqli_fetch_row($result);
                return $row[0];
            } else {
                // Handle query execution failure
                echo "Query failed: " . mysqli_error($link);
            }
    
            mysqli_close($link);
        }

        public function layDuLieu($sql)
        {
            $link = $this->connect();
            $ketqua = mysqli_query($link, $sql);
            $data = [];
    
            if ($ketqua) {
                while ($row = mysqli_fetch_assoc($ketqua)) {
                    $data[] = $row;
                }
            } else {
                // Log error instead of echoing
                error_log('Lỗi truy vấn: ' . mysqli_error($link));
            }mysqli_close($link);
    
            return $data;
        }

        public function layTongSoDong($sql)
        {
            $link = $this->connect();
            $result = mysqli_query($link, $sql);
    
            if ($result) {
                $totalRows = mysqli_num_rows($result);
                mysqli_free_result($result); // Giải phóng bộ nhớ của kết quả truy vấn
                mysqli_close($link); // Đóng kết nối
    
                return $totalRows;
            } else {
                // Xử lý lỗi truy vấn
                error_log('Lỗi truy vấn: ' . mysqli_error($link));
                mysqli_close($link);
                return 0;
            }
        }
    }
?>