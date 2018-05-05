<?php
/**
 * @var $message
 */

isset($message) OR $message = '';
?>

<div class="login_box">

    <form action="/login" autocomplete="on" method="POST">

        <div class="login_message"><?=$message?></div>
        <input type="text" name="login" placeholder="Login" required> <br>
        <input type="password" name="password" placeholder="Password" required> <br>
        <label>
            <input name="remember" type="checkbox" value="remember-me"> Remember me
        </label>
        <button class="button" type="submit">Sign in</button>

    </form>

</div>
