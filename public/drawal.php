<?php
require('function.php');
debug('--------------------退会ページ---------------------');
debugLogStart();

require('auth.php');

if (!empty($_POST)) {
    debug('POST送信があります');
    try {
        $dbh = dbConnect();
        $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = ? AND NOT id = ?';
        $sql2 = 'UPDATE article SET delete_flg = 1 WHERE posted_id = ?';
        $sql3 = 'UPDATE favorite SET delete_flg = 1 WHERE user_id = ?';

        $data = array($_SESSION['user_id'], $gestUserId);

        $stmt1 = queryPost($dbh, $sql1, $data);

        if ($stmt1) {
            debug('クエリ成功');

            // セッション削除
            session_destroy();
            debug('トップページへ遷移します');
            header("Location:top.php");
            exit();
        } else {
            debug('クエリ失敗');
            $err_msg['common'] = MSG08;
        }
    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
        $err_msg['common'] = MSG08;
    }
}
?>
<?php
$sitetitle = '退会';
require('head.php');
?>

<body id="drawal">
    <?php require('header.php'); ?>
    <div class="wrapper">
        <?php require('sheader.php'); ?>
        <div class="join-img-wrap">
            <div class="join-wrap">
                <form action="" method="post">

                    <p class="join-msg raf"> with drawal </p>
                    <div class="err-msg"><?php if (!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
                    <div class="flex">
                        <p class="btn" style="margin-top:0;">
                            <input class="raf js-show-alert" type="submit" value="Yes" name="submit" class="js-show-alert">
                        </p>
                        <p class="btn" style="margin-top:0;">
                            <input type="button" class="raf" value="No" onclick="location.href='mypage.php'">
                        </p>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php require('footer.php'); ?>
</body>
