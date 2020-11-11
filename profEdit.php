<?php
require('function.php');
debug('-----------------プロフィール編集ページ-------------');

//ログイン認証
require('auth.php');

//DBからユーザー情報取得
$dbData = getUser($_SESSION['user_id']);
$areaData = areaData();
debug('ユーザー情報:' . print_r($dbData, true));
debug('エリア情報:' . print_r($areaData, true));

if (!empty($_POST)) {
    debug('POST情報:' . print_r($_POST, true));
    $username = $_POST['username'];
    $email = $_POST['email'];
    $job = $_POST['job'];
    $sports = $_POST['sports'];
    $area = $_POST['area'];
    $intro = $_POST['intro'];
    $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'], 'pic') : '';
    $pic = (empty($pic) && !empty($dbData['pic'])) ? $dbData['pic'] : $pic;

    if ($dbData['username'] !== $username) {
        maxLen($username, 'username');
    }
    if ($dbData['job'] !== $job) {
        maxLen($job, 'job');
    }
    if ($dbData['sports'] !== $sports) {
        maxLen($sports, 'sports');
    }
    if ($dbData['intro'] !== $intro) {
        maxLen($intro, 'intro');
    }
    if ($dbData['email'] !== $email) {
        maxLen($email, 'email');
        if (empty($err_msg['email'])) {
            validEmailDup($email);
        }
        validRequired($email, 'email');
        validEmail($email, 'email');
    }

    if (empty($err_msg)) {
        debug('バリデーションok');

        try {
            $dbh = dbConnect();
            $sql = 'UPDATE users SET username=?,email=?,job=?,sports=?,intro=?,area=?,pic=? WHERE id=? AND NOT id = ?';
            $data = array($username, $email, $job, $sports, $intro, $area, $pic, $dbData['id'], $gestUserId);
            $stmt = queryPost($dbh, $sql, $data);
            if ($gestUserId  === (int)$dbData['id']) {
                $_SESSION['msg_success'] = SUC09;
            } else {
                $_SESSION['msg_success'] = SUC01;
            }
            debug('マイページへ遷移');
            header("Location:mypage.php");
        } catch (Exception $e) {
            error_log('エラー発生:' . $e->getMessage());
        }
    }
}
?>

<?php
$sitetitle = 'プロフィール編集';
require('head.php');
?>
</head>

<body id="profedit">
    <?php require('header.php'); ?>
    <div class="wrapper">
        <div class="mypage-img">
            <?php require('sheader.php'); ?>
            <div class="flex">
                <?php require('side.php'); ?>
                <div class="content-wrapper">
                    <div class="prof-wrapper">
                        <form action="" method="post" enctype="multipart/form-data">
                            <label for="" class="raf" style="font-size:24px;">name - <span class="min" style="font-size:16px;vertical-align:middle;">お名前</span><br>
                                <input type="text" name="username" value="<?php echo getFormData('username'); ?>">
                            </label>
                            <div class="err-msg" style="bottom: 25px;"><?php echo errmsg('name'); ?></div>


                            <label for="" class="raf" style="font-size:24px;">email - <span class="min" style="font-size:16px;vertical-align:middle;">メールアドレス</span><br>
                                <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
                            </label>
                            <div class="err-msg" style="bottom:25px;"><?php echo errmsg('email'); ?></div>

                            <label class="raf" for="" method="post" style="font-size:24px;">job - <span class="min" style="font-size:16px;vertical-align:middle;">職業</span><br>
                                <input type="text" name="job" value="<?php echo getFormData('job'); ?>">
                            </label>
                            <div class="err-msg" style="bottom:25px;"><?php echo errmsg('job'); ?></div>

                            <label class="raf" for="" method="post" style="font-size:24px;">sports - <span class="min" style="font-size:16px;vertical-align:middle;">競技</span><br>
                                <input type="text" name="sports" value="<?php echo getFormData('sports'); ?>">
                            </label>
                            <div class="err-msg" style="bottom:25px;"><?php echo errmsg('sports'); ?></div>

                            <!--パスワード入力-->
                            <label class="raf" for="" style="font-size:24px;display:inline-block;margin-bottom:20px;">regidential area - <span class="min" style="font-size:16px;vertical-align:middle;">居住エリア</span><br>
                                <select class="min" name="area" id="area" style="font-size:14px;">
                                    <option value="0" <?php if (getFormData('area') == 0) echo 'selected'; ?>>選択してください</option>
                                    <?php foreach ($areaData as $key => $val) { ?>
                                        <option value="<?php echo $val['id']; ?>" <?php if (getFormData('area') == $val['id']) echo 'selected'; ?>><?php echo $val['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </label>
                            <div class="err-msg" style="bottom:25px;"><?php echo errmsg('area'); ?></div>

                            <label class="raf" for="" method="post" style="font-size:24px;">self introduction - <span class="min" style="font-size:16px;vertical-align:middle;">自己紹介</span><br>
                                <textarea name="intro" id="intro" cols="20" rows="5"><?php echo getFormData('intro'); ?></textarea>
                            </label>
                            <div class="err-msg" style="bottom:15px;"><?php echo errmsg('intro'); ?>
                                <p class="count-text">
                                    <span id="js-count-view">0</span>/200文字
                                </p>
                            </div>

                            <div class="flex" style="justify-content: space-between;">
                                <div class="flex culom">
                                    <label class="raf js-area-drop" for="" method="post" style="font-size:24px;">
                                        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                        <input type="file" name="pic" class="input-file">

                                        <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img" style="border-radius:50%;max-height:300px; <?php if (empty(getFormData('pic'))) echo 'display:none;'; ?>">
                                        drag & drop
                                    </label>
                                    <div class="err-msg" style="top:5px;"><?php echo errmsg('pic'); ?></div>
                                </div>


                            </div>

                            <!--パスワード再入力-->


                            <p class="btn" style="margin-top: 30px;"><input class="raf" type="submit" value="change" name="submit"></p>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require('footer.php'); ?>
