<?php
require('function.php');
debug('-----------------------メッセージ-------------------------');
debugLogStart();

require('auth.php');

$h_id = (!empty($_GET['h_id'])) ? $_GET['h_id'] : '';
$m_id = (!empty($_GET['m_id'])) ? $_GET['m_id'] : '';

$viewData = getMsgAndBord($m_id);
debug('取得した情報:' . print_r($viewData, true));


if (!empty($_POST)) {
    debug('POST送信があります');
    $msg = isset($_POST['msg']);
}

// 相手のユーザID
debug('相手のID:' . print_r($h_id, true));
$receiverInfo = getUser($h_id);
debug('相手の情報:' . print_r($receiverInfo, true));

// 自分のユーザーID
$myInfo = getUser($_SESSION['user_id']);
debug('自分のID:' . print_r($myInfo, true));

//　自分の情報が取れたかチェック
if (empty($myInfo) || empty($h_id)) {
    error_log('指定したページに不正な値が入りました');
}

if (!empty($_POST)) {
    debug('POST送信があります');
    $msg = (isset($_POST['msg'])) ? $_POST['msg'] : '';
    maxLen($msg, 'msg');
    validRequired($msg, 'msg');
    if (empty($err_msg)) {
        debug('バリデーションOKです');

        try {
            $dbh = dbConnect();
            $sql = 'INSERT INTO message SET bord_id=?, msg=?, to_user=?, from_user=?, send_date=?, create_date=?';
            $data = array($m_id, $msg, $h_id, $_SESSION['user_id'], date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
            $stmt = queryPost($dbh, $sql, $data);
            if ($stmt) {
                debug('送信完了');
                //自画面に遷移
                debug('自分へ遷移');
                header("location:" . $_SERVER['PHP_SELF'] . '?m_id=' . $m_id . '&p_id=' . $p_id . '&h_id=' . $h_id);
            }
        } catch (Exception $e) {
            error_log('エラー発生:' . $e->getMessage());
        }
    }
}

?>
<?php
$sitetitle = 'メッセージ';
require('head.php');
?>

<body id="msg">
    <?php require('header.php'); ?>
    <div class="wrapper">
        <?php require('sheader.php'); ?>
        <div class="msg-img-wrap">
            <div class="msg-wrap">
                <ul>
                    <?php if (!empty($viewData[0]['m_id'])) {
                        foreach ($viewData as $key => $val) {
                            if (!empty($val['from_user']) && $val['from_user'] == $h_id) { ?>
                                <li class="left-msg msg">
                                    <dl class="flex">
                                        <dt><img src="<?php echo showProfImg(sanitize($receiverInfo['pic'])); ?>" alt="" class="avater"></dt>
                                        <dd>
                                            <p><?php echo sanitize($val['msg']); ?></p>
                                        </dd>
                                    </dl>
                                    <div class="send"><?php echo sanitize($val['send_date']); ?></div>
                                </li>
                            <?php
                            } else { ?>
                                <li class="right-msg msg">
                                    <dl class="flex" style="flex-flow:row-reverse;">
                                        <dt><img src="<?php echo showProfImg(sanitize($myInfo['pic'])); ?>" alt=""></dt>
                                        <dd>
                                            <p><?php echo sanitize($val['msg']); ?></p>
                                        </dd>
                                    </dl>
                                    <div class="send"><?php echo sanitize($val['send_date']); ?></div>
                                </li>
                        <?php
                            }
                        } ?>

                    <?php } ?>

                </ul>
            </div>
            <div class="txt-wrap">
                <form action="" method="post" class="flex txt-form">
                    <textarea name="msg" id="" cols="30" rows="10" class="raf" placeholder="message" style="text-align:center;"></textarea>
                    <input type="submit" name="submit" class="far" value="&#xf1d8;" placeholder="message">
                </form>
            </div>

        </div>
    </div>

</body>
