<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <title>404 - ไม่พบหน้าที่คุณต้องการ</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                        'spin-slow': 'spin 6s linear infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        * {
            font-family: "Itim", serif;
            font-weight: 400;
            font-style: normal;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-100 flex items-center justify-center p-4">
    <div class="text-center">
        <div class="relative w-48 h-48 mx-auto mb-8">
            <div class="absolute inset-0 bg-purple-100 rounded-full animate-spin-slow"></div>
            <div class="absolute inset-0 flex items-center justify-center animate-float">
                <i class="ri-ghost-line text-8xl text-purple-600"></i>
            </div>
            <i class="ri-question-line absolute top-0 left-0 text-2xl text-indigo-400 animate-bounce"></i>
            <i class="ri-error-warning-line absolute top-1/4 right-0 text-2xl text-purple-400 animate-pulse"></i>
            <i class="ri-question-line absolute bottom-0 right-1/4 text-2xl text-indigo-400 animate-bounce"></i>
        </div>

        <h1 class="text-6xl font-bold text-purple-600 mb-4">404</h1>
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">ไม่พบหน้าที่คุณต้องการ</h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            หน้าที่คุณกำลังค้นหาอาจถูกย้าย ลบ หรือไม่มีอยู่ในระบบ
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-purple-600 text-white rounded-full hover:bg-purple-700 transition-colors duration-300 shadow-lg hover:shadow-xl">
                <i class="ri-home-line"></i>
                <span>กลับหน้าหลัก</span>
            </a>
        </div>
    </div>

    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <i class="ri-ghost-line text-purple-100 text-8xl absolute top-1/4 left-1/4 animate-float"></i>
        <i class="ri-question-line text-indigo-100 text-6xl absolute top-1/3 right-1/4 animate-bounce"></i>
        <i class="ri-error-warning-line text-purple-100 text-7xl absolute bottom-1/4 left-1/3 animate-pulse"></i>
        <i class="ri-ghost-line text-indigo-100 text-9xl absolute bottom-1/4 right-1/3 animate-float"></i>
    </div>
</body>
</html>