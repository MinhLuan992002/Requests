<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // Gửi form sử dụng AJAX (ví dụ sử dụng Fetch API)
  document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();  // Ngăn chặn việc reload trang sau khi submit form
    
    const formData = new FormData(this);

    fetch('submit_form.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                title: 'Thành công!',
                text: data.message,  // Thông báo thành công từ server
                icon: 'success',
                confirmButtonText: 'OK'
            });
        } else {
            Swal.fire({
                title: 'Lỗi!',
                text: data.message,  // Hiển thị lỗi chi tiết từ server
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Lỗi hệ thống:', error);
        Swal.fire({
            title: 'Lỗi hệ thống!',
            text: 'Có sự cố xảy ra. Vui lòng thử lại sau.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
});

</script>
