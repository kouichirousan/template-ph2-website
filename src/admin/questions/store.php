<?php
session_start();

require "../../vendor/autoload.php";
use Verot\Upload\Upload;

require_once '../../dbconnect.php';


if (!isset($_POST['content'], $_POST['name1'], $_POST['name2'], $_POST['name3'], $_POST['valid'], $_FILES['image'])) {
    $_SESSION['error'] = 'すべての項目を入力してください';
    header('Location: ./create.php');
    exit;
}

// if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
//     $_SESSION['error'] = '画像ファイルをアップロードしてください';
//     header('Location: ./create.php');
//     exit;
// }

// $maxSize = 5 * 1024 * 1024; // 5MB
// if ($_FILES['image']['size'] >= $maxSize) {
//     $_SESSION['error'] = '画像ファイルのサイズは5MB以下にしてください';
//     header('Location: ./create.php');
//     exit;
// }


// if (getimagesize($_FILES['image']['tmp_name']) === false) {
//     $_SESSION['error'] = '画像ファイルをアップロードしてください';
//     header('Location: ./create.php');
//     exit;
// }

// $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

// $extension = strtolower(
//     pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION)
// );

// if (!in_array($extension, $allowedExtensions, true)) {
//     $_SESSION['error'] = '許可されていない拡張子です。';
//     header('Location: ./create.php');
//     exit;
// }

$file = $_FILES['image'];
$lang = 'ja_JP';
// これのlangってなんなんすかっていうことをいうといろんな言語のエラーメッセージがある中で日本語を選ぶっていう意味です。

// アップロードされたファイルを渡す
$handle = new Upload($file, $lang);

if ($handle->uploaded) {
    // ファイルの転送が成功したか確認
        // ① ファイルサイズのバリデーション：5MBまで
    $handle->file_max_size = 5 * 1024 * 1024; // 5MB
    
    // ② ファイルの拡張子とMIMEタイプをチェック：jpeg、png、gif
    $handle->allowed = ['image/jpeg', 'image/png', 'image/gif'];
    
    // ③ ファイルの拡張子の統一：PNGに変換
    $handle->image_convert = 'png';
    
    // ④ サイズ統一：横幅 718px
    $handle->image_resize = true;
    $handle->image_x = 718;
    $handle->image_ratio_y = true; // 縦横比を維持Ï

    // アップロードディレクトリを指定して保存
    $handle->process('../../assets/img/quiz/');
    if ($handle->processed) {
        // アップロード成功
        $image_name = $handle->file_dst_name;
    } else {
        // アップロード処理失敗
        throw new Exception($handle->error);
        // これのエラーの内容ってどういう風に決まってるかというとライブラリで勝手に認識して出力してくれるらしい。
    }
} else {
    // アップロード失敗
    throw new Exception($handle->error);
}


$content = $_POST['content'];
$supplement = $_POST['supplement'];
$name1 = $_POST['name1'];
$name2 = $_POST['name2'];
$name3 = $_POST['name3'];
$valid = $_POST['valid'];
// 多分ここ要らないっす


// $supplement = $_POST['supplement'];
// $image_name = uniqid(mt_rand(), true) . '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);
// $image_path = dirname(__FILE__) . '/../../assets/img/quiz/' . $image_name;
// move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
$sql = "INSERT INTO questions (content, supplement,image) VALUES ('$content', '$supplement','$image_path')";
$dbh->exec($sql);
$lastId = $dbh->lastInsertId();
$sql2 = "INSERT INTO choices (question_id, name, valid) VALUES
    ('$lastId', '$name1', " . ($valid == 1 ? 1 : 0) . "),
    ('$lastId', '$name2', " . ($valid == 2 ? 1 : 0) . "),
    ('$lastId', '$name3', " . ($valid == 3 ? 1 : 0) . ")";
$dbh->exec($sql2);
header('Location: ../index.php');

