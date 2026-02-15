<?php
require_once '../../dbconnect.php';

if (!isset($_POST['delete_id'])) {
    echo 'IDが送られていません';
    exit;
}

$id = $_POST['delete_id'];

$dbh->beginTransaction();

try {
    $stmt = $dbh->prepare(
        'DELETE FROM choices WHERE question_id = :id'
    );
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $dbh->prepare(
        'DELETE FROM questions WHERE id = :id'
    );
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $dbh->commit();

    header('Location: ../index.php');
    exit;

} catch (Exception $e) {
    $dbh->rollBack();
    echo '削除に失敗しました';
}
