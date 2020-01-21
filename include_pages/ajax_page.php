<?php
include_once "general_functions.php";

include_once "../login-api/auth-api.php";

if (isset($_POST['method'])) {
    switch ($_POST['method']) {
        case "edit_module":
            if (!user::get_this_user()->has_rank("chair")) {
                exit;
            }
            if ($_POST['mod_id'] == '-1') {
                add_module($_POST['page_'],$_POST['mod_content']);
            } else {
                edit_module($_POST['page_'],$_POST['mod_id'],$_POST['mod_content']);
            }

            break;
        case "delete_module":
            if (!user::get_this_user()->has_rank("chair")) {
                exit;
            }
            remove_module($_POST['mod_id']);
            break;
        case "update_profile":
            $post = json_decode($_POST['arg1'],true);


            $id = $post['id'];
            if ($id != user::get_this_user()->get_val('id') && !user::get_this_user()->has_rank("chair")) {
                echo "Invalid Permission";
            }
            $prim_email = $post['email'];
            $test_user = user::get_user_by("email",$prim_email);
            if (!$test_user) {
            } else if ($test_user->get_val("id") != $id) {
                echo "That email is already taken!";
                break;
            }
            if ($prim_email == "") {
                echo "a primary email is required";
                break;
            }


            $user = user::get_user_by("id",$id);
            if (!$user) {
                echo "Something went wrong";
                break;
            }

            foreach (config::get_params() as $k=>$v) {
                $user->set_val($k,$post[$k]);
            }


            if (user::get_this_user()->has_rank("director") || true) {
                $user->remove_rank("alumni");
                $user->remove_rank("member");
                $user->remove_rank("director");
                if (isset($post['rank_director'])) {
                    $user->add_rank("director");
                } else {
                    if (isset($post['rank_alumni'])) {
                        $user->add_rank("alumni");
                    } else {
                        if (isset($post['rank_member'])) {
                            $user->add_rank("member");
                        }
                    }
                }


            }

            if ($post['keep_account'] == "DELETE") {
                $user->delete();
                if (user::get_this_user()->get_val("id") == $user->get_val("id")) {
                    session_destroy();
                }
                echo "<script>window.location.href = 'index.php';</script>";
                die();
            } else {
                if ($id == user::get_this_user()->get_val("id")) {
                    $user = user::get_user_by("id",$user->get_val("id"));
                    $user->force_login();
                    user::set_this_user($user);
                }
            }

            if (isset($post['current_password_profile']) && $post['current_password_profile']!="") {
                if (!isset($post['confirm_password_profile']) || !isset($post['password_profile'])) {
                    echo "password is required";
                    break;
                }else if ($post['password_profile'] != $post['confirm_password_profile']) {
                    echo "Passwords do not match";
                    break;
                } else if ($user->verify_password($post['current_password_profile']) ||
                    (user::get_this_user()->has_rank("chair") && $user->get_val("id")!=user::get_this_user()->get_val("id"))) {
                    $user->set_password($post['password_profile']);
                } else {
                    echo "Current password not correct";
                    break;
                }
            }
            if ($user->get_val("id") == user::get_this_user()->get_val("id")) {
                $_SESSION['msg'] = "Updated!";
            }
            echo "Updated!";

            break;
        case "update_request":
            if (!user::get_this_user()->is_logged_in()) {
                echo "Log out!";
                break;
            }
            $_POST = json_decode($_POST['arg1'],true);
            $db = connect_to_database();
            $res = $db->query("DESCRIBE requests");
            $res->fetch_assoc();
            $cols = array();
            $user_id = user::get_this_user()->get_val("id");
            if (isset($_POST['user_id'])) {
                if (user::get_this_user()->has_rank("crew_chief")) {
                    $user_id = mysqli_escape_string($db,$_POST['user_id']);
                }
            }
            $isnew = true;
            $cols_string = "(`user_id`";
            $userid = user::get_this_user()->get_val("id");
            $vals_string = "('$user_id',";
            if (isset($_POST['id'])) {
                $isnew = false;
                $vals_string = "SET ";
            }
            $i = 0;
            while ($row = $res->fetch_assoc()) {
                array_push($cols,$row['Field']);
                if (isset($_POST[$row['Field']])) {
                    if ($row['Field'] == "status" && !user::get_this_user()->has_rank("chair")) {
                        continue;
                    }
                    $val = mysqli_escape_string($db,$_POST[$row['Field']]);
                    if (!$val) {
                        continue;
                    }

                    if ($i > 0)
                        $cols_string .= ",";
                    $cols_string .= "`". $row['Field'] . "`";
                    if ($isnew) {

                        $vals_string .= "'". $val . "'";
                    } else {
                        if ($vals_string != "SET ")
                            $vals_string.=",";
                        $vals_string .= " `".$row['Field']."` = '" . $val . "' ";
                    }
                }
                ++$i;

            }
            if ($isnew) {
                $res = $db->query("INSERT INTO requests $cols_string) VALUES $vals_string)");
                if (!$res) {
                    echo "Failed";
                    error_log("INSERT INTO requests $cols_string) VALUES $vals_string)");
                    error_log(mysqli_error($db));
                    break;
                }
            } else {
                $res = $db->query("UPDATE requests $vals_string WHERE user_id='$user_id' AND id='{$_POST['id']}'");
                if (!$res) {
                    echo "Failed";
                    error_log("UPDATE requests $vals_string WHERE user_id='$user_id' AND id='{$_POST['id']}'");
                    error_log(mysqli_error($db));
                    break;
                }
            }
            $_SESSION['msg'] = "Submitted! You can always view and edit your request under the view your requests tab";
            echo "Done";
            break;


    }
}