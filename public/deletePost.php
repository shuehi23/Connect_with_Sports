<?php 
require('function.php');
debug('---------------------------投稿削除-------------------------');

$a_id = (!empty($_GET['a_id'])) ? $_GET['a_id'] : '';
debug('削除投稿ID:'.$a_id);

try {
    $dbh = dbConnect();
    $sql = 'DELETE FROM article WHERE id = ?';
    $data = array($a_id);
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
        $_SESSION['msg_success'] = SUC07;
        debug('マイページへ遷移');
        header("Location:mypage.php");
        exit();
    }  
} catch (Exception $e) {
        error_log('エラー発生:' .$e->getMessage());
}
?>