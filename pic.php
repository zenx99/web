<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $secretKey = '6LenKsEqAAAAAOxgvCClceGy7M5-Me7Ipdf1zul_';  // กุญแจลับของคุณจาก Google reCAPTCHA
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    // ตรวจสอบการยืนยันจาก Google reCAPTCHA
    $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptchaResponse");
    $responseKeys = json_decode($verifyResponse, true);

    if(intval($responseKeys["success"]) !== 1) {
        echo json_encode(['status' => 'error', 'message' => 'การยืนยันแคปช่าล้มเหลว']);
        exit;
    }

    header('Content-Type: application/json');

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        if ($_FILES['image']['size'] > $maxFileSize) {
            echo json_encode(['status' => 'error', 'message' => 'ขนาดไฟล์ไม่สามารถเกิน 2MB']);
            exit;
        }

        $targetDir = "files/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileType = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($fileType), $allowedTypes)) {
            // สร้างแฮชของไฟล์เพื่อตรวจสอบความซ้ำซ้อน
            $fileHash = hash_file('sha256', $_FILES['image']['tmp_name']);
            $targetFilePath = $targetDir . $fileHash . '.' . $fileType;

            if (file_exists($targetFilePath)) {
                // ถ้าไฟล์มีอยู่แล้ว ส่ง URL เดิมกลับไป
                $fileUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . $targetFilePath;
                echo json_encode([
                    'status' => 'success',
                    'url' => $fileUrl,
                    'message' => 'ไฟล์นี้เคยถูกอัปโหลดแล้ว'
                ]);
                exit;
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $fileUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . $targetFilePath;
                echo json_encode([
                    'status' => 'success',
                    'url' => $fileUrl,
                    'message' => 'อัปโหลดสำเร็จ'
                ]);
                exit;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์']);
                exit;
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ประเภทไฟล์ไม่ถูกต้อง']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบไฟล์หรือเกิดข้อผิดพลาด']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <title>Dementor Pic - ฝากรูป</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="description" content="เว็ปฝากรูป Dementor">
    <link rel="icon" type="image/png" href="http://85.203.4.219/files/2528b632fe2f5c86a15da9b28d54bb7c">
    
    <meta name="keywords" content="ฝากรูป, dementor-pic, pic, dementor ">
    <meta property="og:image" content="http://85.203.4.219/files/2528b632fe2f5c86a15da9b28d54bb7c">
    
    <meta property="og:image:alt" content="เว็ปฝากรูป ที่ดีที่สุด">
    <meta property="og:image:width" content="630">
    <meta property="og:image:height" content="630">
    <style>
        * {
            font-family: "Itim", serif;
            font-weight: 400;
            font-style: normal;
        }
        .header {
            margin: 20px 0;
        }
        .logo img {
            max-width: 200px;
        }
        .g-recaptcha, ::after, ::before {
            box-sizing: content-box;
    
    border-width: 0;
    border-style: solid;
    border-color: #e5e7eb;
}
.g-recaptcha {
                width: 100%;
                    height: auto;
                         box-sizing: content-box;
 
                    transform: scale(0.85); /* ปรับขนาดให้เล็กลง */
                    transform-origin: 0 0; /* ตั้งค่าจุดเริ่มต้นให้เหมาะสม */    
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-500 to-purple-600 flex justify-center items-center p-4">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
        <div class="text-center mb-8">
            <center><div class="logo center mb-4 "><img src="http://85.203.4.219/files/3271f0139bf575247a83936d0ab903b0" alt=""></div></center>
            <i class="ri-image-add-line text-5xl text-purple-600 mb-4 block"></i>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">อัพโหลดรูปภาพ</h1>
            <p class="text-gray-600">รองรับไฟล์ JPG, JPEG, PNG และ GIF</p>
        </div>

        <form id="uploadForm" enctype="multipart/form-data">
            <div class="upload-area border-2 border-dashed border-purple-400 rounded-lg p-8 text-center cursor-pointer transition-all hover:bg-gray-50 mb-4">
                <i class="ri-upload-cloud-2-line text-3xl text-purple-600 mb-4 block"></i>
                <p class="text-gray-600 mb-4">คลิกหรือลากไฟล์มาที่นี่</p>
                <input type="file" name="image" id="image" accept="image/*" required class="hidden">
            </div>
            <center>   <div class="g-recaptcha" style="padding: 25px; box-sizing: content-box;" data-sitekey="6LenKsEqAAAAAGjcKfXli2NGNQOaPUD-e6HaQ-CP"></div></center>  
            <img id="preview" src="#" alt="preview" class="hidden w-full rounded-lg mb-4 border-2 border-dashed border-purple-400" style="background:rgb(235, 225, 245);">
                 <button id="uploadBtn" type="submit" disabled 
                    class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-3 px-8 rounded-full 
                           flex items-center justify-center gap-2 transition-all hover:-translate-y-0.5 hover:shadow-lg
                           disabled:opacity-70 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                <i class="ri-upload-2-line"></i>
                <span>อัพโหลด</span>
            </button>
        </form>
    </div>
    <script>
       document.addEventListener("keydown", function (e) {
    if (e.key === "F12" || (e.ctrlKey && e.shiftKey && e.key === "I")) {
        e.preventDefault();
        alert("ไม่สามารถใช้ Developer Tools บนเว็บไซต์นี้ได้");
    }
});

    </script>
    <script>document.addEventListener('contextmenu', event => event.preventDefault());    </script>
    <script>
        const imageInput = document.getElementById('image');
        const preview = document.getElementById('preview');
        const uploadBtn = document.querySelector('button[type="submit"]');
        const uploadArea = document.querySelector('.upload-area');

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('bg-gray-50');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('bg-gray-50');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('bg-gray-50');
            if (e.dataTransfer.files.length) {
                imageInput.files = e.dataTransfer.files;
                handleFileSelect();
            }
        });

        uploadArea.addEventListener('click', () => {
            imageInput.click();
        });

        imageInput.addEventListener('change', handleFileSelect);

        function handleFileSelect() {
            const file = imageInput.files[0];
            if (file) {
                uploadBtn.disabled = false;
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        $('#uploadForm').on('submit', function(e) {
    e.preventDefault();

    // ตรวจสอบว่า reCAPTCHA ถูกกรอกหรือไม่
    var recaptchaResponse = grecaptcha.getResponse();
    if (recaptchaResponse.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'โปรดยืนยันแคปช่า',
            text: 'กรุณา ยืนยันreCAPTCHA ก่อนที่จะอัปโหลดไฟล์'
        });
        return;
    }

    let formData = new FormData(this);
    const file = imageInput.files[0];

    // ตรวจสอบว่าไฟล์ถูกเลือกหรือไม่
    if (!file) {
        Swal.fire({
            icon: 'error',
            title: 'ไม่พบไฟล์',
            text: 'กรุณาเลือกไฟล์ก่อนที่จะอัปโหลด'
        });
        return;
    }

    // แสดงป๊อปอัพให้ยืนยันการอัปโหลด
    Swal.fire({
        title: 'ยืนยันการอัปโหลด',
        text: `คุณต้องการอัปโหลดไฟล์นี้: ${file.name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ใช่, อัปโหลด',
        cancelButtonText: 'ยกเลิก',
    }).then((result) => {
        if (result.isConfirmed) {
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> <span>กำลังอัพโหลด...</span>';

            $.ajax({
                url: '',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    uploadBtn.disabled = false;
                    uploadBtn.innerHTML = '<i class="ri-upload-2-line"></i> <span>อัพโหลด</span>';

                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'อัพโหลดสำเร็จ',
                            text: 'คัดลอกลิงก์ด้านล่าง:',
                            input: 'text',
                            timer: 12000,
                            inputValue: response.url,
                            showConfirmButton: true,
                            confirmButtonText: 'คัดลอก',
                            inputAttributes: {
                                readonly: true
                            },
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // คัดลอก URL
                                const inputElement = document.querySelector('.swal2-input');
                                inputElement.select();
                                inputElement.setSelectionRange(0, 99999); // สำหรับมือถือ
                                document.execCommand('copy');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'คัดลอกสำเร็จ',
                                    timer: 3600
                                });
                            }
                        });

                        // รีเซ็ตฟอร์ม
                        preview.classList.add('hidden');
                        imageInput.value = '';
                        setTimeout(function() {
                                location.reload();
                                }, 3000);
                    } else {
                        Swal.fire('ข้อผิดพลาด', response.message, 'error');
                    }
                },
                error: function() {
                    uploadBtn.disabled = false;
                    uploadBtn.innerHTML = '<i class="ri-upload-2-line"></i> <span>อัพโหลด</span>';
                    Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการอัพโหลดรูปภาพ', 'error');
                }
            });
        }
    });
});
    </script>
</body>
</html>
