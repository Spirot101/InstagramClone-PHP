<?php
require_once "core/init.php";
require_once "shared/login_handlers.php";
require "shared/header.php";
?>
<section class="pageContainer">
    <main class="row">
        <div class="col-1">
            <div class="heroImg"></div>
        </div>
        <article class="col-2">
            <?php
            if (!empty($form_errors)) {
                echo show_errors($form_errors);
            }
            ?>
            <form action="<?= h($_SERVER["PHP_SELF"]); ?>" method="POST" class="form">
                <div class="siteLogoContainer">
                    <img src="public/assets/images/logo/instagram.png" alt="Instagram Logo">
                </div>
                <input type="text" placeholder="Email or Username" class="form--input" name="email_username" value="<?= escape(Input::get('email_username')); ?>">
                <div class="passwordContainer">
                    <input type="password" placeholder="Password" class="form--input" name="password" id="password">
                    <span class="show_hide_text cursor-pointer" id="show_hide_password">Show</span>
                </div>
                <button class="button cursor-pointer" type="submit" name="submitButton">Log In</button>
                <span class="separator">Or</span>
                <a href="#" class="password_reset">Forgot Password</a>
            </form>
            <footer class="form--footer">
                Don't have an account? <a href="register">Sign up</a>
            </footer>
        </article>
    </main>
</section>
<script src="<?= url_for('public/js/common.js'); ?>"></script>
</body>

</html>