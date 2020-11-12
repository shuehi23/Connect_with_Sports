<?php
require('function.php');
debug('--------------------------投稿ページ----------------------------');
debugLogStart();

require('auth.php');

$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
debug('GETパラメータ:'.print_r($p_id,true));
// DBから投稿情報を取得
$dbData = (!empty($p_id)) ? getPost($_SESSION['user_id'], $p_id) : '';
// 新規投稿か編集か判断
$edit_flg = (empty($dbData)) ? false : true;
// カテゴリー
$dbCategory = getCate();

debug('投稿ID:'.$p_id);
debug('DBphoto情報:'.print_r($dbData,true));
debug('カテゴリー:'.print_r($dbCategory,true));

// getパラメータはあるが改竄されている場合マイページへ
if(!empty($_GET) && empty($dbData)){
    debug('GETパラメータのIDが違います');
    header("Location:mypage.php");
    exit();
}

if(!empty($_POST)){
    debug('POST情報:'.print_r($_POST,true));
    debug('FILES情報:'.print_r($_FILES,true));

    $title = $_POST['title'];
    $place = $_POST['place'];
    $key1 = $_POST['key1'];
    $key2 = $_POST['key2'];
    $key3 = $_POST['key3'];
    $cate = $_POST['category_id'];
    $comment = $_POST['comment'];
    $pic1 = (!empty($_FILES['pic1']['name'])) ? uploadImg($_FILES['pic1'],'pic1') : '';
    $pic1 = (empty($pic1) && !empty($dbData['pic1'])) ? $dbData['pic1'] : $pic1;
    $pic2 = (!empty($_FILES['pic2']['name'])) ? uploadImg($_FILES['pic2'],'pic2') : '';
    $pic2 = (empty($pic2) && !empty($dbData['pic2'])) ? $dbData['pic2'] : $pic2;
    $pic3 = (!empty($_FILES['pic3']['name'])) ? uploadImg($_FILES['pic3'],'pic3') : '';
    $pic3 = (empty($pic3) && !empty($dbData['pic3'])) ? $dbData['pic3'] : $pic3;
    $pic4 = (!empty($_FILES['pic4']['name'])) ? uploadImg($_FILES['pic4'],'pic4') : '';
    $pic4 = (empty($pic4) && !empty($dbData['pic4'])) ? $dbData['pic4'] : $pic4;

    if(empty($dbData)){
        validRequired($title, 'title');
        maxLen($title, 'title');
        maxLen($place, 'place');
        maxLen($comment, 'comment');
        validselect($cate, 'category_id');
    }else{
        if($dbData['title'] !== $title){
            validRequired($title, 'title');
            maxLen($title, 'title');
        }
        if($dbData['place'] !== $place){
            maxLen($place, 'place');
        }
        if($dbData['comment'] !== $comment){
            maxLen($comment, 'comment');
        }
        if($dbData['category_id'] !== $cate){
            validSelect($cate, 'category_id');
        }
    }

    if (empty($err_msg)) {
        debug('バリデーションOKです');

        try {
            $dbh = dbConnect();
            if ($edit_flg) {
                debug('DB更新です');
                $sql = 'UPDATE photo SET title=?, place=?, key1=?, key2=?, key3=?, category_id=?, comment=?, pic1=?, pic2=?, pic3=?, pic4=? WHERE id=? AND posted_id=?';
                $data = array($title, $place, $key1, $key2, $key3, $cate, $comment, $pic1, $pic2, $pic3, $pic4, $p_id, $_SESSION['user_id']);
            } else {
                debug('新規登録です');
                $sql = 'INSERT INTO photo SET title=?, place=?, key1=?, key2=?, key3=?, category_id=?, comment=?, pic1=?, pic2=?, pic3=?, pic4=?, create_date=?, posted_id=?';
                $data = array($title, $place, $key1, $key2, $key3, $cate, $comment, $pic1, $pic2, $pic3, $pic4, date('Y-m-d H:i:s'), $_SESSION['user_id']);
            }
            $stmt = queryPost($dbh, $sql, $data);
            if($stmt){
                $_SESSION['msg_success'] = SUC06;
                header("Location:mypage.php");
                exit();
            }

        } catch (Exception $e) {
            error_log('エラー発生:' .$e->getMessage());
            $err_msg['common'] = MSG08;
        }
    }

}


?>
<?php
$sitetitle = '投稿';
require('head.php');
?>
<body id="mypage" class="post">
    <?php require('header.php'); ?>
    <div class="wrapper">
        <div class="post-img">
            <?php require('sheader.php'); ?>
            <div class="flex">
            <?php require('side.php'); ?>
            <div class="content-wrapper">
                <div class="post-wrapper">
                    <form action="" method="post" enctype="multipart/form-data">
                         <label for="" class="raf" style="font-size:24px;">title - <span class="min" style="font-size:16px;vertical-align:middle;">タイトル</span>
                             <input type="text" name="title" value="<?php echo  getFormData('title'); ?>"> 
                         </label>
                         <div class="err-msg"><?php echo errmsg('title'); ?></div>
                         
                         <label for="" class="raf" style="font-size:24px;">place - <span class="min" style="font-size:16px;vertical-align:middle;">場所</span>
                            <input type="text" name="place" value="<?php echo getFormData('place'); ?>">
                         </label>
                         <div class="err-msg"><?php echo errmsg('place'); ?></div>

                         <label for="" class="raf key" style="font-size:24px;">keywords - <span class="min" style="font-size:16px;vertical-align:middle;">キーワード</span><br>
                        <div class="input-area">
                             <input class="key" type="text" name="key1" value="<?php echo getFormData('key1'); ?>">
                             <input class="key" type="text" name="key2" value="<?php echo getFormData('key2'); ?>">
                             <input class="key" type="text" name="key3" value="<?php echo getFormData('key3'); ?>">
                             </div> 
                         </label>
                        <div class="err-msg"></div>

                        <label for="" class="raf" style="font-size:24px;display:inline-block;margin-bottom:20px;">category - <span class="min" style="font-size:16px;vertical-align:middle;">カテゴリー</span><br>
                             <select class="min" name="category_id" id="cate" style="font-size:14px;margin-top:5px;">
                                 <option value="0" <?php if(getFormData('category_id') == 0) echo 'selected'; ?>>選択してください</option>
                                 <?php foreach($dbCategory as $key => $val): ?>
                                 <option value="<?php echo $val['id']; ?>" <?php if(getFormData('category_id') === $val['id']) echo 'selected'; ?>><?php echo $val['name']; ?></option>
                                 <?php endforeach; ?>
                            </select>
                        </label>
                        <div class="err-msg" style="text-align:left;bottom: 15px;left:20px;"><?php echo errmsg('category_id'); ?></div>

                        <label for="" class="raf" style="font-size:24px;">comment - <span class="min" style="font-size:16px;vertical-align:middle;">コメント</span>
                             <textarea name="comment" id="intro" cols="20" rows="5"><?php echo getFormData('comment'); ?></textarea>
                        </label>
                        <div class="err-msg"><?php echo errmsg('comment'); ?></div>

                        <div class="flex" style="justify-content:space-between;">
                           <div class="flex culom">
                               <label for="" class="raf js-area-drop" style="font-size:24px;">
                                   <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                   <input type="file" name="pic1" class="input-file">
                                   <img src="<?php echo getFormData('pic1'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic1'))) echo 'display:none;'; ?>">
                                   drag & drop
                               </label>
                               <div class="err-msg"><?php echo errmsg('pic1'); ?></div>
                           </div>
                           <div class="flex culom">
                               <label for="" class="raf js-area-drop" style="font-size: 24px;">
                                   <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                   <input type="file" name="pic2" class="input-file">
                                   <img src="<?php echo getFormData('pic2'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic2'))) echo 'display:none;'; ?>">
                                   drag & drop
                               </label>
                               <div class="err-msg"><?php echo errmsg('pic2'); ?></div>
                             </div>

                        </div>
                        <div class="flex" style="justify-content: space-between;margin-bottom:40px;">
                           <div class="flex culom">
                               <label for="" class="raf js-area-drop" style="font-size: 24px;">
                                   <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                   <input type="file" name="pic3" class="input-file">
                                   <img src="<?php echo getFormData('pic3'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic3'))) echo 'display:none;'; ?>">
                                   drag & drop
                               </label>
                               <div class="err-msg"><?php echo errmsg('pic3'); ?></div>
                           </div>
                           <div class="flex culom">
                               <label for="" class="raf js-area-drop" style="font-size: 24px;">
                                   <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                   <input type="file" name="pic4" class="input-file">
                                   <img src="<?php echo getFormData('pic4'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic4'))) echo 'display:none;'; ?>">
                                   drag & drop
                               </label>
                               <div class="err-msg"><?php echo errmsg('pic4'); ?></div>
                           </div>

                        </div>

                        <div class="flex" style="display: block;">
                        <?php if(!empty($edit_flg)){ ?>
                        <a class="delete" href="deletePost.php<?php echo '?p_id='.$p_id; ?>"><i class="far fa-trash-alt"></i></a> 
                        <?php } ?>
                        <p  class="btn" style="text-align:center;"><input class="raf" type="submit" value="<?php echo(empty($edit_flg)) ? 'post' : 'change'; ?>" name="submit"></p>
                        </div>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>

    <?php require('footer.php'); ?>
    
</body>