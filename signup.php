<?php
require('function.php');
debug('-------------------新規登録画面です---------------------');
debugLogStart();

// post送信したか
if(!empty($_POST)){
    debug('POST入力があります');
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];

    // 未入力チェック
    validRequired($name, 'name');
    validRequired($email, 'email');
    validRequired($pass, 'pass');
    validRequired($pass_re, 'pass_re');

    if(empty($err_msg)){
        // email形式
        validEmail($email, 'email');
        maxLen($email, 'email');
        validEmailDup($email);

        // パスワード
        maxLen($pass, 'pass');
        minLen($pass, 'pass');
        maxLen($pass_re, 'pass_re');
        minLen($pass_re, 'pass_re');
        validhalf($pass, 'pass');
        validhalf($pass_re, 'pass_re');
        if(empty($err_msg)){
            // パスワード再入力
            validMatch($pass, $pass_re, 'pass_re');
        }
        if(empty($err_msg)){
            debug('バリデーションOKです');
            // 例外処理
            try {
                $dbh = dbConnect();
                $sql = 'INSERT INTO users SET username=?, email=?, password=?, login_time=?, create_date=?';
                $data = array($name,$email,password_hash($pass,PASSWORD_DEFAULT),date('Y-m-d H:i:s'),date('Y-m-d H:i:s'));
                $stmt = queryPost($dbh, $sql, $data);
                if($stmt){
                    debug('クエリ成功');
                    $_SESSION['msg_success'] = SUC04;
                    $seslimit = 60*60;
                    $_SESSION['login_limit'] = $seslimit;
                    $_SESSION['login_date'] = time();
                    $_SESSION['user_id'] = $dbh->lastInsertId();

                    debug('セッション変数の中身:'.print_r($_SESSION,true));
                    header("Location:mypage.php");
                    exit();
                }else{
                    debug('クエリ失敗');
                    $err_msg['common'] = MSG08;
                }
            } catch (Exception $e) {
                error_log('エラー発生:' .$e->getMessage());
            }
        }
    }
}
?>
<?php
$sitetitle = '新規登録';
require('head.php');
?>
</head>
<body id="signup">
<?php require('header.php'); ?>

<div class="wrap">
    <div class="join-img-wrap">
        <div class="join-wrap">
        <form action="" method="post">
            
            <p class="join-msg raf">Sign up</p>
            <div class="err-msg"><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
                <div class="err-msg"><<?php echo errmsg('name') ?>/div>
                <label class="raf">  
                <input type="text" name="name" placeholder="username" value="<?php echo keep('name'); ?>">
                </label>
                <div class="err-msg"><?php echo errmsg('email'); ?></div>
                <label class="raf">
                <input type="text" name="email" placeholder="email" value="<?php echo keep('email'); ?>">
                </label>
                <!-- パスワード入力 -->
                <div class="err-msg"><?php errmsg('pass'); ?></div>
                <label class="raf">
                <input type="password" name="pass" placeholder="password" value="<?php echo keep('pass'); ?>">
                </label>
                <!-- パスワード再入力 -->
                <div class="err-msg"><?php echo errmsg('pass_re'); ?></div>
                <label class="raf">
                <input type="password" name="pass_re" placeholder="re-enter password" value="<?php echo keep ('pass_re'); ?>">
                </label>
                <p class="btn"><input class="raf" type="submit" value="signup" name="submit"></p>
        </form>
       </div>
    </div>
</div>

<?php require('footer.php') ?>