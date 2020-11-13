<footer id="footer" class="min" style="width: 100%;">

  Copyright ©︎ <a href="#" style="color: white;">Connect with Sports</a>
</footer>
<script src="js/vendor/jquery-3.4.1.min.js"></script>
<script>
  $(function() {

    // フッターを最下部に固定
    var $footer = $('#footer');
    if (window.innerHeight > $footer.offset().top + $footer.innerHeight()) {
      $footer.offset({
        top: window.innerHeight - $footer.innerHeight(),
        top: -15 + 'px;'
      });
    }

    //サクセスメッセージ表示
    var $jsShowMsg = $('#js-show-msg');
    if ($jsShowMsg.text().replace(/\s+/g, '').length > 0) {
      $jsShowMsg.fadeIn(2000);
      setTimeout(function() {
        $jsShowMsg.fadeOut(2000);
      }, 3000);
    }

    //ライブプレビュー
    var $dropArea = $('.js-area-drop'),
      $fileInput = $('.input-file');
    $dropArea.on('dragover', function(e) {
      e.stopPropagation();
      e.preventDefault();
      $(this).css({
        border: '4px dashed #ccc'
      });
    });
    $dropArea.on('dragleave', function(e) {
      e.stopPropagation();
      e.preventDefault();
      $(this).css({
        border: 'none'
      });
    });
    $fileInput.on('change', function(e) {
      $(this).closest('.js-area-drop').css({
        border: 'none',
        backgroundColor: 'transparent'
      });
      var fileimg = this.files['0'],
        $img = $(this).siblings('.prev-img'),
        fileReader = new FileReader();


      fileReader.onload = function(event) {
        $img.attr('src', event.target.result).show();
      };

      fileReader.readAsDataURL(fileimg);
    });

    // カウンターテキスト
    var $countUp = $('#intro'),
      $countView = $('#js-count-view');
    $countUp.on('keyup', function(e) {
      $countView.html($(this).val().length);
    });

    //テキストエリアの高さを自動調節
    $(function() {
      function adjust() {
        var h = $(window).height(); //ウィンドウの高さ
        var h1 = $('#header').height(); //他要素の高さ
        $('#contents').css('height', h - h1); //可変部分の高さを適用
      }

      adjust();

      $(window).on('resize', function() {
        adjust();
      })
    });

    // ライブプレビュー
    var $like,
      likePhotoId;
    $like = $('.js-like') || null;
    likePhotoId = $like.data('photoid') || null;

    if (likePhotoId !== undefined && likePhotoId !== null) {
      $like.on('click', function() {
        var $this = $(this);
        $.ajax({
          type: "POST",
          url: "ajax.php",
          data: {
            photoId: likePhotoId
          }
          // ajax通信が成功した場合
        }).done(function(data) {
          console.log('success');
          $this.toggleClass('active');
          // ajax通信がつながらない場合
        }).fail(function(msg) {
          console.log('err');
        });
      });
    }

    //画像切替
    var $switchImgSubs = $('.js-switch-imgsub'),
      $switchImgMain = $('#js-switch-imgmain');
    $switchImgSubs.on('click', function(e) { // サブがクリックされたら、
      $switchImgMain.attr('src', $(this).attr('src')); // メインのDOMに対してSrc属性を変更している
    });

    //　フォロー
    var $follow,
      followId;
    $follow = $('.js-follow') || null;
    followId = $follow.data('followid') || null;

    if (followId !== undefined && followId !== null) {
      $follow.on('click', function() {
        var $this = $(this);
        $.ajax({
          type: "POST",
          url: "ajax.php",
          data: {
            followIdkey: followId
          }
        }).done(function(data) {
          console.log('success');
          $this.toggleClass('active');
        }).fail(function(msg) {
          console.log('err');
        });
      });
    }

    //退会時確認用アラート
    var jsShowAlert = $('.js-show-alert');
    jsShowAlert.on('click', function() {
      var flag = confirm('本当に退会してよろしいですか？');
      return flag;
    });

  });
</script>
