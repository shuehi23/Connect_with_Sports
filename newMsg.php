<?php
require('function.php');
debug('-----------------------新着メッセージ------------------------');

require('auth.php');

$dealUserId = ''; // 相手のユーザーID情報
$partnerUserInfo = ''; // 相手のユーザー情報
$myUserInfo = ''; // 自分のユーザー情報

$u_id = $_SESSION['user_id'];

$m_id = (!empty($_GET['m_id'])) ? $_GET['m_id'] : '';

// DBから連絡掲示板データを取得
$viewData = getMyMsgsAndBord($u_id);
debug('msg情報:' . print_r($viewData, true));
// 未ログインユーザーによるパラメータの操作があった場合トップページへ遷移する
if (empty($_SESSION['user_id'])) {
    error_log('エラー発生：指定ページに不正な値が入りました(未ログインユーザーです)');
    debug('$_SESSIONの中身：' . print_r($_SESSION, true));
    header("Location:index.php");
}

if(!empty($viewData)) {
    $dealUserId = array();
    $dealUserId[] = $viewData[0]['sender_id'];
    $dealUserId[] = $viewData[0]['receiver_id'];
    if(($key = array_search($_SESSION['user_id'], $dealUserId)) !== false) {
        unset($dealUserId[$key]);
    }
    $partnerUserId = array_shift($dealUserId);
    // 相手のユーザー情報を取得
    $partnerUserInfo = getUser($partnerUserId);
}
// if ($u_id === $viewData[0]['sender_id']) {
//     $dealUserId = $viewData[0]['receiver_id'];
//     $myUserId = $viewData[0]['sender_id'];
//     debug('相手の情報:' . print_r($dealUserId, true));
// } elseif ($u_id === $viewData[0]['receiver_id']) {
//     $dealUserId = $viewData[0]['sender_id'];
//     $myUserId = $viewData[0]['receiver_id'];
//     debug('相手の情報:' . print_r($dealUserId, true));
// }
// $partnerUserInfo = getUser($dealUserId);
// debug('相手の情報:' . print_r($partnerUserInfo, true));
// $myUserInfo = getUser($myUserId);
// debug('自分の情報:' . print_r($myUserInfo, true));

// if (empty($partnerUserInfo) || empty($myUserInfo)) :
//     error_log('エラー発生:相手のユーザー情報が取得できませんでした');
//     header("Location:mypage.php"); //マイページへ
//     exit();
// endif;

?>

<?php
$sitetitle = '新着メール';
require('head.php');
?>
</head>

<body id="mypage" style="position:relative;">
    <p id="js-show-msg" class="msg-success" style="display:none;"><?php echo getFlash('msg_success'); ?></p>
    <?php require('header.php'); ?>
    <div class="wrapper">
        <div class="mypage-img">
            <?php require('sheader.php'); ?>
            <div class="flex">
                <?php require('side.php'); ?>
                <div class="content-wrapper">
                    <div class="content-wrap">
                        <div class="posted con">
                            <div class="main-txt">
                                <h3 class="raf right">message - <span class="min" style="font-size:16px;vertical-align:middle;">メッセージ</span></h3>
                            </div>
                            <ul class="flex">
                                <?php foreach ($viewData as $key => $val) {
                                    if (!empty($val['msg'])) {
                                        $msg = array_shift($val['msg']);
                                ?>
                                        <li>
                                            <a href="msg.php?m_id=<?php echo sanitize($msg['bord_id']); ?>">
                                                <dl>
                                                    <dt class="photo"><img src="<?php echo showImg(sanitize($partnerUserInfo['pic'])); ?>" alt=""></dt>
                                                    <dd class="ptitle min"><?php echo sanitize($partnerUserInfo['username']); ?> </dd>
                                                    <dd class="comment min">メッセージ : <?php echo mb_substr(sanitize($msg['msg']), 0, 5); ?>...</dd>
                                                    <dd class="send min"><?php echo date('Y-m-d', strtotime(sanitize($msg['send_date']))); ?></dd>
                                                </dl>
                                            </a>
                                        </li>
                                    <?php } else { ?>
                                        <p class="min my-none" style="display:none;">メッセージはありません</p>
                                <?php }
                                }
                                ?>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <?php require('footer.php'); ?>
</body>
