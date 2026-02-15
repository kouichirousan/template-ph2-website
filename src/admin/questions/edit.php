<?php
// ★変更1: session_start()を一番上に移動★
session_start();

require "../../vendor/autoload.php";
use Verot\Upload\Upload;

require_once '../../dbconnect.php';

/*
-----------------------------------------
  ① GET：編集画面を開いたとき
-----------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // URLから id を取得
    if (!isset($_GET['id'])) {
        echo "IDが指定されていません。";
        exit;
    }

    $id = $_GET['id'];

    // 問題（questions）の取得
    $stmt = $dbh->prepare('SELECT * FROM questions WHERE id = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $question = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$question) {
        echo "問題が存在しません。";
        exit;
    }

    // 選択肢（choices）の取得（3つ）
    $stmt2 = $dbh->prepare('SELECT * FROM choices WHERE question_id = :id');
    $stmt2->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $options = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}

/*
-----------------------------------------
  ② POST：更新処理
-----------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ★変更2: バリデーション追加（id、content、name1-3、valid の必須チェック）★
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        $_SESSION['error'] = '問題IDが不正です';
        header('Location: ../index.php');
        exit;
    }

    if (!isset($_POST['content']) || empty($_POST['content'])) {
        $_SESSION['error'] = '問題文を入力してください';
        header('Location: ./edit.php?id=' . $_POST['id']);
        exit;
    }

    if (!isset($_POST['name1']) || empty($_POST['name1']) ||
        !isset($_POST['name2']) || empty($_POST['name2']) ||
        !isset($_POST['name3']) || empty($_POST['name3'])) {
        $_SESSION['error'] = 'すべての選択肢を入力してください';
        header('Location: ./edit.php?id=' . $_POST['id']);
        exit;
    }

    if (!isset($_POST['valid']) || !in_array($_POST['valid'], ['1', '2', '3'])) {
        $_SESSION['error'] = '正解を選択してください';
        header('Location: ./edit.php?id=' . $_POST['id']);
        exit;
    }

    $id = $_POST['id'];
    $content = $_POST['content'];
    $supplement = $_POST['supplement'] ?? '';
    $name1 = $_POST['name1'];
    $name2 = $_POST['name2'];
    $name3 = $_POST['name3'];
    $valid = $_POST['valid'];

    // ★変更3: 画像アップロードを任意化（アップロードされている場合のみ処理）★
    $image_path = null; // デフォルトはnull（画像更新なし）

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // ★変更4: class.upload.phpを使った画像バリデーション・リサイズ★
        $handle = new Upload($_FILES['image'], 'ja_JP');

        if ($handle->uploaded) {
            // ① ファイルサイズのバリデーション：5MBまで
            $handle->file_max_size = 5 * 1024 * 1024; // 5MB

            // ② ファイルの拡張子とMIMEタイプをチェック：jpeg、png、gif
            $handle->allowed = ['image/jpeg', 'image/png', 'image/gif'];

            // ③ ファイルの拡張子の統一：PNGに変換
            $handle->image_convert = 'PNG';

            // ④ サイズ統一：横幅 718px
            $handle->image_resize = true;
            $handle->image_x = 718;
            $handle->image_ratio_y = true; // 縦横比を維持

            // アップロードディレクトリを指定して保存
            $handle->process('../../assets/img/quiz/');

            if ($handle->processed) {
                // アップロード成功 → 画像パスを保存
                $image_path = 'assets/img/quiz/' . $handle->file_dst_name;
                $handle->clean(); // メモリ解放
            } else {
                // ★変更6: エラー処理改善（SESSION使用）★
                $_SESSION['error'] = '画像アップロードエラー: ' . $handle->error;
                header('Location: ./edit.php?id=' . $id);
                exit;
            }
        } else {
            // アップロード失敗
            $_SESSION['error'] = '画像アップロードエラー: ' . $handle->error;
            header('Location: ./edit.php?id=' . $id);
            exit;
        }
    }

    // もう一度選択肢を取得（POST時にも必要）
    $stmt2 = $dbh->prepare('SELECT * FROM choices WHERE question_id = :id');
    $stmt2->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt2->execute();
    $options = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $dbh->beginTransaction();

    try {
        // -------------------------
        // questions 更新
        // -------------------------
        // ★変更5: 画像がアップロードされた場合のみimageカラムも更新★
        if ($image_path !== null) {
            $sql = "UPDATE questions 
                    SET content = :content,
                        supplement = :supplement,
                        image = :image
                    WHERE id = :id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':content', $content);
            $stmt->bindValue(':supplement', $supplement);
            $stmt->bindValue(':image', $image_path);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        } else {
            $sql = "UPDATE questions 
                    SET content = :content,
                        supplement = :supplement
                    WHERE id = :id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':content', $content);
            $stmt->bindValue(':supplement', $supplement);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        }
        $stmt->execute();

        // -------------------------
        // choices 更新（正解だけ valid=1 にする）
        // -------------------------
        $choiceSql = "UPDATE choices 
                      SET name = :name, valid = :valid 
                      WHERE id = :choice_id";

        // 選択肢1
        $stmt1 = $dbh->prepare($choiceSql);
        $stmt1->bindValue(':name', $name1);
        $stmt1->bindValue(':valid', ($valid == 1) ? 1 : 0);
        $stmt1->bindValue(':choice_id', $options[0]['id'], PDO::PARAM_INT);
        $stmt1->execute();

        // 選択肢2
        $stmt2 = $dbh->prepare($choiceSql);
        $stmt2->bindValue(':name', $name2);
        $stmt2->bindValue(':valid', ($valid == 2) ? 1 : 0);
        $stmt2->bindValue(':choice_id', $options[1]['id'], PDO::PARAM_INT);
        $stmt2->execute();

        // 選択肢3
        $stmt3 = $dbh->prepare($choiceSql);
        $stmt3->bindValue(':name', $name3);
        $stmt3->bindValue(':valid', ($valid == 3) ? 1 : 0);
        $stmt3->bindValue(':choice_id', $options[2]['id'], PDO::PARAM_INT);
        $stmt3->execute();

        // -------------------------
        // 完了（DB確定）
        // -------------------------
        $dbh->commit();

        header('Location: ../index.php');
        exit;

    } catch (Exception $e) {
        $dbh->rollBack();
        // ★変更6: エラー処理改善（SESSION使用）★
        $_SESSION['error'] = '更新エラー: ' . $e->getMessage();
        header('Location: ./edit.php?id=' . $id);
        exit;
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編集画面</title>
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
                    <h1 class="text-2xl font-bold text-teal-600">問題編集</h1>
                    <button class="bg-teal-600 text-white px-4 py-2 rounded">ログアウト</button>
                </div>

                <h2 class="text-xl font-semibold text-teal-700 mb-6">問題編集</h2>

                <form action="" method="POST" enctype="multipart/form-data">

                    <input type="hidden" name="id" value="<?php echo $question['id']; ?>">

                    <div class="mb-4">
                        <label for="question" class="block text-sm font-medium text-gray-700">問題文:</label>
                        <input type="text" id="question" name="content"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500 shadow-sm"
                            placeholder="問題文を入力してください" value="<?php echo $question['content']; ?>">
                        <div>
                            <?php if (isset($_SESSION['error'])) {
                                echo '<p class = "text-red-600">' . htmlspecialchars($_SESSION['error']) . '</p>';
                                unset($_SESSION['error']);
                            }
                            ?>
                        </div>
                    </div>

                    <div class="mb-4 grid grid-cols-3 gap-4">
                        <div>
                            <label for="option1" class="block text-sm font-medium text-gray-700">選択肢1:</label>
                            <input type="text" id="option1" name="name1"
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500 shadow-sm"
                                placeholder="選択肢1を入力してください" value="<?php echo $options[0]['name']; ?>">
                            <div>
                                <?php if (isset($_SESSION['error'])) {
                                    echo '<p class = "text-red-600">' . htmlspecialchars($_SESSION['error']) . '</p>';
                                    unset($_SESSION['error']);
                                }
                                ?>
                            </div>
                        </div>
                        <div>
                            <label for="option2" class="block text-sm font-medium text-gray-700">選択肢2:</label>
                            <input type="text" id="option2" name="name2"
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500 shadow-sm"
                                placeholder="選択肢2を入力してください" value="<?php echo $options[1]['name']; ?>">
                            <div>
                                <?php if (isset($_SESSION['error'])) {
                                    echo '<p class = "text-red-600">' . htmlspecialchars($_SESSION['error']) . '</p>';
                                    unset($_SESSION['error']);
                                }
                                ?>
                            </div>
                        </div>
                        <div>
                            <label for="option3" class="block text-sm font-medium text-gray-700">選択肢3:</label>
                            <input type="text" id="option3" name="name3"
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500 shadow-sm"
                                placeholder="選択肢3を入力してください" value="<?php echo $options[2]['name']; ?>">
                            <div>
                                <?php if (isset($_SESSION['error'])) {
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
                                <input type="radio" name="valid" value="1" class="form-radio text-teal-500" <?php if ($options[0]['valid'] == 1)
                                    echo 'checked'; ?>>
                                <span class="ml-2">選択肢1</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="valid" value="2" class="form-radio text-teal-500" <?php if ($options[1]['valid'] == 1)
                                    echo 'checked'; ?>>
                                <span class="ml-2">選択肢2</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="valid" value="3" class="form-radio text-teal-500" <?php if ($options[2]['valid'] == 1)
                                    echo 'checked'; ?>>
                                <span class="ml-2">選択肢3</span>
                            </label>
                        </div>
                        <div>
                            <?php if (isset($_SESSION['error'])) {
                                echo '<p class = "text-red-600">' . htmlspecialchars($_SESSION['error']) . '</p>';
                                unset($_SESSION['error']);
                            }
                            ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="question_image" class="block text-sm font-medium text-gray-700">問題の画像:</label>
                        <input type="file" id="question_image" name="image"
                            class="mt-1 block w-full text-teal-500 file:border-gray-300 file:rounded-md file:px-4 file:py-2">
                        <div>
                            <?php if (isset($_SESSION['error'])) {
                                echo '<p class = "text-red-600">' . htmlspecialchars($_SESSION['error']) . '</p>';
                                unset($_SESSION['error']);
                            }
                            ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="hint" class="block text-sm font-medium text-gray-700">補足:</label>
                        <textarea id="hint" name="supplement" rows="4"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500 shadow-sm"
                            placeholder="補足を入力してください"><?php echo $question['supplement']; ?></textarea>
                    </div>

                    <button type="submit"
                        class="w-full bg-teal-600 text-white py-3 rounded-md text-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                        更新する
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>