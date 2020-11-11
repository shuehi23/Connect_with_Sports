<?php
require('function.php');
debug('--------------------------投稿一覧----------------------------');

$nowPage = (!empty($_GET['p'])) ? $_GET['p'] : 1;
// カテゴリー
$cate = (!empty($_GET['category_id'])) ? $_GET['category_id'] : '';
// 表示順
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';
// キーワード

if (!is_int((int)$nowPage)) {
    header("Location:mypage.php");
    error_log('エラー発生:指定ページに不正な値が入りました');
}

// 表示件数
$list = 20;
// 現在の表示レコード先頭を算出
$nowMin = (($nowPage - 1) * $list);
// DBから商品データを取得
$dbPostData = getPostList($nowMin, $cate, $sort);
debug('現在のページ:' . $nowPage);
// カテゴリー取得
$dbCate = getCate();

?>

<?php
$sitetitle = '投稿一覧';
require('head.php');
?>

<body id="postlist">
    <?php require('header.php'); ?>
    <div class="wrapper">
        <div class="post-list-img">
            <?php require('sheader.php'); ?>
            <div class="post-list-wrap">
                <div class="search-wrap">

                    <form action="" method="get">
                        <div>
                            <!-- キーワード検索 -->
                            <label for="" class="min" style="font-size: 18px;">
                                <p>キーワード検索</p>
                                <input type="text" name="keyword" placeholder="search" value="<?php if (isset($_GET['keyword'])) echo $_GET['keyword']; ?>">
                            </label>
                            <div class="err-msg"></div>

                            <!-- カテゴリー -->
                            <label for="" class="min" style="font-size:18px;">
                                <p>カテゴリー</p>
                                <select name="category_id" id="" class="select-item" style="margin-right:10px">
                                    <option value="0" <?php if (getFormData('category_id', true) == false) echo 'selected'; ?>>選択してください</option>
                                    <?php foreach ($dbCate as $key => $val) : ?>
                                        <option value="<?php echo $val['id']; ?>" <?php if (getFormData('category_id', true) == $val['id']) echo 'selected'; ?>><?php echo $val['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <div class="err-msg"></div>

                            <!--表示順-->
                            <label class="min" style="font-size:18px;">
                                <p>表示順</p>
                                <select name="sort" id="" class="select-item">
                                    <option value="0" <?php if (getFormData('sort', true) == 0) echo 'selected'; ?>>選択してください</option>
                                    <option value="1" <?php if (getFormData('sort', true) == 1) echo 'selected'; ?>>新しい順</option>
                                    <option value="2" <?php if (getFormData('sort', true) == 2) echo 'selected'; ?>>古い順</option>
                                </select>
                            </label>
                            <div class="err-msg"></div>
                        </div>


                        <input type="submit" class="raf" name="submit" value="search">
                    </form>
                </div>

                <!-- 投稿写真一覧 -->
                <div class="photo-list-wrap">
                    <div class="discover-wrap flex min">
                        <div class="dis">
                            <?php if(!empty($dbPostData['total'])) { ?>
                            <p><span class="tatalre"><?php echo sanitize($dbPostData['total']); ?></span>件の投稿が見つかりました</p>
                                    <?php } else { ?>
                                    投稿はありません
                                <?php } ?>
                        </div>
                        <div class="dis">
                            <p><span><?php echo (!empty($dbPostData['data'])) ? $nowMin + 1 : 0; ?></span> - <span><?php echo $nowMin + $list; ?></span> / <span><?php echo sanitize($dbPostData['total']); ?></span>件中</p>
                        </div>
                    </div>
                    <ul class="flex photo-ul">
                        <?php foreach ($dbPostData['data'] as $key => $val) : ?>
                            <li>
                                <a href="postDetail.php<?php echo (!empty(appendget())) ? appendget() . '&p_id=' . $val['id'] . '&h_id=' . $val['posted_id'] : '?p_id=' . $val['id'] . '&h_id=' . $val['posted_id']; ?>">
                                    <dl>
                                        <dt class="photo"><img src="<?php echo showImg(sanitize($val['pic1'])); ?>" alt=""></dt>
                                        <dd class="ptitle min"><?php echo sanitize($val['title']); ?></dd>
                                        <dd class="comment min"><?php echo sanitize(mb_substr($val['comment'], 0, 10)); ?>...</dd>
                                    </dl>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- ページネーション -->
                    <div class="pagenation-wrap">
                        <ul class="flex">
                            <?php
                            $pageColNum = 5;
                            $totalPageNum = $dbPostData['total_page'];
                            if ($nowPage == $totalPageNum && $totalPageNum >= $pageColNum) {
                                $minPageNum = $nowPage - 4;
                                $maxPageNum = $nowPage;
                            } else if ($nowPage == ($totalPageNum - 1) && $totalPageNum >= $pageColNum) {
                                $minPageNum = $nowPage - 3;
                                $maxPageNum = $nowPage + 1;
                            } else if ($nowPage == 2 && $totalPageNum >= $pageColNum) {
                                $minPageNum = $nowPage - 1;
                                $maxPageNum = $nowPage + 3;
                            } else if ($nowPage == 1 && $totalPageNum >= $pageColNum) {
                                $minPageNum = $nowPage;
                                $maxPageNum = $nowPage + 5;
                            } else if ($totalPageNum < $pageColNum) {
                                $minPageNum = 1;
                                $maxPageNum = $totalPageNum;
                            } else {
                                $minPageNum = $nowPage - 2;
                                $maxPageNum = $nowPage + 2;
                            }
                            ?>
                            <?php if ($nowPage != 1) : ?>
                                <li class="list-item"><a href="postlist.php<?php echo (!empty(appendGet())) ? appendGet() . '&p=1' : '?p=1'; ?>" class="raf">&lt;</a></li>
                            <?php endif; ?>
                            <?php for ($i = $minPageNum; $i <= $maxPageNum; $i++) : ?>
                                <li class="raf list-item <?php if ($nowPage == $i) echo 'active'; ?>"><a href="<?php echo (!empty(appendget())) ? appendget() . '&p=' . $i : '?p=' . $i; ?>"><?php echo $i; ?></a></li>
                            <?php endfor; ?>
                            <?php if ($nowPage != $maxPageNum) : ?>
                                <li class="raf list-item"><a href="postlist.php<?php echo (!empty(appendget())) ? appendget() . '&p=' . $maxPageNum : '?p=' . $maxPageNum; ?>">&gt;</a></li>
                            <?php endif; ?>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require('footer.php'); ?>