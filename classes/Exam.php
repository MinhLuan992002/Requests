<?php

include_once realpath(dirname(__FILE__) . '/../lib/Database.php');
include_once realpath(dirname(__FILE__) . '/../helpers/Format.php');

class Exam
{
    private $db;
    private $fm;

    public function __construct()
    {
        $this->db = new Database();
        $this->fm = new Format();
    }
    public function getManageTests()
    {
        $stmt = $this->db->prepare("
            SELECT 
                id,
                name
            FROM 
                manage_test
            ORDER BY 
                id
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }





    // public function updateUserAnswer($manv, $test_id, $ques_id, $selected_answer)
    // {
    //     $sql = "UPDATE user_answer 
    //             SET answer = :answer, updated_at = NOW() 
    //             WHERE manv = :manv AND test_id = :test_id AND ques_id = :ques_id";
    //     $stmt = $this->db->prepare($sql);
    //     $stmt->execute([
    //         'answer' => $selected_answer,
    //         'manv' => $manv,
    //         'test_id' => $test_id,
    //         'ques_id' => $ques_id
    //     ]);
    // }

    public function updateCorrectAnswersCount($manv, $test_id, $correct_count)
    {
        $sql = "UPDATE test_results 
                SET correct_answers = :correct_answers 
                WHERE manv = :manv AND test_id = :test_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'correct_answers' => $correct_count,
            'manv' => $manv,
            'test_id' => $test_id
        ]);
    }




    // Lấy câu trả lời của người dùng cho một bài kiểm tra
    public function updateUserAnswers($manv, $test_id, $updatedAnswers)
    {
        foreach ($updatedAnswers as $quesId => $answer) {
            $query = "UPDATE user_answer SET answer = ? WHERE manv = ? AND test_id = ? AND ques_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$answer, $manv, $test_id, $quesId]);
        }
    }




    // Lấy thông tin số câu trả lời đúng (Nếu có thông tin trong manage_test)
    public function getResults($manv = '', $day = '', $month = '', $year = '', $test_name = '')
    {
        // Gọi stored procedure thay cho việc viết SQL phức tạp
        $sql = "CALL traning_forms.getManagerTest(:day,:month, :year, :manv,:test_name)";

        // Chuẩn bị câu truy vấn
        $stmt = $this->db->prepare($sql);

        // Gán giá trị cho các tham số tìm kiếm
        // Nếu tham số trống thì đặt giá trị NULL
        $stmt->bindValue(':manv', $manv === '' ? null : $manv, PDO::PARAM_STR);
        $stmt->bindValue(':day', $day === '' ? null : $day, PDO::PARAM_INT);
        $stmt->bindValue(':month', $month === '' ? null : $month, PDO::PARAM_INT);
        $stmt->bindValue(':year', $year === '' ? null : $year, PDO::PARAM_INT);
        $stmt->bindValue(':test_name', $test_name === '' ? null : $test_name, PDO::PARAM_STR);
        // Thực thi câu truy vấn
        $stmt->execute();

        // Trả về kết quả
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInterview($id = '', $day = '', $month = '', $year = '', $code = '')
    {
        // Gọi stored procedure thay cho việc viết SQL phức tạp
        $sql = "CALL traning_forms.getInterView(:day,:month, :year, :id,:search_code)";

        // Chuẩn bị câu truy vấn
        $stmt = $this->db->prepare($sql);

        // Gán giá trị cho các tham số tìm kiếm
        // Nếu tham số trống thì đặt giá trị NULL
        $stmt->bindValue(':id', $id === '' ? null : $id, PDO::PARAM_STR);
        $stmt->bindValue(':day', $day === '' ? null : $day, PDO::PARAM_INT);
        $stmt->bindValue(':month', $month === '' ? null : $month, PDO::PARAM_INT);
        $stmt->bindValue(':year', $year === '' ? null : $year, PDO::PARAM_INT);
        $stmt->bindValue(':search_code', $code === '' ? null : $code, PDO::PARAM_STR);
        // Thực thi câu truy vấn
        $stmt->execute();

        // Trả về kết quả
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getUserAnswersByCode($code) {
        $query = "SELECT ques_id, answer FROM user_answer WHERE code = :code";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['code' => $code]);

        $userAnswers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $userAnswers[$row['ques_id']] = $row['answer'];
        }

        return $userAnswers;
    }

    // Lấy thông tin người dùng theo code
    public function getUserInfoByCode($code) {
        $query = "SELECT ua.manv, u.fullname, ua.created_at AS test_date, ua.test_id
                  FROM user_answer ua
                  JOIN users u ON ua.manv = u.manv
                  WHERE ua.code = :code
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy tên bài kiểm tra
    public function getTestName($test_id) {
        $stmt = $this->db->prepare("SELECT name FROM manage_test WHERE id = :test_id");
        $stmt->bindParam(':test_id', $test_id, PDO::PARAM_INT);
        $stmt->execute();

        $name = $stmt->fetchColumn();
        return $name ? $name : null; // Trả về tên hoặc null nếu không tìm thấy
    }

    // Lấy câu hỏi và câu trả lời cho bài kiểm tra
    public function getQuestionsAndAnswers($test_id) {
        $stmt = $this->db->prepare("
            SELECT 
                q.id AS question_id,
                q.name AS question_text,
                a.id AS answer_id,
                a.answer AS answer_text,
                q.question_image AS question_image,
                a.answer_image AS answer_image,
                a.correct AS is_correct
            FROM 
                questions q
            JOIN 
                answers a ON q.id = a.questions_id
            WHERE 
                q.manage_test_id = :test_id
        ");
        $stmt->bindParam(':test_id', $test_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy số câu trả lời đúng của người dùng
    // public function getCorrectAnswersCount($manv, $test_id) {
    //     // Giả sử bạn đã có phương thức tính số câu đúng
    //     // Việc này có thể thực hiện bằng cách so sánh câu trả lời của người dùng với câu trả lời đúng
    //     // Trả về số câu trả lời đúng
    // }
    // Trong lớp Exam.php
    public function getUserAnswers($manv, $test_id)
    {
        $query = "SELECT  ques_id, answer FROM user_answer WHERE manv = :manv AND test_id = :test_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['manv' => $manv, 'test_id' => $test_id]);

        $userAnswers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $userAnswers[$row['ques_id']] = $row['answer']; // Lưu giá trị is_correct thay vì answer_id
        }

        return $userAnswers;
    }
    public function updateUserAnswer($manv, $test_id, $question_id, $answer_id, $code, $testType, $result_test) {
        // Gọi thủ tục lưu trữ AddUserAnswer
        $stmt = $this->db->prepare("CALL AddUserAnswer(?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$manv, $question_id, $answer_id, $test_id, $code, $testType, $result_test]);
    }
    // public function getQuestionsAndAnswers($test_id)
    // {
    //     $stmt = $this->db->prepare("
    //         SELECT 
    //             q.id AS question_id,
    //             q.name AS question_text,
    //             a.id AS answer_id,
    //             a.answer AS answer_text,
    //             a.correct AS is_correct
    //         FROM 
    //             questions q
    //         JOIN 
    //             answers a ON q.id = a.questions_id
    //         WHERE 
    //             q.manage_test_id = :test_id
    //     ");
    //     $stmt->bindParam(':test_id', $test_id, PDO::PARAM_INT);
    //     $stmt->execute();
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }


    
    // public function getTestName($test_id)
    // {
    //     $stmt = $this->db->prepare("SELECT name FROM manage_test WHERE id = :test_id");
    //     $stmt->bindParam(':test_id', $test_id, PDO::PARAM_INT);
    //     $stmt->execute();

    //     $name = $stmt->fetchColumn();
    //     return $name ? $name : null; // Trả về tên hoặc null nếu không tìm thấy
    // }

    public function getUserInfo($manv, $test_id)
    {
        $query = "SELECT ua.manv,  u.fullname, ua.created_at AS test_date
              FROM user_answer ua
              JOIN users u ON ua.manv = u.manv
              WHERE ua.manv = :manv AND ua.test_id = :test_id
              LIMIT 1"; // Giới hạn kết quả về một bản ghi
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':manv', $manv, PDO::PARAM_STR);
        $stmt->bindParam(':test_id', $test_id, PDO::PARAM_INT);
        $stmt->execute();
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        return $userInfo;
    }

    // Trong lớp Exam.php
    public function updateTestResultTimestamp($manv, $test_id)
    {
        $query = "UPDATE user_answer SET updated_at = NOW() WHERE manv = :manv AND test_id = :test_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'manv' => $manv,
            'test_id' => $test_id
        ]);
    }




    public function getCorrectAnswersCount($manv, $test_id)
    {
        $query = "SELECT COUNT(answer) AS correct_count 
              FROM user_answer 
              WHERE test_id = :test_id 
              AND manv = :manv 
              AND answer = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':manv', $manv, PDO::PARAM_INT); // Sử dụng PDO::PARAM_INT nếu `manv` là số nguyên
        $stmt->bindParam(':test_id', $test_id, PDO::PARAM_INT);
        $stmt->execute();

        // Lấy kết quả của câu lệnh SQL
        $result = $stmt->fetchColumn();

        return $result;
    }






    public function getCorrectAnswer($question_id)
    {
        $stmt = $this->db->prepare("
            SELECT answer
            FROM answers
            WHERE questions_id = :question_id AND correct = 1
        ");
        $stmt->bindParam(':question_id', $question_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }



    public function getResultsByTest($test_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM user_answer WHERE test_id = ?");
        $stmt->execute([$test_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResultById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM user_answer WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateResult($id, $answer)
    {
        $stmt = $this->db->prepare("UPDATE user_answer SET answer = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$answer, $id]);
    }

    public function getTestInfo($test_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM manage_test WHERE id = ?");
        $stmt->execute([$test_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm câu hỏi
    public function getAddQuestion($data)
    {
        $quesNo = $data['quesNo'];
        $ques = $data['ques'];
        $ans1 = $data['ans1'];
        $ans2 = $data['ans2'];
        $ans3 = $data['ans3'];
        $ans4 = $data['ans4'];
        $rightAns = $data['rightAns'];
        $manageTestName = $data['manage_test'];

        // Lấy ID của bài kiểm tra dựa trên tên
        $getManageTestIdQuery = "SELECT id FROM manage_test WHERE name = :manageTestName";
        $stmt = $this->db->prepare($getManageTestIdQuery);
        $stmt->bindParam(':manageTestName', $manageTestName, PDO::PARAM_STR);
        $stmt->execute();
        $manageTestId = $stmt->fetchColumn();

        if (!$manageTestId) {
            return "Manage test does not exist.";
        }

        // Kiểm tra xem số câu hỏi đã tồn tại chưa
        $checkQuery = "SELECT COUNT(*) AS count FROM questions WHERE question_no = :quesNo AND manage_test_id = :manageTestId";
        $stmt = $this->db->prepare($checkQuery);
        $stmt->bindParam(':quesNo', $quesNo, PDO::PARAM_INT);
        $stmt->bindParam(':manageTestId', $manageTestId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            return "Question Number already exists for this manage test.";
        }

        // Thêm câu hỏi mới vào bảng questions
        $sql = "INSERT INTO questions (manage_test_id, manage_test_name, question_no, question, answer1, answer2, answer3, answer4, correct_answer) 
                VALUES (:manageTestId, :manageTestName, :quesNo, :ques, :ans1, :ans2, :ans3, :ans4, :rightAns)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':manageTestId', $manageTestId, PDO::PARAM_INT);
        $stmt->bindParam(':manageTestName', $manageTestName, PDO::PARAM_STR);
        $stmt->bindParam(':quesNo', $quesNo, PDO::PARAM_INT);
        $stmt->bindParam(':ques', $ques, PDO::PARAM_STR);
        $stmt->bindParam(':ans1', $ans1, PDO::PARAM_STR);
        $stmt->bindParam(':ans2', $ans2, PDO::PARAM_STR);
        $stmt->bindParam(':ans3', $ans3, PDO::PARAM_STR);
        $stmt->bindParam(':ans4', $ans4, PDO::PARAM_STR);
        $stmt->bindParam(':rightAns', $rightAns, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "";
        } else {
            $errorInfo = $stmt->errorInfo();
            return "Error adding question: " . $errorInfo[2];
        }
    }


    // Lấy tất cả câu hỏi
    public function getqueData()
    {
        $query = "SELECT * FROM tbl_ques ORDER BY quesNo ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Xóa câu hỏi
    public function getdelresult($quesNo)
    {
        $tables = array("tbl_ques", "tbl_ans");
        $delResult = false;
        foreach ($tables as $table) {
            $query = "DELETE FROM $table WHERE quesNo = :quesNo";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':quesNo', $quesNo);
            $delResult = $stmt->execute();
        }
        if ($delResult) {
            return "<div class='alert alert-success'>Question Deleted Successfully!</div>";
        } else {
            return "<div class='alert alert-danger'>Data Not Deleted.</div>";
        }
    }
    // public function getManageTests() {
    //     $sql = "SELECT id, name FROM manage_test"; // Đảm bảo tên bảng chính xác
    //     $stmt = $this->db->prepare($sql);
    //     $stmt->execute();
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC); // Trả về dữ liệu dưới dạng mảng kết hợp
    // }    
    public function getManageTestIdByName($name)
    {
        $sql = "SELECT id FROM manage_test WHERE name = :name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    }


    public function isNewManageTest($manageTest)
    {
        // Kiểm tra xem giá trị mới có tồn tại trong cơ sở dữ liệu không
        $query = "SELECT COUNT(*) FROM manage_test WHERE name = :name";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $manageTest, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() == 0;
    }

    public function addManageTest($name)
    {
        $sql = "INSERT INTO manage_test (name) VALUES (:name)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);

        if ($stmt->execute()) {
            // Lấy ID của bài kiểm tra mới bằng cách thực hiện truy vấn SQL riêng
            $getIdSql = "SELECT id FROM manage_test WHERE name = :name ORDER BY id DESC LIMIT 1";
            $stmt = $this->db->prepare($getIdSql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->execute();
            $manageTestId = $stmt->fetchColumn();

            if ($manageTestId !== false) {
                return $manageTestId; // Trả về ID của bài kiểm tra mới
            } else {
                throw new Exception("Unable to retrieve the ID of the new manage test.");
            }
        } else {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Error adding manage test: " . $errorInfo[2]);
        }
    }
    public function getQuestionsByManageTest($manageTestId)
    {
        $sql = "SELECT question_no, question, answer1, answer2, answer3, answer4 
                FROM questions 
                WHERE manage_test_id = :manageTestId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':manageTestId', $manageTestId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function checkAndAddUser($employeeId, $employeeName)
    {
        // Kiểm tra nếu người dùng đã tồn tại
        $query = "SELECT * FROM users WHERE manv = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$employeeId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Thêm người dùng mới vào cơ sở dữ liệu
            $query = "INSERT INTO users (manv, fullname) VALUES (?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$employeeId, $employeeName]);
        } else {
            // Cập nhật thông tin người dùng nếu cần
            $query = "UPDATE users SET fullname = ? WHERE manv = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$employeeName, $employeeId]);
        }
    }

    public function getQuestionsByTest($manageTestId)
    {
        // Thêm câu lệnh gỡ lỗi

        // Truy vấn cơ sở dữ liệu
        $query = "SELECT * FROM questions WHERE manage_test_id = :manageTestId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':manageTestId', $manageTestId, PDO::PARAM_INT);
        $stmt->execute();

        // Lấy tất cả các câu hỏi
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Kiểm tra nếu có câu hỏi nào được tìm thấy
        if (!empty($result)) {
            return $result;
        } else {
            throw new Exception("No questions found for the specified test.");
        }
    }











    // Lấy tổng số câu hỏi
    public function getTotalRows()
    {
        $query = "SELECT COUNT(*) AS total FROM tbl_ques";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Lấy câu hỏi theo số câu hỏi
    public function getQuestionNumber($quesNo)
    {
        $query = "SELECT * FROM tbl_ques WHERE quesNo = :quesNo";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':quesNo', $quesNo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy câu trả lời theo số câu hỏi
    public function getAnswer($quesNumber)
    {
        $query = "SELECT * FROM tbl_ans WHERE quesNo = :quesNumber";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':quesNumber', $quesNumber);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalRowsByDepartment($department)
    {
        $query = "SELECT COUNT(*) AS total FROM questions_table WHERE department = :department";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':department', $department, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    public function getQuestionsByDepartment($department)
    {
        $query = "SELECT question_no, question, ans1, ans2, ans3, ans4 FROM questions_table WHERE department = :department";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':department', $department);
        $stmt->execute();
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Kiểm tra nếu không có câu hỏi
        if (empty($questions)) {
            throw new Exception("No questions found for the specified department.");
        }

        return $questions;
    }

    public function saveTestResults($employeeId, $testId, $testDate, $correctAnswersCount, $totalScore)
    {
        // Kiểm tra mã nhân viên có tồn tại trong bảng users không
        $query = "SELECT  FROM users WHERE manv = :manv";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':manv', $employeeId, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            $query = "INSERT INTO users (manv, fullname ) VALUES (:manv, :fullname)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':manv', $employeeId, PDO::PARAM_STR);
            $stmt->bindParam(':fullname', $employeeName, PDO::PARAM_STR);
            $stmt->execute();
        }

        // Kiểm tra mã nhân viên đã làm bài kiểm tra cho bài kiểm tra này chưa
        $query = "SELECT * FROM test_results WHERE manv = :manv AND test_id = :test_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':manv', $employeeId, PDO::PARAM_STR);
        $stmt->bindParam(':test_id', $testId, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            throw new Exception("Bạn đã làm bài kiểm tra cho bài test này rồi.");
        }

        // Lưu kết quả vào cơ sở dữ liệu
        $query = "INSERT INTO test_results (manv, test_id, test_date, correct_answers_count, score) 
                  VALUES (:manv, :test_id, :test_date, :correct_answers_count, :score)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':manv', $employeeId, PDO::PARAM_STR);
        $stmt->bindParam(':test_id', $testId, PDO::PARAM_INT);
        $stmt->bindParam(':test_date', $testDate, PDO::PARAM_STR);
        $stmt->bindParam(':correct_answers_count', $correctAnswersCount, PDO::PARAM_INT);
        $stmt->bindParam(':score', $totalScore, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi lưu dữ liệu: " . print_r($stmt->errorInfo(), true));
        }
    }






    public function calculateScore($answers, $testId)
    {
        // Lấy danh sách câu hỏi và đáp án đúng từ cơ sở dữ liệu
        $stmt = $this->db->prepare("SELECT question_no, correct_ans FROM questions WHERE manage_test_id = :test_id");
        $stmt->execute([':test_id' => $testId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalQuestions = count($questions);
        if ($totalQuestions === 0) {
            throw new Exception("Không có câu hỏi nào để chấm điểm.");
        }

        $correctAnswers = 0;

        foreach ($questions as $question) {
            $quesNo = $question['question_no'];
            $correctAnswer = $question['correct_ans'];
            if (isset($answers[$quesNo]) && $answers[$quesNo] == $correctAnswer) {
                $correctAnswers++;
            }
        }

        $score = ($correctAnswers / $totalQuestions) * 100; // Tính điểm dựa trên tỷ lệ câu đúng
        return ['correctAnswersCount' => $correctAnswers, 'totalScore' => round($score, 2)];
    }





    public function getCorrectAnswersByDepartment($department)
    {
        $query = "SELECT question_no, correct_ans FROM questions_table WHERE department = :department";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':department', $department);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getUserById($employeeId)
    {
        $query = "SELECT * FROM users WHERE manv = :manv";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':manv', $employeeId, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTotalScoreByUserId($employeeId, $department)
    {
        $query = "SELECT SUM(score) as total_score FROM test_results WHERE manv = :manv AND department = :department";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':manv', $employeeId, PDO::PARAM_STR);
        $stmt->bindParam(':department', $department, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['total_score'] : 0;
    }

    // Armcuff Fo

















    public function getCorrectAnswerByQuestionNo($department, $question_no)
    {
        $query = "SELECT correct_ans FROM questions_table WHERE department = :department AND question_no = :question_no";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':department', $department);
        $stmt->bindParam(':question_no', $question_no);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['correct_ans'];
    }


    //user làm bài
    public function getAllTrainingInfo()
    {
        $query = "SELECT * FROM training_info WHERE manv IS NULL;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    function savePersonalInfo($sonha, $phuongxa, $quan, $tinh, $cmnd, $sodt)
    {
        $query = "INSERT INTO personal_info (name_user,house_number, ward, district, province, id_number, phone_number)
              VALUES (:name_user,:sonha, :phuongxa, :quan, :tinh, :cmnd, :sodt)";
        $stmt = $this->db->prepare($query);

        // Gán giá trị cho các tham số
        $stmt->bindParam(':name_user', $name_user, PDO::PARAM_STR);
        $stmt->bindParam(':sonha', $sonha, PDO::PARAM_STR);
        $stmt->bindParam(':phuongxa', $phuongxa, PDO::PARAM_STR);
        $stmt->bindParam(':quan', $quan, PDO::PARAM_STR);
        $stmt->bindParam(':tinh', $tinh, PDO::PARAM_STR);
        $stmt->bindParam(':cmnd', $cmnd, PDO::PARAM_STR);
        $stmt->bindParam(':sodt', $sodt, PDO::PARAM_STR);

        // Thực thi câu lệnh SQL
        if ($stmt->execute()) {
            return "Lưu thông tin thành công!";
        } else {
            return "Lưu thông tin thất bại!";
        }
    }
    public function getTrainingInfoByManv($manv)
    {
        $stmt = $this->db->prepare("SELECT * FROM training_info WHERE manv = :manv");
        $stmt->bindParam(':manv', $manv, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getCorrectAnswersCountIn($code, $test_id) {
        // Giả sử bạn có bảng chứa câu trả lời đúng, đây là cách bạn có thể đếm
        $sql = "SELECT COUNT(*) as correct_count 
                FROM answers 
                WHERE user_code = :code 
                AND test_id = :test_id 
                AND is_correct = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['code' => $code, 'test_id' => $test_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['correct_count'];
    }
    
}
