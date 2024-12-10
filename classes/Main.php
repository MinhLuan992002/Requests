<?php
$filepath = realpath(dirname(__FILE__));
include_once realpath(dirname(__FILE__) . '/../lib/Database.php');
include_once realpath(dirname(__FILE__) . '/../helpers/Format.php');
class Main
{
    private $db;
    private $fm;

    public function __construct()
    {
        $this->db = new Database();
        $this->fm = new Format();
    }

    // Thêm phương thức lấy tất cả người dùng
    public function getAllUsers()
    {
        $query = "SELECT * FROM users WHERE IsDeleted = 0"; // Lấy người dùng chưa bị xóa
        return $this->db->select($query);
    }
    // public function addQuestion($questionText, $questionImage, $departmentID, $manageTestID)
    // {
    //     $sql = "INSERT INTO questions (name, question_image, departmentID, manage_test_id, IsDeleted, IsActive, UpdateTime) 
    //             VALUES (:name, :question_image, :departmentID, :manage_test_id, 0, 1, NOW())";
    //     $stmt = $this->db->prepare($sql);

    //     // Binds
    //     $stmt->bindParam(':name', $questionText);
    //     $stmt->bindParam(':question_image', $questionImage);
    //     $stmt->bindParam(':departmentID', $departmentID);
    //     $stmt->bindParam(':manage_test_id', $manageTestID);

    //     // Execute
    //     return $stmt->execute();
    // }
    public function addAnswer($questionId, $answerText, $answerImage)
    {
        $sql = "INSERT INTO answers (question_id, answer_text, answer_image) VALUES (:question_id, :answer_text, :answer_image)";
        $stmt = $this->db->prepare($sql);

        // Binds
        $stmt->bindParam(':question_id', $questionId);
        $stmt->bindParam(':answer_text', $answerText);
        $stmt->bindParam(':answer_image', $answerImage);

        // Execute
        return $stmt->execute();
    }
    // In Main.php
    public function addQuestion($manageTestId, $departmentId, $questionText, $questionImage) {
        try {
            // Gọi stored procedure để thêm câu hỏi
            $stmt = $this->db->prepare("CALL AddQuesition(
                :manage_test_id, 
                :department_id, 
                :question_name, 
                :question_image
            )");
    
            $stmt->bindParam(':manage_test_id', $manageTestId);
            $stmt->bindParam(':department_id', $departmentId);
            $stmt->bindParam(':question_name', $questionText);
            $stmt->bindParam(':question_image', $questionImage);
    
            // Thực thi stored procedure
            $stmt->execute();
    
            // Lấy kết quả từ stored procedure
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor(); // Đảm bảo đóng result set
    
            // Trả về question_id
            return $result['question_id'];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    

    public function addAnswerToQuestion($questionId, $answer) {
        try {
            // Gọi stored procedure để thêm đáp án
            $stmt = $this->db->prepare("CALL AddAnswerToQuestion(
                :question_id, 
                :answer_text, 
                :answer_image, 
                :correct
            )");
    
            // Gán các tham số cho stored procedure
            $stmt->bindParam(':question_id', $questionId);
            $stmt->bindParam(':answer_text', $answer['text']);
            $stmt->bindParam(':answer_image', $answer['image']);
            $stmt->bindParam(':correct', $answer['correct'], PDO::PARAM_BOOL);
    
            // Thực thi stored procedure
            $stmt->execute();
    
            // Giải phóng tập kết quả (nếu cần)
            $stmt->closeCursor();
    
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    public function impQuestion($manageTestId, $departmentId, $questionText, $questionImage = null) {
        try {
            // Chuẩn bị câu lệnh SQL để thêm câu hỏi
            $stmt = $this->db->prepare("
                INSERT INTO questions (manage_test_id, departmentId, name, question_image, UpdateTime)
                VALUES (:manage_test_id, :department_id, :question_text, :question_image, NOW())
            ");
    
            // Gán các tham số
            $stmt->bindParam(':manage_test_id', $manageTestId);
            $stmt->bindParam(':department_id', $departmentId);
            $stmt->bindParam(':question_text', $questionText);
            $stmt->bindParam(':question_image', $questionImage);
    
            // Thực thi câu lệnh SQL
            $stmt->execute();
    
            // Sau khi thêm câu hỏi, thực hiện một truy vấn để lấy ID của câu hỏi vừa chèn
            $stmt = $this->db->prepare("
                SELECT id FROM questions
                WHERE manage_test_id = :manage_test_id
                AND departmentId = :department_id
                AND name = :question_text
                ORDER BY UpdateTime DESC LIMIT 1
            ");
    
            $stmt->bindParam(':manage_test_id', $manageTestId);
            $stmt->bindParam(':department_id', $departmentId);
            $stmt->bindParam(':question_text', $questionText);
    
            $stmt->execute();
    
            // Lấy ID của câu hỏi vừa được thêm
            $question = $stmt->fetch(PDO::FETCH_ASSOC);
            return $question['id'];
    
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    public function addAll($questionId, $answer) {
        try {
            // Chuẩn bị câu lệnh để thêm đáp án vào bảng answers
            $stmt = $this->db->prepare("INSERT INTO answers (
                questions_id, 
                answer, 
                answer_image, 
                correct, 
                IsDeleted, 
                IsActive, 
                UpdateTime
            ) VALUES (
                :question_id, 
                :answer_text, 
                :answer_image, 
                :correct, 
                0, 1, NOW()
            )");
    
            $stmt->bindParam(':question_id', $questionId);
            $stmt->bindParam(':answer_text', $answer['text']);
            $stmt->bindParam(':answer_image', $answer['image']);
            $stmt->bindParam(':correct', $answer['correct'], PDO::PARAM_BOOL);
    
            // Thực thi câu lệnh
            if ($stmt->execute()) {
                return true; // Trả về true khi thành công
            } else {
                return false; // Trả về false khi thất bại
            }
    
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    
    public function checkExistingQuestion($manageTestId, $departmentId, $questionText) {
        $stmt = $this->db->prepare("SELECT id FROM questions WHERE manage_test_id = :manage_test_id AND departmentId = :department_id AND name = :question_text");
        $stmt->bindParam(':manage_test_id', $manageTestId);
        $stmt->bindParam(':department_id', $departmentId);
        $stmt->bindParam(':question_text', $questionText);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result ? $result['id'] : null;
    }
    

    public function uploadImage($file, $type = 'questions')
    {
        // Kiểm tra xem file có hợp lệ hay không
        if (is_array($file) && isset($file['tmp_name']) && $file['error'] == 0) {
            // Thư mục upload dựa vào loại câu hỏi hoặc đáp án
            $uploadDir = 'uploads/' . $type . '/';
    
            // Đảm bảo thư mục tồn tại
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
    
            // Tạo tên file duy nhất
            $fileName = uniqid() . '-' . basename($file['name']);
            $uploadFilePath = $uploadDir . $fileName;
    
            // Di chuyển file tới thư mục upload
            if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
                return $uploadFilePath;  // Trả về đường dẫn nếu tải lên thành công
            } else {
                return false;  // Trả về false nếu tải lên thất bại
            }
        } else {
            // Trả về false nếu tệp tin không hợp lệ
            return false;
        }
    }
    
    

    function getDepartments()
    {
        $sql = "SELECT id, name FROM department WHERE IsDeleted = 0 AND IsActive = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    function getManageTests()
    {
        $sql = "SELECT id, name FROM manage_test WHERE IsDeleted = 0 AND IsActive = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getManageImp($testName) {
        error_log("Tên bài kiểm tra: " . $testName);
        $stmt = $this->db->prepare("SELECT id FROM manage_test WHERE name = :testName");
        $stmt->bindParam(':testName', $testName);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    }
    
    public function getDepartmentImp($departmentName) {
        error_log("Tên phòng ban: " . $departmentName);
        $stmt = $this->db->prepare("SELECT id FROM department WHERE name = :departmentName");
        $stmt->bindParam(':departmentName', $departmentName);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    }
    

    public function getResults($manv = '', $day = '', $month = '', $year = '', $test_name = '',$department_name='', $code='')
    {
        // Gọi stored procedure thay cho việc viết SQL phức tạp
        $sql = "CALL armcuff.getCheckAnswerUser(:day,:month, :year, :manv,:test_name,:department_name,:code)";

        // Chuẩn bị câu truy vấn
        $stmt = $this->db->prepare($sql);

        // Gán giá trị cho các tham số tìm kiếm
        // Nếu tham số trống thì đặt giá trị NULL
        $stmt->bindValue(':manv', $manv === '' ? null : $manv, PDO::PARAM_STR);
        $stmt->bindValue(':day', $day === '' ? null : $day, PDO::PARAM_INT);
        $stmt->bindValue(':month', $month === '' ? null : $month, PDO::PARAM_INT);
        $stmt->bindValue(':year', $year === '' ? null : $year, PDO::PARAM_INT);
        $stmt->bindValue(':test_name', $test_name === '' ? null : $test_name, PDO::PARAM_STR);
        $stmt->bindValue(':department_name', $department_name === '' ? null : $department_name, PDO::PARAM_STR);
        $stmt->bindValue(':code', $code === '' ? null : $code, PDO::PARAM_STR);
        // Thực thi câu truy vấn
        $stmt->execute();

        // Trả về kết quả
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Phương thức cập nhật thông tin người dùng
    public function updateUser($manv, $fullname, $username, $password, $isActive)
    {
        // Mã hóa mật khẩu
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "UPDATE users 
                  SET fullname = :fullname, username = :username, password = :password, IsActive = :isActive, UpdateTime = NOW() 
                  WHERE manv = :manv";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':manv', $manv);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':isActive', $isActive);

        return $stmt->execute(); // Trả về true nếu cập nhật thành công
    }

    // Phương thức xóa người dùng (chuyển sang trạng thái đã xóa)
    public function deleteUser($manv)
    {
        $query = "UPDATE users SET IsDeleted = 1, DeletedTime = NOW() WHERE manv = :manv";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':manv', $manv);

        return $stmt->execute(); // Trả về true nếu xóa thành công
    }
    // View_results
    public function getQuestionsAndAnswersByTestName($test_name) {
        // Truy vấn để lấy câu hỏi và câu trả lời dựa trên test_name
        $query = "SELECT * FROM questions q JOIN answers a ON q.question_id = a.question_id WHERE q.test_name = :test_name";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':test_name', $test_name);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getUserAnswersByTestName($manv, $test_name) {
        // Truy vấn để lấy câu trả lời của người dùng dựa trên test_name
        $query = "SELECT * FROM user_answers WHERE manv = :manv AND test_name = :test_name";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':manv', $manv);
        $stmt->bindParam(':test_name', $test_name);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCorrectAnswersCountByTestName($manv, $test_name) {
        // Truy vấn để lấy số câu đúng dựa trên test_name
        $query = "SELECT COUNT(*) FROM user_answers WHERE manv = :manv AND test_name = :test_name AND is_correct = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':manv', $manv);
        $stmt->bindParam(':test_name', $test_name);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    // public function updateUserAnswer($question_id, $answer_id) {
    //     $sql = "UPDATE user_answers SET answer_id = :answer_id WHERE question_id = :question_id AND user_id = :user_id";
    //     $stmt = $this->db->prepare($sql);
    //     $stmt->bindValue(':answer_id', $answer_id);
    //     $stmt->bindValue(':question_id', $question_id);
    //     $stmt->bindValue(':user_id', $_SESSION['user_id']); // Lấy user_id từ session hoặc nguồn khác
    //     $stmt->execute();
    // }
    //Update Answers 
    public function updateUserAnswer($manv, $test_id, $question_id, $answer_id, $code, $testType, $result_test) {
        // Gọi thủ tục lưu trữ AddUserAnswer
        $stmt = $this->db->prepare("CALL AddUserAnswer(?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$manv, $question_id, $answer_id, $test_id, $code, $testType, $result_test]);
    }

    public function addTest($testName, $departmentId) {
        $sql = "INSERT INTO manage_test (name, IsDeleted, IsActive, UpdateTime, department_id) 
                VALUES (:name, 0, 1, NOW(), :department_id)";
        $stmt = $this->db->getPDO()->prepare($sql);
        $stmt->bindParam(':name', $testName, PDO::PARAM_STR);
        $stmt->bindParam(':department_id', $departmentId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Lấy ID của bản ghi vừa chèn bằng lastInsertId
            return $this->db->getPDO()->lastInsertId();
        }
        return false;
    }
    public function addDepartment($departmentName) {
        $sql = "INSERT INTO department (name, IsDeleted, IsActive, UpdateTime) 
                VALUES (:name, 0, 1, NOW())";
        $stmt = $this->db->getPDO()->prepare($sql);
        $stmt->bindParam(':name', $departmentName, PDO::PARAM_STR);
    
        if ($stmt->execute()) {
            // Lấy ID của phòng ban vừa thêm bằng lastInsertId
            return $this->db->getPDO()->lastInsertId();
        }
        return false; // Trả về false nếu có lỗi
    }
    
}


