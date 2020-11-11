<?php
//================================
// ログイン認証・自動ログアウト
//================================
// ログインしている場合
if(!empty($_SESSION['login_date'])){
    debug('ログイン済ユーザーです');
    if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
        debug('有効期限切れです');
        session_destroy();
        header("Location:login.php");
        exit();
    }else{
        debug('有効期限以内です');
        $_SESSION['login_date'] = time();

        if(basename($_SERVER['PHP_SELF']) === 'login.php'){
            debug('マイページへ遷移します');
            header("Location:mypage.php");
            exit();
        }
    }
}else{
    debug('未ログインユーザーです');
    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
        header("Location:login.php");
        exit();
    }
}
?>