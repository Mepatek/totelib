<?php
declare(strict_types=1);

function adminer_object()
{
    class ToteLibAdminer extends Adminer
    {
        function loginForm() {
            echo "<table cellspacing='0' class='layout'>\n";
            echo $this->loginFormField('driver', '<tr><th>' . lang('System') . '<td>', '<input type="text" name="auth[driver]" value="mssql" readonly="readonly">');
            echo $this->loginFormField('server', '<tr><th>' . lang('Server') . '<td>', '<input name="auth[server]" value="' . getenv('TOTE_SERVER') . '" title="hostname[:port]" placeholder="localhost" autocapitalize="off">' . "\n");
            echo $this->loginFormField('username', '<tr><th>' . lang('Username') . '<td>', '<input name="auth[username]" id="username" value="' . getenv('TOTE_USERNAME') . '" autocomplete="username" autocapitalize="off">' . script("focus(qs('#username')); qs('#username').form['auth[driver]'].onchange();"));
            echo $this->loginFormField('password', '<tr><th>' . lang('Password') . '<td>', '<input type="password" name="auth[password]" autocomplete="current-password" value="' . getenv('TOTE_PASSWORD') .'">' . "\n");
            echo $this->loginFormField('db', '<tr><th>' . lang('Database') . '<td>', '<input name="auth[db]" value="' . h($_GET["db"]) . '" autocapitalize="off">' . "\n");
            echo "</table>\n";
            echo "<p><input type='submit' value='" . lang('Login') . "'>\n";
            echo checkbox("auth[permanent]", 1, $_COOKIE["adminer_permanent"], lang('Permanent login')) . "\n";
        }
    }
    return new ToteLibAdminer;
}

include __DIR__ . '/adminer.php';
