<div class="header">
    <div class="grid w960">
        <div class="row headerrow">
            <div class="c6 logo">

            </div>
            <div class="c6 nav">
                <ul>
                    <?php if(isUserLoggedIn()) { ?>
                        <a href="account.php"><li>Account</li></a>
                        <a href="logout.php"><li>Log Out</li></a>
                    <?php } else { ?>
                        <a href="register.php"><li class="signup">Sign Up</li></a>
                        <a href="login.php"><li>Login</li></a>
                    <?php } ?>
                </ul>
                <div class="cf"></div>
            </div>
            <div class="cf"></div>
        </div>
    </div>
</div>