<p>Loading ...</p>

<?php

function verify_reset() {
    $post = $_POST;
    $user = user::get_user_by("email",$post['email']);
    if (!$user) {
        echo "email not found";

    }
    if ($user->verify_password_reset_code($post['code'])) {
        if (!isset($post['confirm_password']) || !isset($post['password'])) {
            echo "password is required";
            return;
        }else if ($post['password'] != $post['confirm_password']) {
            echo "Passwords do not match";
        }
        $user->set_password($post['password']);
        $_SESSION['msg'] = "Success";
        echo "<script>document.location.href = 'index.php?page=home';</script>";
    } else {
        echo "Code did not match";
    }
}

function forgot_password() {
    $post = $_POST;
    $email = $post['email'];

    $user = user::get_user_by("email",$email);
    if (!$user) {
        echo "Email not found";
    }
    else {
        $code = $user->generate_password_reset();
        require_once 'testemail/email-inc.php';
        send_mail($user->get_val("email"),"Password Reset","Click here to reset your password<br><a href='https://waddellryan.com/index.php?page=reset_password&code=$code&email=$email'>https://waddellryan.com/index.php?page=reset_password&code=$code&email=$email</a>");
        echo "Email Sent";
    }
}

if (isset($_POST['password'])) {
    verify_reset();
} else if (!isset($_GET['email'])) {
    forgot_password();
}

?>


<form id="reset_password_form" method="post">
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-6 mb-3">
                <div class="mb-2"><b>Change Password</b> The temp. code expires in 5 minutes. Any new codes will overwrite old ones</div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label>Email</label>
                            <input class="form-control" type="text" name="email" placeholder="email@gmail.com" value="<?php

                            if (isset($_GET['email'])) {
                                echo $_GET['email'];
                            }

                            ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label>Temp. Code</label>
                            <input class="form-control" type="text" name="code" placeholder="999999" value="<?php

                            if (isset($_GET['code'])) {
                                echo $_GET['code'];
                            }

                            ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label>New Password</label>
                            <input class="form-control" type="password" name="password" placeholder="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label>Confirm <span class="d-none d-xl-inline">Password</span></label>
                            <input class="form-control"  type="password" name="confirm_password" placeholder=""></div>
                    </div>

                </div>
                <input type="submit" class="btn btn-primary" value="Update">
            </div>
        </div>
    </div>
</form>