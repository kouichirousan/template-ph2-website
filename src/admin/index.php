<?php
session_start();
require_once '../dbconnect.php';
$questions = $dbh->query("SELECT * FROM questions")->fetchAll(PDO::FETCH_ASSOC);
if (!isset($_SESSION['id'])) {
  header('location: ./auth/signin.php');
  exit;
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POSSE - 問題一覧</title>
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
                    <a href="./index.php" class="flex items-center text-white bg-teal-700 rounded px-3 py-2">
                        <span>問題一覧</span>
                    </a>
                </li>
                <li>
                    <a href="./questions/create.php" class="flex items-center text-white hover:bg-teal-700 rounded px-3 py-2">
                        <span>問題作成</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="flex-1">
            <div class="max-w-4xl mx-auto py-8 px-4">
                <!-- Header -->
                <header class="flex items-center justify-between mb-6">
                    <h1 class="text-3xl font-semibold text-teal-500">管理画面</h1>
                    <form action="./auth/signout.php" method='POST'>
                        <button class="text-sm text-gray-600 hover:text-teal-500" type='submit'>ログアウト</button>
                    </form>
                </header>

        <!-- Main Content -->
        <section class="mt-6">
            <h2 class="text-2xl font-semibold mb-4">問題一覧</h2>
            
            <table class="min-w-full table-auto border-collapse bg-white shadow-md rounded-lg">
                <thead class="bg-teal-500 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left">ID</th>
                        <th class="px-6 py-3 text-left">問題</th>
                        <th class="px-6 py-3 text-left">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($questions as $index => $question): ?>
                    <tr class="border-b">
                        <td class="px-6 py-3"><?php echo $question['id']; ?></td>
                        <td class="px-6 py-3"><a href="./questions/edit.php?id=<?php echo $question['id']; ?>"><?php echo htmlspecialchars($question['content']); ?></a></td>
                        <td class="px-6 py-3">
                            <form method="POST" action="../admin/questions/delete.php" onsubmit="return confirm('本当に削除しますか？');">
                                <input type="hidden" name="delete_id" value="<?php echo $question['id']; ?>">
                                <button type="submit" class="text-red-500 hover:underline">
                                削除
                                </button>
                            </form>
</td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
            </div>
        </div>
    </div>

</body>
</html>
