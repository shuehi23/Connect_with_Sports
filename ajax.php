<?php
require('function.php');

debug('-----------------------ajax----------------------');
debugLogStart();

// POST送信があり、ユーザーIDgaあり、ログインしている場合
if(isset($_POST['photoId']) && isset($_SESSION['user_id'])){
    debug('POST送信があります');
    $p_id = $_POST['photoId'];
    debug('写真ID:'.$p_id);
    
    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM favorite WHERE photo_id = ? AND user_id = ?';
        $data = array($p_id, $_SESSION['user_id']);
        $stmt = queryPost($dbh, $sql, $data);
        $resultCount = $stmt->rowCount();
        // レコードが１件でもある場合
        if (!empty($resultCount)) {
            // お気に入りされている場合レコードを削除する
            $sql = 'DELETE FROM favorite WHERE photo_id = ? AND user_id = ?';
            $data = array($p_id, $_SESSION['user_id']);
            $stmt =queryPost($dbh, $sql, $data);
            debug('お気に入りから外されました:'.$resultCount);
        }else{
            // レコード挿入
            $sql = 'INSERT INTO favorite SET photo_id = ?, user_id = ?, create_date = ?';
            $data = array($p_id, $_SESSION['user_id'], date('Y-m-d H:i:s'));
            $stmt = queryPost($dbh, $sql, $data);
            debug('お気に入りされました:'.$resultCount);
        }
    } catch(Exception $e) {
        error_log('エラー発生:'.$e->getMessage());
    }
    
}

if(isset($_POST['followIdkey']) && isset($_SESSION['user_id'])){
    debug('POST送信があります');
    $o_id = $_POST['followIdkey'];
    debug('相手ID:'.$o_id);
    debug('自分ID:'.$_SESSION['user_id']);
    
    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM follow WHERE user_id=? AND opponent_id=?';
        $data = array($_SESSION['user_id'],$o_id);
        $stmt = queryPost($dbh, $sql, $data);
        $resultCount = $stmt->rowCount();
        if(!empty($resultCount)){
            $sql = 'DELETE FROM follow WHERE user_id=? AND opponent_id=?';
            $data = array($_SESSION['user_id'], $o_id);
            $stmt = queryPost($dbh, $sql, $data);
            debug('フォロー解除');
        }else{
            $sql = 'INSERT INTO follow SET opponent_id=?,user_id=?,create_date=?';
            $data = array($o_id, $_SESSION['user_id'], date('Y-m-d H:i:s'));
            $stmt = queryPost($dbh, $sql ,$data);
            debug('フォローしました');
        }

    } catch (Exception $e) {
        error_log('エラー発生:' .$e->getMessage());
    }
}
?>