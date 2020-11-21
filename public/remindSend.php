<?php
require('function.php');
debug('-----------------------パスワード再発行メール送信ページ------------------------');
debugLogStart();

if (!empty($_POST)) {
    $email = $_POST['email'];

    // 未入力チェック
    validRequired($email, 'email');
    if (empty($err_msg)) {
        maxLen($email, 'email');
        validEmail($email, 'email');
        //ゲストユーザーチェック（ゲストユーザは利用不可）
        validGestUserEmail($email, 'common');

        if (empty($err_msg)) {
            debug('バリデーションOKです');

            try {
                $dbh = dbConnect();
                $sql = 'SELECT count(*) FROM users WHERE email = ? AND delete_flg = 0';
                $data = array($email);
                $stmt = queryPost($dbh, $sql, $data);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($stmt && array_shift($result)) {
                    debug('クエリ成功です');
                    $_SESSION['msg_success'] = SUC02;
                    $authkey = makeRand();
                    $from = 'info@com';
                    $to = $email;
                    $subject = 'パスワード再発行認証';
                    $comment = <<<EOT
                    本メールアドレス宛にパスワード再発行のご依頼がありました。
                    下記のURLにて認証キーをご入力頂くとパスワードが再発行されます。

                    パスワード再発行認証キー入力ページ：https://takahito_special_site.herokuapp.com/remindrecieve.php
                    認証キー：{$authkey}
                    ※認証キーの有効期限は30分となります

                    認証キーを再発行されたい場合は下記ページより再度再発行をお願い致します。
                    https://takahito_special_site.herokuapp.com/remindsend.php
                    EOT;
                    sendMail($to, $subject, $comment, $from);

                    // 認証に必要な情報をセッションへ保存
                    $_SESSION['auth_key'] = $authkey;
                    $_SESSION['auth_email'] = $email;
                    // 認証キー有効期限は30分
                    $_SESSION['auth_limit'] = time() + (60 * 30);
                    debug('セッションの中身:' . print_r($_SESSION, true));
                    header("Location:remindRecieve.php");
                    exit();
                } else {
                    debug('クエリに失敗したかDBに登録のないEmailが入力されました');
                    $err_msg['common'] = MSG08;
                }
            } catch (Exception $e) {
                error_log('エラー発生:' . $e->getMessage());
                $err_msg['common'] = MSG08;
            }
        }
    }
}
?>
<?php
$sitetitle = 'パスワード再発行メール送信';
require('head.php');
?>

<body id="remindsend">
    <?php require('header.php'); ?>
    <div class="wrapper" style="margin-top: 50px;">

        <div class="join-img-wrap">
            <div class="join-wrap join-wrap-area">
                <form action="" method="post">

                    <p class="join-msg min" style="font-size: 18px;text-align:left;"> ご指定のメールアドレス宛に再発行用のURLと認証キーをお送りいたします。</p>
                    <div class="err-msg"><?php if (!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
                    <div class="err-msg"><?php echo errmsg('email'); ?></div>
                    <label class="raf" for="" method="post">
                        <input type="text" name="email" placeholder="email" value="<?php echo keep('email'); ?>">
                    </label>
                    <p class="btn"><input type="submit" class="raf" value="send" name="submit"></p>
                </form>
            </div>
        </div>
    </div>

    <?php require('footer.php'); ?>
</body>
