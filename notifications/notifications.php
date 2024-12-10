<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"> -->
<link rel="stylesheet" href="./css/sweetalert2.min.css">
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
<script src="./js/sweetalert2_nof.js"></script>


<script>
// Hàm hiển thị thông báo thành công
function showSuccessNotification(message) {


    Swal.fire({
        title: 'Thành công!',
        text: message,
        icon: 'success',
        confirmButtonText: 'OK',
        showCloseButton: true, // Thêm nút đóng
        timer: 5000, // Thời gian tự động đóng sau 5 giây
        timerProgressBar: true // Hiện thanh tiến trình
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer || result.dismiss === Swal.DismissReason.confirmButtonText ) {
            // Nếu thông báo được đóng tự động, chuyển hướng
            window.location.href = 'index.php'; // Chuyển hướng về trang index
        }
    });
}

// Hàm hiển thị thông báo lỗi
function showErrorNotification(message) {
    return Swal.fire({
        title: 'Lỗi!',
        text: message,
        icon: 'error',
        confirmButtonText: 'OK',
        showCloseButton: true, // Thêm nút đóng
        timer: 3000, // Thời gian tự động đóng sau 5 giây
        timerProgressBar: true // Hiện thanh tiến trình
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer || result.dismiss === Swal.DismissReason.confirmButtonText ) {
            // Nếu thông báo được đóng tự động, chuyển hướng
            window.location.href = 'index.php'; // Chuyển hướng về trang index
        }
    });
}


// Hàm hiển thị thông báo hướng dẫn
function showGuideNotification(guideText) {
    Swal.fire({
        title: 'Thông báo!',
        html: `<p>${guideText}</p>`,
        icon: 'info',
        confirmButtonText: 'OK',
        showCloseButton: true, // Thêm nút đóng
        timer: 3000, // Thời gian tự động đóng sau 5 giây
        timerProgressBar: true // Hiện thanh tiến trình
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer || result.dismiss === Swal.DismissReason.confirmButtonText ) {
            // Nếu thông báo được đóng tự động, chuyển hướng
            window.location.href = 'index.php'; // Chuyển hướng về trang index
        }
    });
}


</script>