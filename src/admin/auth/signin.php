<?php

session_start();

require_once '../../dbconnect.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
        header('Location: ../auth/signin.php');
        $_SESSION['error'] = '正しいメールアドレスを入力してください';
        exit;
    }

  // SQL命令の準備
  $stmt = $dbh->prepare('SELECT * FROM users WHERE email = :email');

  // パラメータをバインドする
  $stmt->bindValue(":email", $_POST["email"]);

  // SQL文を実行する
  $stmt->execute();

  // 結果を変数に代入
  $user = $stmt->fetch();

  if($user && isset($_POST['password']) && password_verify($_POST['password'], $user['password'])) {
    $_SESSION['id'] = $user['id'];
    //idっていうキーを作る。ここで定義する。
    header('Location: ../index.php');
    exit;
  }else{
        $error = 'メールアドレスかパスワードが間違っています。';
  }


  echo $error;
}
//   } else {
//     header('Location: ./signin.php');
//     exit;
//   }

// if($user && isset($_POST['password']) && $_POST['password'] === $user['password']){
//     $_SESSION['id'] = $user['id'];
//     header('Location: ../index.php');
//     exit;
// // }else{
// //     header('Location: ./signin.php');
// //     exit;
//     }

// if($user && !isset($_POST['password']) && $_POST['password'] === $user['password']){
//     $_SESSION['id'] = $user['id'];
//     header('Location:./signin.php ');
//     exit;
// }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - POSSE</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <header class="bg-teal-500 text-white px-8 py-4 flex justify-between items-center">
        <div class="flex items-center">
            <h1 class="text-2xl font-bold">POSSE</h1>
        </div>
        <a href="#" class="text-sm hover:underline">ログアウト</a>
    </header>

    <!-- Main Content -->
    <div class="flex items-start justify-center pt-20 px-4">
        <div class="bg-white rounded-lg shadow-md p-8 w-full max-w-2xl">
            <h2 class="text-3xl font-bold text-gray-800 mb-8">ログイン</h2>
            
            <form action="" method="POST">
                <!-- Email Field -->
                <div class="mb-6">
                    <label for="email" class="block text-gray-700 font-medium mb-2">
                        Email
                    </label>
                    <div>
                        <?php
                            if(isset($_SESSION['error'])) {
                                echo '<p class="text-red-500 mb-2">'.htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8').'</p>';
                                unset($_SESSION['error']);
                            }
                        ?>
                    </div>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                        required
                    >
                </div>

                <!-- Password Field -->
                <div class="mb-8">
                    <label for="password" class="block text-gray-700 font-medium mb-2">
                        パスワード
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                        required
                    >
                </div>

                <!-- Login Button -->
                <div>
                    <button 
                        type="submit" 
                        class="bg-teal-500 text-white font-medium px-8 py-3 rounded-md hover:bg-teal-600 transition duration-200"
                    >
                        ログイン
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

