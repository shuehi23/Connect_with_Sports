    <?php
    require('function.php');
    debug('--------------------投稿詳細ページ----------------------');
    debugLogStart();

    $p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
    $m_id = (!empty($_GET['m_id'])) ? $_GET['m_id'] : '';
    $h_id = (!empty($_GET['h_id'])) ? $_GET['h_id'] : '';

    if (!empty($_POST['submit'])) {
        debug('POST送信があります');
        $selfcom = $_POST['selfcom'];
        validRequired($selfcom, 'selfcom');
        maxLen($selfcom, 'selfcom');

        // ログイン認証
        require('auth.php');

        if (empty($err_msg)) {
            debug('バリデーションOKです');
            try {
                $dbh = dbConnect();
                $sql = 'INSERT INTO partnerComment SET send_date=?, from_user=?, comment=?, create_date=?, photo_id=?';
                $data = array(date('Y-m-d H:i:s'), $_SESSION['user_id'], $selfcom, date('Y-m-d H:i:s'), $p_id);
                $stmt =  queryPost($dbh, $sql, $data);
                if ($stmt) {
                    $_POST = array();
                    header("Location:" . $_SERVER['PHP_SELF'] . appendGet());
                }
            } catch (Exception $e) {
                error_log('エラー発生:' . $e->getMessage());
            }
        }
    }
    $partnerDataCmt = getPartnerComment($p_id);
    debug('メッセージ情報:' .print_r($partnerDataCmt, true));
    // DBから投稿情報を取得
    debug($p_id);
    $viewData = getPostOne($p_id);
    debug('写真ID：' . $p_id);
    $getUser = getUser($h_id);
    $myInfo = getUser($_SESSION['user_id']);
    debug('myinfo中身:' . print_r($myInfo, true));
    // パラメータに不正な値が入っているかチェック
    if (empty($viewData)) {
        error_log('エラー発生:指定ページに不正な値が入りました');
        header("Location:postList.php");
        exit;
    }
    debug('取得したDBデータ:' . print_r($viewData, true));
    ?>

    <?php
    $sitetitle = '投稿詳細';
    require('head.php');
    ?>

    <body id="postdetail">
        <?php require('header.php'); ?>
        <div class="wrapper">
            <div class="post-detail-img">
                <?php require('sheader.php'); ?>
                <div class="flex wrap">
                    <div>
                        <div class="poster-wrap">

                            <div class="flex" style="flex-direction:column;">
                                <h3 style="font-size:35px;text-align:center;" class="raf">poster</h3>
                                <div class="poster-img"><img src="<?php echo $getUser['pic']; ?>" alt=""></div>
                                <p class="min" style="margin-top:10px;font-size:20px;"><?php echo $viewData['username']; ?></p>
                                <p><a class="raf" href="profDetail.php<?php echo (empty($h_id)) ? appendGet() . '&h_id=' . $viewData['posted_id'] : appendGet(); ?>">profile details >></a></p>
                                <p style="font-size:15px;"><?php echo $getUser['intro']; ?></p>
                            </div>
                        </div>
                        <p><a class="raf" style="font-size:20px;" href="postlist.php">
                                << back</a> </p> </div> <div class="detail-wrap">
                                    <div class="detail-title">
                                        <h2 class="raf">title -<span class="min" style="font-size:34px;margin-left:10px"><?php echo $viewData['title']; ?></span><i class="fas fa-heart js-like <?php if (isLike($_SESSION['user_id'], $viewData['id'])) echo 'active'; ?>" data-photoid="<?php echo sanitize($viewData['id']); ?>"></i></h2>
                                    </div>

                                    <div class="sub-title flex">
                                        <div class="place-title">
                                            <span class="title min">場所</span>
                                            <span class="txt"><?php echo sanitize($viewData['place']); ?></span>
                                        </div>
                                        <div class="key-title">
                                            <span class="title min">キーワード</span>
                                            <span class="txt"><?php echo sanitize($viewData['key1']); ?></span>
                                        </div>
                                        <div class="cate-title">
                                            <span class="title min">カテゴリー</span>
                                            <span class="txt"><?php echo sanitize($viewData['c_name']); ?></span><span><?php echo sanitize($viewData['key2']); ?></span><span><?php echo sanitize($viewData['key3']); ?></span>
                                        </div>
                                    </div>

                                    <div class="detail-img-wrap">
                                        <div class="detail-main"><img id="js-switch-imgmain" src="<?php echo showImg(sanitize($viewData['pic1'])); ?>" alt="" class="prev-mainimg"></div>
                                        <div class="sub-img-wrap flex">
                                            <div class="sub-img"><img src="<?php echo showImg(sanitize($viewData['pic1'])); ?>" alt="" class="js-switch-imgsub prev-subimg"></div>
                                            <div class="sub-img"><img src="<?php echo showImg(sanitize($viewData['pic2'])); ?>" alt="" class="js-switch-imgsub prev-subimg"></div>
                                            <div class="sub-img"><img src="<?php echo showImg(sanitize($viewData['pic3'])); ?>" alt="" class="js-switch-imgsub prev-subimg"></div>
                                            <div class="sub-img"><img src="<?php echo showImg(sanitize($viewData['pic4'])); ?>" alt="" class="js-switch-imgsub prev-subimg"></div>
                                        </div>
                                    </div>
                                    <div class="com-wrap">
                                        <h3 class="raf">comment -<span class="min" style="font-size:18px;">コメント</span></h3>
                                        <p class="comment min" style="font-size:20px;"><?php echo sanitize($viewData['comment']); ?></p>
                                    </div>
                                    <div class="word-wrap auto-resize">
                                        <ul>
                                            <?php foreach ($partnerDataCmt as $key => $val) : ?>
                                                <li>
                                                    <dl class="flex">
                                                        <dt><img src="<?php echo $val['pic']; ?>" alt=""></dt>
                                                        <dd><?php echo $val['comment']; ?></dd>
                                                    </dl>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>

                                    <form class="self-com min" action="" method="post">
                                            <textarea name="selfcom" id="" cols="30" rows="2" class="raf" placeholder="leave a message"></textarea>
                                        <div class="err-msg" style="top:5px;"><?php echo errmsg('selfcom'); ?> </div>
                                        <input type="submit" name="submit" class="raf" value="send">
                                    </form>

                    </div>



                </div>

            </div>
        </div>

       <?php require('footer.php'); ?>
    </body>