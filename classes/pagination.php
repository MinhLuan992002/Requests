<?php
function getPaginatedResults($conn, $table, $perPage = 10, $page = 1, $conditions = '') {
    $offset = ($page - 1) * $perPage;
    $sql = "SELECT * FROM $table $conditions LIMIT $offset, $perPage";
    $result = $conn->query($sql);

    // Tính toán số trang
    $countSql = "SELECT COUNT(*) AS total FROM $table $conditions";
    $countResult = $conn->query($countSql);
    $totalRows = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $perPage);

    return [
        'data' => $result,
        'totalPages' => $totalPages,
        'currentPage' => $page
    ];
}
?>
