<?php 
require('function.php');
debug('-----------------------マイページ---------------------------');
debugLogStart();

require('auth.php');

// 自分情報取得
$myInfo = myData($_SESSION['user_id']);
debug('記事投稿情報:'.print_r($myInfo,true));

// お気に入り情報取得
$myLike = myLike($_SESSION['user_id']);
debug('お気に入り情報:'.print_r($myLike,true));

// フォロー情報取得
$myFollow = myFollow($_SESSION['user_id']);
debug('フォローしている情報:'.print_r($myFollow,true));
?>

<?php
$sitetitle = 'マイページ';
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
                            <h3 class="raf right">posted article - <span class="min" style="font-size:16px;vertical-align:middle;">投稿記事</span></h3>
                        </div>
                        <ul class="flex">
                            <?php if(!empty($myInfo[0]['id'])){
                                foreach($myInfo as $key => $val){ ?>
                            <li>
                                <a href="post.php<?php echo (!empty(appendGet())) ? appendGet(). '&a_id='.$val['id'] : '?a_id='.$val['id']; ?>">
                                    <dl>
                                        <dt class="article"><img src="<?php echo showImg(sanitize($val['pic1'])); ?>" alt=""></dt>
                                        <dd class="ptitle min"><?php echo sanitize($val['title']); ?></dd>
                                        <dd class="comment min"><?php echo sanitize(mb_substr($val['comment'], 0, 30)); ?>...</dd>
                                    </dl>
                                </a>
                            </li>
                                <?php } 
                                }else{ ?>
                            <p class="min my-none">投稿がありません</p>
                                <?php  } ?>
                        </ul>
                    </div>

                    <div class="favo con">
                        <div class="main-txt">
                            <h3 class="raf right">favorite - <span class="min" style="font-size:16px;vertical-align:middle;">お気に入り写真</span></h3>
                        </div>
                        <ul class="flex">
                            <?php if(!empty($myLike[0]['user_id'])){
                                foreach($myLike as $key => $val){ ?>
                            <li>
                                <a href="postDetail.php<?php echo '?h_id='.$val['posted_id'].'&a_id='.$val['id']; ?>">
                                    <dl>
                                        <dt class="article"><img src="<?php echo showImg(sanitize($val['pic1'])); ?>" alt=""></dt>
                                        <dd class="ptitle min"><?php echo sanitize($val['title']); ?></dd>
                                        <dd class="comment min"><?php echo sanitize(mb_substr($val['comment'], 0, 30)); ?>...</dd>
                                    </dl>
                                </a>
                            </li>
                                <?php } 
                            } else { ?>
                            <p class="min my-none">お気に入り情報はありません</p>
                             <?php } ?>
                        </ul>
                    </div>

                    <div class="follow con">
                        <div class="main-txt">
                            <h3 class="raf right">follows - <span class="min" style="font-size:16px;vertical-align:middle;">フォローしたユーザー</span></h3>
                        </div>
                        <ul class="flex">
                            <?php if(!empty($myFollow[0]['user_id'])){
                                foreach($myFollow as $key => $val){ ?>
                            <li>
                                <a href="profdetail.php<?php echo '?h_id='.$val['opponent_id']; ?>">
                                    <dl>
                                        <dt class="article"><img src="<?php echo showImg(sanitize($val['pic'])); ?>" alt=""></dt>
                                        <dd class="ptitle min"><?php echo sanitize($val['username']); ?></dd>
                                    </dl>
                                </a>
                            </li>
                                <?php }
                                } else { ?>
                                <p class="min my-none">フォローしているユーザーはいません</p>
                                <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>  
        </div>
    </div>
</div>
<?php require('footer.php'); ?>
</body>