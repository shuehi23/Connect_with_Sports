<?php 
require('function.php');
debug('---------------------------投稿削除-------------------------');

$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
debug('削除写真ID:'.$p_id);

try {
    $dbh = dbConnect();
    $sql = 'DELETE FROM photo WHERE id = ?';
    $data = array($p_id);
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