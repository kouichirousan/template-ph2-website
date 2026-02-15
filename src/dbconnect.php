<?php
$dsn = 'mysql:host=db;dbname=posse;charset=utf8';
$user = 'root';
$password = 'root';

try {
    $dbh = new PDO($dsn, $user, $password);
    // 接続成功のメッセージは表示しない
} catch (PDOException $e) {
    // エラーメッセージはデバッグ時のみ表示
    error_log('Connection failed: ' . $e->getMessage());
}

// SQL ステートメント
$sql = 'SELECT *FROM questions';

// テーブル内のレコードを順番に出力
// foreach ($dbh->query($sql) as $row) {
//   echo $row['content'] . '<br>';
//   echo $row['id'] . '<br>'  ;
//   echo $row['supplement'] . '<br>'  ;
// }
?>