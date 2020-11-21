<header>
    <div class="header-inner sub">
        <div class="inner">
            <nav class="global-nav">
                <ul class="flex">
                    <li><a href="#" class="raf">Contact</a></li>
                    <li><a href="#" class="raf">About</a></li>
                    <li><a href="#" class="raf">Concept</a></li>
                </ul>
            </nav>
        </div>
        <div class="inner">
            <div class="main-logo">
                <h1><a href="top.php" class="raf">Special Fan Club</a></h1>
            </div>
        </div>
        <div class="inner">
            <nav class="rig-nav">
                <ul class="flex">
                    <?php if (empty($_SESSION['user_id'])) { ?>
                        <li><a href="login.php" class="raf">Login</a></li>
                        <li><a href="signup.php" class="raf">Sign up</a></li>
                    <?php } else { ?>
                        <li><a href="logout.php" class="raf">Logout</a></li>
                        <li><a href="mypage.php" class="raf">My page</a></li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </div>
</header>
