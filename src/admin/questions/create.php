<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>問題作成</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex">
        <!-- Sidebar -->
        <nav class="w-64 bg-teal-600 h-screen px-4 py-6">
            <div class="text-2xl font-bold text-white mb-8">POSSE</div>
            <ul class="space-y-4">
                <li>
                    <a href="#" class="flex items-center text-white hover:bg-teal-700 rounded px-3 py-2">
                        <span>ユーザ招待</span>
                    </a>
                </li>
                <li>
                    <a href="../index.php" class="flex items-center text-white hover:bg-teal-700 rounded px-3 py-2">
                        <span>問題一覧</span>
                    </a>
                </li>
                <li>
                    <a href="./create.php" class="flex items-center text-white bg-teal-700 rounded px-3 py-2">
                        <span>問題作成</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="flex-1">
            <div class="max-w-4xl mx-auto mt-10 bg-white p-8 rounded-lg shadow-lg">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-2xl font-bold text-teal-600">問題作成</h1>
                    <button class="bg-teal-600 text-white px-4 py-2 rounded">ログアウト</button>
                </div>

        <h2 class="text-xl font-semibold text-teal-700 mb-6">問題作成</h2>

        <form action="./store.php" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="question" class="block text-sm font-medium text-gray-700">問題文:</label>
                <input type="text" id="question" name="content" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500 shadow-sm" placeholder="問題文を入力してください">
                <div>
                    <?php if(isset($_SESSION['error'])){
                        echo '<p class = "text-red-600">' . htmlspecialchars($_SESSION['error']) . '</p>';
                        unset($_SESSION['error']);
                    }
                    ?>
                </div>
            </div>

            <div class="mb-4 grid grid-cols-3 gap-4">
                <div>
                    <label for="option1" class="block text-sm font-medium text-gray-700">選択肢1:</label>
                    <input type="text" id="option1" name="name1" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500 shadow-sm" placeholder="選択肢1を入力してください">
                    <div>
                        <?php if(isset($_SESSION['error'])){
                            echo '<p class = "text-red-600">' . htmlspecialchars($_SESSION['error']) . '</p>';
                            unset($_SESSION['error']);
                        }
                        ?>
                    </div>
                </div>
                <div>
                    <label for="option2" class="block text-sm font-medium text-gray-700">選択肢2:</label>
                    <input type="text" id="option2" name="name2" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500 shadow-sm" placeholder="選択肢2を入力してください">
                    <div>
                        <?php if(isset($_SESSION['error'])){
                            echo '<p class = "text-red-600">' . htmlspecialchars($_SESSION['error']) . '</p>';
                            unset($_SESSION['error']);
                        }
                        ?>
                    </div>
                </div>
                <div>
                    <label for="option3" class="block text-sm font-medium text-gray-700">選択肢3:</label>
                    <input type="text" id="option3" name="name3" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500 shadow-sm" placeholder="選択肢3を入力してください">
                    <div>
                        <?php if(isset($_SESSION['error'])){
                            echo '<p class = "text-red-600">' . htmlspecialchars($_SESSION['error']) . '</p>';
                            unset($_SESSION['error']);
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">正解の選択肢:</label>
                <div class="flex items-center space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="valid" value="1" class="form-radio text-teal-500">
                        <span class="ml-2">選択肢1</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="valid" value="2" class="form-radio text-teal-500">
                        <span class="ml-2">選択肢2</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="valid" value="3" class="form-radio text-teal-500">
                        <span class="ml-2">選択肢3</span>
                    </label>
                </div>
                <div>
                    <?php if(isset($_SESSION['error'])){
                        echo '<p class = "text-red-600">' . htmlspecialchars($_SESSION['error']) . '</p>';
                        unset($_SESSION['error']);
                    }
                    ?>
                </div>
            </div>

            <div class="mb-4">
                <label for="question_image" class="block text-sm font-medium text-gray-700">問題の画像:</label>
                <input type="file" id="question_image" name="image" class="mt-1 block w-full text-teal-500 file:border-gray-300 file:rounded-md file:px-4 file:py-2">
                <div>
                    <?php if(isset($_SESSION['error'])){
                        echo '<p class = "text-red-600">' . htmlspecialchars($_SESSION['error']) . '</p>';
                        unset($_SESSION['error']);
                    }
                    ?>
                </div>
            </div>

            <div class="mb-4">
                <label for="hint" class="block text-sm font-medium text-gray-700">補足:</label>
                <textarea id="hint" name="supplement" rows="4" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500 shadow-sm" placeholder="補足を入力してください"></textarea>
                <div>
                    <?php if(isset($_SESSION['error'])){
                        echo '<p class = "text-red-600">' . htmlspecialchars($_SESSION['error']) . '</p>';
                        unset($_SESSION['error']);
                    }
                    ?>
                </div>
            </div>

            <button type="submit" class="w-full bg-teal-600 text-white py-3 rounded-md text-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                作成
            </button>
        </form>
            </div>
        </div>
    </div>

</body>
</html>
