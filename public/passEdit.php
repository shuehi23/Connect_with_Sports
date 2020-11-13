<?php
require('function.php');
debug('-----------------------パスワード編集画面----------------------');
debugLogStart();

// ログイン認証
require('auth.php');

$userpass = getUser($_SESSION['user_id']);

if (!empty($_POST)) {
    debug('POST情報:' . print_r($_POST, true));

    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $renew_pass = $_POST['renew_pass'];

    validRequired($old_pass, 'old_pass');
    validRequired($new_pass, 'new_pass');
    validRequired($renew_pass, 'renew_pass');
    if (empty($err_msg)) {

        validPass($old_pass, 'old_pass');
        validPass($new_pass, 'new_pass');

        if (!password_verify($old_pass, $userpass['password'])) {
            $err_msg['old_pass'] = MSG10;
        }

        if ($old_pass === $new_pass) {

            $err_msg['new_pass'] = MSG11;
        }

        validMatch($new_pass, $renew_pass, 'renew_pass');

        if (empty($err_msg)) {
            debug('バリデーションOKです');

            try {
                $dbh = dbConnect();
                $sql = 'UPDATE users SET password = ? WHERE id = ? AND NOT id = ?';
                $data = array(password_hash($renew_pass, PASSWORD_DEFAULT), $userpass['id'], $gestUserId);
                $stmt = queryPost($dbh, $sql, $data);
                if ($gestUserId === (int)$userpass['id']) {
                    $_SESSION['msg_success'] = SUC09;
                } else {
                    $_SESSION['msg_success'] = SUC01;
                }
                debug('セッション変数の中身:' . print_r($_SESSION, true));

                // メールを送信
                $username = (!empty($userpass['username'])) ? $userpass['username'] : '名無し';
                $from = 'info@com';
                $to = $userpass['email'];
                $subject = 'パスワード変更通知 | cheering_sport';
                $comment = <<<EOT
                    {$username}さん
                    パスワードが変更されました.
                    EOT;
                sendMail($to, $subject, $comment, $from);

                debug('マイページへ遷移');
                header("Location:mypage.php");
                exit();
            } catch (Exception $e) {
                error_log('エラー発生:' . $e->getMessage());
                $err_msg['common'] = MSG08;
            }
        }
    }
}
?>
<?php
$sitetitle = 'パスワード変更';
require('head.php');
?>

<body id="mypage">
    <?php require('header.php'); ?>
    <div class="wrapper">
        <div class="pass-img">
            <?php require('sheader.php'); ?>
            <div class="flex">
                <?php require('side.php'); ?>
                <div class="content-wrapper">
                    <div class="pass-wrapper">
                        <form action="" method="post">
                            <div class="err-msg"><?php echo errmsg('old_pass'); ?></div>
                            <label>
                                <input type="password" name="old_pass" placeholder="old password - 古いパスワード" value="<?php echo getFormData('old_pass'); ?>">
                            </label>
                            <div class="err-msg"><?php echo errmsg('new_pass'); ?></div>
                            <label>
                                <input type="password" name="new_pass" placeholder="new password - 新しいパスワード" value="<?php echo getFormData('new_pass'); ?>">
                            </label>
                            <div class="err-msg"><?php echo errmsg('renew_pass'); ?></div>
                            <label>
                                <input type="password" name="renew_pass" placeholder="re-enter password - パスワード再入力" value="<?php echo getFormData('renew_pass'); ?>">
                            </label>

                            <p class="btn"><input type="submit" class="raf" value="change" name="submit"></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require('footer.php'); ?>
</body>
