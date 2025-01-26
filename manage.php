<?php
$targetDir = "files/";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // ฟังก์ชันเพื่อดึงรายการไฟล์และเรียงตามวันที่
        function getSortedFiles($dir) {
            $files = array_diff(scandir($dir), ['.', '..']);
            $fileList = [];

            foreach ($files as $file) {
                $filePath = $dir . $file;
                if (is_file($filePath)) {
                    $fileList[] = [
                        'name' => $file,
                        'date' => filemtime($filePath)
                    ];
                }
            }

            usort($fileList, function ($a, $b) {
                return $b['date'] - $a['date']; // เรียงใหม่ -> เก่า
            });

            return $fileList;
        }

        if ($action === 'list') {
            $files = getSortedFiles($targetDir);
            echo json_encode(['status' => 'success', 'files' => $files]);
            exit;
        } elseif ($action === 'delete' && isset($_POST['filename'])) {
            $filename = basename($_POST['filename']);
            $filePath = $targetDir . $filename;

            if (file_exists($filePath)) {
                unlink($filePath);
                echo json_encode(['status' => 'success', 'message' => 'ลบไฟล์สำเร็จ']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ไม่พบไฟล์']);
            }
            exit;
        } elseif ($action === 'deleteByDays' && isset($_POST['days'])) {
            $days = intval($_POST['days']);
            $threshold = time() - ($days * 86400);
            $files = getSortedFiles($targetDir);

            $deletedCount = 0;
            foreach ($files as $file) {
                if ($file['date'] <= $threshold) {
                    unlink($targetDir . $file['name']);
                    $deletedCount++;
                }
            }

            echo json_encode(['status' => 'success', 'message' => "ลบไฟล์สำเร็จจำนวน $deletedCount ไฟล์"]);
            exit;
        }
    }

    echo json_encode(['status' => 'error', 'message' => 'คำขอไม่ถูกต้อง']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการรูปภาพ</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    #fileList li .file-name {
    display: inline-block;
    max-width: 80%; /* ปรับตามความเหมาะสม */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
</head>
<body class="min-h-screen bg-gray-100 flex justify-center items-center p-8">
    <div class="bg-white shadow-lg rounded-lg p-8 max-w-2xl w-full">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">📂 รายการรูปภาพที่อัปโหลด</h2>
        
        <div class="flex justify-between mb-4">
            <button onclick="deleteByDays(7)" class="bg-gray-700 text-white py-2 px-4 rounded-lg hover:bg-gray-800">ลบเกิน 7 วัน</button>
            <button onclick="deleteByDays(14)" class="bg-gray-700 text-white py-2 px-4 rounded-lg hover:bg-gray-800">ลบเกิน 14 วัน</button>
            <button onclick="deleteByDays(30)" class="bg-gray-700 text-white py-2 px-4 rounded-lg hover:bg-gray-800">ลบเกิน 30 วัน</button>
        </div>

        <ul id="fileList" class="space-y-4"></ul>
        
        <button onclick="loadFiles()" class="mt-6 w-full bg-blue-500 text-white py-3 rounded-lg hover:bg-blue-600 transition">🔄 โหลดไฟล์อีกครั้ง</button>
    </div>

    <script>
        function loadFiles() {
            $.post('manage.php', { action: 'list' }, function(response) {
                if (response.status === 'success') {
                    $('#fileList').empty();
                    if (response.files.length === 0) {
                        $('#fileList').append(`<li class="text-center text-gray-500">ไม่มีไฟล์อัปโหลด</li>`);
                    } else {
                        $('#fileList').append(`<li class="text-center font-bold">พบ ${response.files.length} ไฟล์</li>`);
                        response.files.forEach(file => {
                            let date = new Date(file.date * 1000).toLocaleDateString('th-TH');
                            $('#fileList').append(`
                                <li class="flex items-center justify-between bg-gray-50 p-4 rounded-lg shadow-sm">
                                    <div>
                                        <a href="files/${file.name}" target="_blank" class="file-name text-blue-600 font-medium hover:underline">${file.name}</a>
                                        <p class="text-sm text-gray-500">วันที่อัปโหลด: ${date}</p>
                                    </div>
                                    <button class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition" onclick="deleteFile('${file.name}')">ลบ</button>
                                </li>
                            `);
                        });
                    }
                }
            }, 'json');
        }

        function deleteFile(filename) {
            Swal.fire({
                title: 'ยืนยันการลบ',
                text: `คุณต้องการลบไฟล์ "${filename}" ใช่หรือไม่?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ลบไฟล์',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('manage.php', { action: 'delete', filename: filename }, function(response) {
                        if (response.status === 'success') {
                            Swal.fire('สำเร็จ!', 'ไฟล์ถูกลบแล้ว', 'success');
                            loadFiles();
                        } else {
                            Swal.fire('ข้อผิดพลาด', response.message, 'error');
                        }
                    }, 'json');
                }
            });
        }

        function deleteByDays(days) {
            Swal.fire({
                title: `ยืนยันการลบไฟล์เกิน ${days} วัน`,
                text: "คุณแน่ใจหรือไม่ว่าต้องการลบไฟล์เหล่านี้?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ลบไฟล์',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('manage.php', { action: 'deleteByDays', days: days }, function(response) {
                        Swal.fire('สำเร็จ!', response.message, 'success');
                        loadFiles();
                    }, 'json');
                }
            });
        }

        loadFiles();
    </script>
</body>
</html>