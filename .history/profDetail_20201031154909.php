<?php 
require('function.php');
debug('----------------------プロフィール詳細ページ---------------------');
debugLogStart();

require('auth.php');

$h_id = (!empty($_GET['h_id'])) ? $_GET['h_id'] : '';

if(!empty($_POST['submit'])){
    debug('POST送信あり');
    if(empty($_SESSION['last_id'])){
        try{
            $dbh = dbConnect();
            $sql = 'INSERT INTO bord SET sender_id=?, receiver_id=?, create_date=?';
            $data = array($_SESSION['user_id'], $h_id, date('Y-m-d H:i:s'));
            $stmt = queryPost($dbh, $sql, $data);
            if($stmt){
                debug('messageへ遷移');
                header("Location:msg.php?h_id=" .$h_id);
                debug('$_SESSIONの中身：'.print_r($_SESSION,true));
                debug('$stmtの中身：'.print_r($stmt,true));
                exit;
            }
        } catch (Exception $e){
            error_log('エラー発生:' .$e->getMessage()); 
        }
    } else {
        header("Location:msg.php?m_id=".$_SESSION['last_id'].'&h_id='.$h_id);
        debug('$_SESSIONの中身：'.print_r($_SESSION,true));

    }
}

// DBから連絡掲示板データを取得
$viewData = getMyMsgsAndBord($u_id);
debug('msg情報:' . print_r($viewData, true));
// 未ログインユーザーによるパラメータの操作があった場合トップページへ遷移する
if (empty($_SESSION['user_id'])) {
   error_log('エラー発生：指定ページに不正な値が入りました(未ログインユーザーです)');
   debug('$_SESSIONの中身：' . print_r($_SESSION, true));
   header("Location:index.php");
}
if ($u_id === $viewData[0]['sender_id']) {
   $partnerUserId = $viewData[0]['receiver_id'];
   $myUserId = $viewData[0]['sender_id'];
   debug('相手の情報:' . print_r($partnerUserId, true));
} elseif ($u_id === $viewData[0]['receiver_id']) {
   $partnerUserId = $viewData[0]['sender_id'];
   $myUserId = $viewData[0]['receiver_id'];
   debug('相手の情報:' . print_r($partnerUserId, true));
}

$partnerUserInfo = getUser($partnerUserId);
debug('相手の情報:' . print_r($partnerUserInfo, true));
$myUserInfo = getUser($myUserId);
debug('自分の情報:' . print_r($myUserInfo, true));

if (empty($partnerUserInfo) || empty($myUserInfo)) :
   error_log('エラー発生:相手のユーザー情報が取得できませんでした');
   header("Location:mypage.php"); //マイページへ
   exit();
endif;

$getUser = getUserOne($h_id);
debug('$getUser中身:'.print_r($getUser,true));
?>
<?php
$sitetitle = 'プロフィール詳細';
require('head.php');
?>

<body id="profdetail">
    <?php require('header.php'); ?>
    <div class="wrapper">
        <div class="prof-detail-img">
            <?php require('sheader.php'); ?>
            <div class="wrapper-wrap">
                <div class="flex wrap">
                    <div>
                        <div class="prof-icon"><img src="<?php echo showImg($getUser['pic']); ?>" alt=""></div>
                    </div>

                    <div class="prof-wrap">
                        <div class="n-f flex">
                        <?php foreach ($viewData as $key => $val) {
                                    if (!empty($val['msg'])) {
                                        $msg = array_shift($val['msg']);
                                ?>
                            <p class="min" style="width:60%;font-size:30px;"><?php echo $getUser['username']; ?></p>
                            <div class="flex" style="width:100%;">
                                <form action="msg.php?m_id=<?php echo sanitize($msg['bord_id']);?>" method="post">
                                    <?php if($_SESSION['user_id'] === $h_id){ ?>
                                    <input type="submit" class="raf" value="message" name="submit" style="opacity:0;width:100;">
                                    <?php } else { ?>
                                    <input  type="submit" class="raf" value="message" name="submit" style="width:100%;padding: 0px 18px;">
                                    <?php } ?>
                                    <?php } ?>
                                </form>
                                <?php if($_SESSION['user_id'] === $h_id){ ?> 
                                <p style="opacity:0;"><span class="min js-follow <?php if(isFollow($_SESSION['user_id'], $h_id)) echo 'active'; ?>" data-followid=<?php echo $h_id; ?>>フォロー中</span></p>
                                <?php } else { ?>
                                <p><span class="min js-follow <?php if(isFollow($_SESSION['user_id'], $h_id)) echo 'active'; ?>" data-followid=<?php echo $h_id; ?>>フォロー</span></p>
                                <?php } ?>
                            </div>


                        </div>

                        <div class="flex detail-de-wrap">
                            <div class="detail-de">
                                <div class="job de-in flex">
                                    <p class="min pjpb">職業</p>
                                    <p class="min"><?php echo $getUser['job']; ?></p>
                                </div>
                                <div class="hoddy de-in flex">
                                    <p class="min phob">スポーツ</p>
                                    <p class="min"><?php echo $getUser['sports']; ?></p>
                                </div>
                                <div class="area de-in flex">
                                    <p class="min parea">居住エリア</p>
                                    <p class="min"><?php echo $getUser['a_name']; ?></p>
                                </div>
                            </div>
                            <div class="de-com">
                                <p class="comment"><?php echo $getUser['intro']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require('footer.php'); ?>
</body>