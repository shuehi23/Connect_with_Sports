<?php
require('function.php');
debug('---------------------ログインページ--------------------');
debugLogStart();

// ログイン認証
require('auth.php');

if (!empty($_POST)) {
    debug('POST送信があります');

    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;

    // 未入力チェック
    validRequired($email, 'email');
    validRequired($pass, 'pass');

    // email形式チェック
    validEmail($email, 'email');
    // email最大文字数
    maxLen($email, 'email');

    // 半角チェック
    validHalf($pass, 'pass');
    // 最大文字数
    maxLen($pass, 'pass');
    // 最小文字数
    minLen($pass, 'pass');

    if (empty($err_msg)) {
        debug('バリデーションOK');

        try {
            $dbh = dbConnect();
            $sql = 'SELECT password,id FROM users WHERE email=? AND delete_flg = 0';
            $data = array($email);
            $stmt = queryPost($dbh, $sql, $data);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            debug('クエリ結果の中身:' . print_r($result, true));
            if (!empty($result) && password_verify($pass, array_shift($result))) {
                debug('パスワードがマッチしました');

                $limit = 60 * 60;
                $_SESSION['login_date'] = time();

                if ($pass_save) {
                    debug('ログイン保持にチェックがあります');
                    $_SESSION['login_limit'] = $limit * 24 * 30;
                } else {
                    debug('ログイン保持にチェックはありません');
                    $_SESSION['login_limit'] = $limit;
                }
                $_SESSION['user_id'] = $result['id'];

                debug('セッション変数の中身:' . print_r($_SESSION, true));
                header("Location:mypage.php");
                exit();
            } else {
                debug('パスワードがマッチしませんでした');
                $err_msg['common'] = MSG09;
            }
        } catch (Exception $e) {
            error_log('エラー発生:' . $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}

?>
<?php
$sitetitle = 'ログイン';
require('head.php');
?>

<body style="position:relative;z-index:100;">
    <p id="js-show-msg" class="msg-success" style="display:none;">
        <?php echo getFlash('msg_success'); ?>
    </p>

    <?php require('header.php'); ?>
    <div class="wrap">
        <div class="join-img-wrap">
            <div class="join-wrap">
                <form action="" method="post">
                    <p class="join-msg raf"> Login </p>
                    <div class="err-msg"><?php if (!empty($err_msg['common'])) echo $err_msg['common']; ?> </div>
                    <div class="err-msg"><?php echo errmsg('email'); ?></div>
                    <label class="raf" for="" method="post">
                        <input type="text" name="email" placeholder="email" value="<?php echo keep('email'); ?>">
                    </label>

                    <div class="err-msg"><?php echo errmsg('pass'); ?></div>
                    <label class="raf" for="" method="post">
                        <input type="password" name="pass" placeholder="password" value="<?php echo keep('pass'); ?>">
                    </label>

                    <!-- 次回ログインを保持 -->
                    <label class="raf" style="font-size: 24px;">
                        <input class="checkbox" type="checkbox" name="pass_save"><span class="check-item">keep login >></span>
                    </label>

                    <p class="btn"><input class="raf" type="submit" value="login" name="submit"></p>
                    <!-- パスワードを忘れた人 -->

                    <div class="login-note">
                        ゲストログインは下記の内容を入力してください。<br>E-Mail「guest@login.com」<br>パスワード「password」<br>（機能は一部制限されています）
                    </div>
                    <span class="raf forgot" style="font-size:24px;">forget password <span style="font-size: 24px;position:relative;left:6px;">>></span> </span><a href="remindSend.php" class="raf forgot" style="font-size:26px;position:relative;left:20px">password issued</a>

                </form>
            </div>
        </div>
    </div>
    <?php require('footer.php'); ?>
</body>
