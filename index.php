<?php

require_once "login-api/auth-api.php";

if (isset($_SESSION['last']) and !(isset($_GET['page']))) {
    $page = $_SESSION['last'];
    unset($_SESSION['last']);
    header("Location: index.php?page=$page");
    exit;
}

function val($v) {
    if (isset($v)) {
        return $v;
    } else {
        return "";
    }
}
?>
<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">

    <title><?php

        $page = "home";

        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        }
        echo $page;

        ?></title>
    <meta name="description" content="">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="styles/styles.css">


    <meta name="author" content="">



</head>

<body>

<?php if (isset($_SESSION['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['msg'];unset($_SESSION['msg'])?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif ?>
<nav class="navbar navbar-expand-lg navbar-light bg-dark">
    <a class="navbar-brand" href="#" style="color:white">Showtechs</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link <?php if ($page == "home"): ?>active<?php endif ?>"
                   href="index.php?page=home">Home</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Submit Request</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="index.php?page=request_show">Request Show</a>
                    <a class="dropdown-item" href="index.php?page=request_rental">Request Speaker Rental</a>
                </div>
            </li>

            <?php if (user::get_this_user()->is_logged_in()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=view_shows">View Your Requests</a>
                </li>
            <?php endif ?>

            <?php if (user::get_this_user()->has_rank("chair")): ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=edit_users">Manage Users</a>
                </li>
            <?php endif ?>
        </ul>
        <ul class="nav nav-pills ml-auto">
            <?php if (!user::get_this_user()->is_logged_in()): ?>
                <li class="nav-item">
                    <a data-toggle="modal" data-target="#login_page" href="#" class="nav-link">Login</a>
                </li>
                <li class="nav-item">
                    <a data-toggle="modal" data-target="#create_account_page" href="#" class="nav-link">Create
                        Account</a>
                </li>
            <?php endif ?>
            <?php if (user::get_this_user()->is_logged_in()): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                       aria-haspopup="true"
                       aria-expanded="false">
                        <?php
                        echo user::get_this_user()->get_val("firstname");
                        ?>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="index.php?page=user_info&id=<?php echo user::get_this_user()->get_val("id");?>">Edit Profile</a>
                        <form method="post" action="auth-confirm.php">
                            <input type="hidden" name="logout">
                            <input type="submit" value="logout" class="dropdown-item">
                        </form>
                    </div>
                </li>
            <?php endif ?>
        </ul>
    </div>
</nav>

<div id="page__" >
    <?php

    /**
     * The current page is included from a php file in the PAGES folder
     */

    if (@include 'pages/'.$page . '.php') {
        $_SESSION['last'] = $page;
        error_log($_SESSION['last']);
    } else {
        if ($_SESSION['last'] == $page) {
            include 'pages/home.php';
            $_SESSION['last'] = "home";
        } else {

            include 'pages/404.php';
        }
    }


    ?>

</div>
<div class="modal fade" id="login_page" tabindex="-1" role="dialog" aria-labelledby="login" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content ">
            <div class="modal-body">
                <h1>Login</h1>
                <?php if (isset($_GET['login_succeed']) && $_GET['login_succeed']==0): ?>
                    <i>Incorrect Email or Password</i>
                <?php endif ?>
                <form method="post" action="auth-confirm.php" >
                    <input type="hidden" name="login">
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <input type="email" required name="email" class="form-control" id="inputEmail3" placeholder="Email">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputPassword3" class="col-sm-2 col-form-label">Password</label>
                        <div class="col-sm-10">
                            <input type="password" name="password" required class="form-control" id="inputPassword3" placeholder="Password">
                        </div>
                    </div>
                    <div class="form-group row">
                        <a class="nav-link" data-toggle="modal" data-dismiss="modal" data-target="#reset_password" href="#">Reset Password</a>
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Sign In</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="create_account_page" tabindex="-1" role="dialog" aria-labelledby="create_account" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content ">
            <div class="modal-body">
                <h1>Create Account</h1>
                <?php if (isset($_GET['create_account_succeed']) && $_GET['create_account_succeed']==0): ?>
                    <p><?php echo $_GET['error'] ?></p>
                <?php endif ?>
                <form  method="post" action="auth-confirm.php">
                    <input type="hidden" name="create_account">
                    <div class="form-group row">
                        <label for="firstname" class="col-sm-2 col-form-label">First Name</label>
                        <div class="col-sm-10">
                            <input type="text" required name="firstname" class="form-control" id="inputEmail3" placeholder="Joe">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="lastname" class="col-sm-2 col-form-label">Last Name</label>
                        <div class="col-sm-10">
                            <input type="text" required name="lastname" class="form-control" id="inputEmail3" placeholder="Smith">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <input type="email" name="email" required class="form-control" id="inputEmail3" placeholder="Email">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputphone" class="col-sm-2 col-form-label">Phone Number</label>
                        <div class="col-sm-10">
                            <input type="tel" name="phone_number" required class="form-control" id="inputEmail3" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">rin</label>
                        <div class="col-sm-10">
                            <input type="text" required name="rin" class="form-control" id="inputEmail3" placeholder="669999999">
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
                        <div class="col-sm-10">
                            <input type="password" name="password" class="form-control" id="inputPassword3">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-2 col-form-label">Confirm Password</label>
                        <div class="col-sm-10">
                            <input type="password" name="password_confirm" class="form-control" id="inputPassword3" >
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Create Account</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reset_password" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="text-center">
                                        <h3><i class="fa fa-lock fa-4x"></i></h3>
                                        <h2 class="text-center">Forgot Password?</h2>
                                        <p>You can reset your password here.</p>
                                        <div class="panel-body">

                                            <form id="forgot-form" action="index.php?page=reset_password" role="form" class="form" method="post">

                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope color-blue"></i></span>
                                                        <input id="email" name="email" placeholder="email address" class="form-control"  type="email">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <input name="recover-submit" class="btn btn-lg btn-primary btn-block" value="Reset Password" type="submit">
                                                </div>

                                                <input type="hidden" class="hide" name="token" id="token" value="">
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script
        src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<?php if (user::get_this_user()->has_rank("chair")): ?>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <script src="scripts/admin.js"></script>
<?php endif ?>
<script src="scripts/main.js"></script>
<?php if (isset($_GET['login_succeed']) && $_GET['login_succeed'] == 0): ?>
    <script>
        $("#login_page").modal('toggle');
    </script>
<?php endif ?>
<?php if (isset($_GET['create_account_succeed']) && $_GET['create_account_succeed'] == 0): ?>
    <script>
        $("#create_account_page").modal('toggle');
    </script>
<?php endif ?>


</body>
</html>