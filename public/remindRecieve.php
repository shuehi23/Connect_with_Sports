<?php
require('function.php');
debug('------------------パスワード再発行認証キー入力ページ------------------');
debugLogStart();

if(empty($_SESSION['auth_key'])){
    header("Location:remindSend.php");
    exit();
}
if(!empty($_POST)){
    debug('POST送信があります');

    $authkey = $_POST['auth_key'];

    validRequired($authkey, 'auth_key');

    if(empty($err_msg)){
        validHalf($authkey, 'auth_key');
        validLength($authkey,'auth_key');

        if(empty($err_msg)){
            debug('バリデーションOKです');

            // 入力したものと認証キーが違った場合
            if($authkey !== $_SESSION['auth_key']){
                $err_msg['auth_key'] = MSG13;
            }

            // 有効期限が切れていた場合
            if(time() > $_SESSION['auth_limit']){
                $err_msg['auth_key'] = MSG14;
            }

            if(empty($err_msg)){
                debug('認証OKです');
                // パスワード生成
                $pass = makeRand();

                try {
                    $dbh = dbConnect();
                    $sql = 'UPDATE users SET password=? WHERE email=? AND delete_flg = 0';
                    $data = array(password_hash($pass,PASSWORD_DEFAULT),$_SESSION['auth_email']);
                    $stmt = queryPost($dbh, $sql, $data);
                    if($stmt) {
                        debug('クエリ成功');


                        $from = 'info@com';
                        $to = $_SESSION['auth_email'];
                        $subject = 'パスワード再発行認証';
                        $comment = <<<EOT
                        本メールアドレス宛にパスワードの再発行を致しました。
                        下記のURLにて再発行パスワードをご入力頂き、ログインください.

                        ログインページ：https://takahito_special_site.herokuapp.com/login.php
                        再発行パスワード：{$pass}
                        ※ログイン後、パスワードのご変更をお願い致します。
                        EOT;
                        sendMail($to, $subject, $comment, $from);
                        // セッション削除
                        session_unset();
                        $_SESSION['msg_success'] = SUC03;
                        debug('セッション変数の中身:'.print_r($_SESSION,true));
                        debug('ログインに遷移');
                        debug('$pass:'.$pass);
                        header("Location:login.php");
                        return;

                    }else{
                        debug('クエリに失敗しました');
                        $err_msg['common'] = MSG08;
                    }

                } catch(Exception $e){
                    error_log('エラー発生:' . $e->getMessage());
                    $err_msg['common'] = MSG08;
                }
            }
        }
    }
}
?>
<?php
$sitetitle = 'パスワード再発行認証キー入力ページ';
require('head.php');
?>
<body id="remindrecieve" style="position:relative;z-index:100;">
<p id="js-show-msg" class="msg-success" style="display:none;">
   <?php echo getFlash('msg_success'); ?>
</p>
<?php require('header.php'); ?>
<div class="wrapper" style="margin-top: 50px;">
     
    <div class="join-img-wrap">
        <div class="join-wrap">
            <form action="" method="post">
                <p class="join-msg min" style="font-size:18px;text-align:left;"> ご指定のメールアドレスにお送りした【パスワード再発行認証メール】内にある「認証キー」をご入力ください。</p>
                <div class="err-msg"><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
                    <div class="err-msg"><?php echo errmsg('auth_key'); ?></div>
                    <label>
                    <input type="text" name="auth_key" placeholder="認証キー" value="<?php echo keep('auth_key'); ?>">
                    </label>
                    <p class="btn"><input type="submit" class="raf" value="send" name="submit"></p>
            </form>
        </div>
    </div>
</div>
    
<?php require('footer.php'); ?>
</body>