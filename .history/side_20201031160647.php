<?php
$getUser = getUser($_SESSION['user_id']);
?>
<div class="side-wrap">
    <div>
        <div class="side-inner over">
            <div class="prof-img"><img src="<?php echo showProfImg($getUser['pic']); ?>" alt=""></div>
            <p class="prof-name min"><?php echo $getUser['username']; ?></p>
            <p><a href="profEdit.php" class="min" style="color:rgb(0 230 255);font-weight:700;">プロフィール編集</a></p>
        </div>
        <div class="side-inner bottom">
            <ul>
                <li class=""><a href="passEdit.php" class="min">パスワード変更</a></li>
                <li class=""><a href="newMsg.php" class="min">メッセージ</a></li>
                <li class=""><a href="" class="min">トッピックス</a></li>
                <li class=""><a href="" class="min">設定</a></li>
                <li class=""><a href="logout.php" class="min">ログアウト</a></li>
                <p><a href="post.php" class="min">投稿する</a></p>
            </ul>
        </div>
    </div>
</div>
