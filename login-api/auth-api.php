<?php

/**
 * Class types
 * links parameter types with mysql types
 */

class config
{
    private function __construct() {

        $config_file_contents = file_get_contents(pathinfo(__FILE__, PATHINFO_DIRNAME)."/config.json");

        if (!$config_file_contents) {
            die("config file not found");

        }
        $this->configured = json_decode($config_file_contents);
    }

    public static function get_config() {
        if (!isset(self::$instance)){
            self::$instance = new config();
        }
        return self::$instance;
    }
    public static function get_tablename() {
        return self::get_config()->configured->{"db_tablename"};
    }
    public static function get_params() {
        return self::get_config()->configured->{"user_parameters"};
    }
    public static function get_required() {
        return self::get_config()->configured->{"required"};
    }
    public static function get_unique() {
        return self::get_config()->configured->{"unique_param"};
    }
    public static function get_ranks() {
        return self::get_config()->configured->{"ranks"};
    }
    public static function get_value($value) {
        return self::get_config()->configured->{$value};
    }


    private static $instance;
    public $configured;

}

class db_connect {
    private function __construct() {
        $config = config::get_config();
        $this->db = new mysqli(
            $config->configured->{"db_servername"},
            $config->configured->{"db_username"},
            $config->configured->{"db_password"},
            config::get_tablename()
        );
    }
    public static function getdb() {
        if (!isset(self::$instance)){
            self::$instance = new db_connect();
        }
        return self::$instance;
    }
    public static $instance;
    public $db;

}
function connect_to_database() {
    return db_connect::getdb()->db;
}

class user {
    private function __construct()
    {

    }


    public static function get_this_user() {
        self::construct();
        return self::$instance;
    }
    public static function set_this_user(&$user) {
        self::$instance = $user;
        $_SESSION['user'] = self::$instance;
    }



    public static function get_user_by($param,$value) {
        $db = connect_to_database();
        $param = mysqli_escape_string($db,$param);
        $stmt = $db->prepare("SELECT * FROM users WHERE $param=?");
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $user = $stmt->get_result();
        if (mysqli_num_rows($user) != 1) {
//            echo "failed!";
            return false;
        }
        $user_values = mysqli_fetch_assoc($user);
        $user_class = new user();
        $user_class->set_user_info($user_values);
        return $user_class;

    }
    public static function get_users_by($param,$value) {
        $db = connect_to_database();

        $stmt = $db->prepare("SELECT * FROM users WHERE ?=?");
        $stmt->bind_param("ss", $param,$value);
        $stmt->execute();
        $user = $stmt->get_result();
        if (mysqli_num_rows($user) == 0) {
            return false;
        }
        $user_array = array();
        while ($user_values = mysqli_fetch_assoc($user)) {
            $user_class = new user();
            $user_class->set_user_info($user_values);
            array_push($user_array,$user_class);
        }

        return $user_array;
    }
    public static function get_all_users() {
        return self::get_users_by(1,1);
    }





    public function has_rank($rank) {
        if (!isset($this->total_ranks[$rank])) {
            return false;
            die("something went wrong");
        }
        if ($this->total_ranks[$rank]) {
            return true;
        }
        return false;
    }
    public function add_rank($rank) {

        $user = $this;
        if (!self::get_user_by("id",$this->id,$user)) {
            return false;
        }
        if ($user->has_rank($rank)) {
            return false;
        }
        $this->total_ranks[$rank] = true;
        $db = connect_to_database();
        $stmt = $db->prepare("INSERT INTO ranks (user_id,rank_id) VALUES (?,?)");
        $stmt->bind_param("ss",$this->id,$rank);
        return $stmt->execute();

    }
    public function remove_rank($rank) {

        $user = $this;
        if (!self::get_user_by("id",$this->id,$user)) {
            return false;
        }
        if (!$user->has_rank($rank)) {
            return false;
        }
        $this->total_ranks[$rank] = false;
        $db = connect_to_database();
        $stmt = $db->prepare("DELETE FROM ranks WHERE user_id=? AND rank_id=?");
        $stmt->bind_param("ss",$this->id,$rank);
        return $stmt->execute();
    }
    public function set_password($password) {
        $password_hash = password_hash($password,PASSWORD_BCRYPT);
        $db = connect_to_database();
        $stmt = $db->prepare("UPDATE users SET password=? WHERE id=$this->id");
        $stmt->bind_param("s",$password_hash);
        $stmt->execute();
    }
    public static function create_account(&$post,&$error) {

        $password_hash = password_hash($post['password'],PASSWORD_BCRYPT);
        $db = connect_to_database();
        $unique = config::get_unique();
        $username = $post[$unique];
        $stmt = $db->prepare("SELECT * FROM users WHERE $unique = ?");
        if (!$stmt) {
            $error = "Error: select call failed";
            return false;
        }
        $stmt->bind_param("s",$username);
        if (!$stmt->execute()) {
            $error = "Execute db error";
            return false;
        }
        $existing_user = $stmt->get_result();
        $stmt->close();
        if (mysqli_num_rows($existing_user) > 0) {
            $error = "A user with that email already exists";
            return false;
        }
        $params = config::get_params();

        $params_copy = array();
        $required = config::get_required();
        $params_string = "";
        $params_questions = "";
        $i = 0;
        foreach ($params as $key => $value) {
            if (isset($post[$key])) {
                $comma = "";
                if ($i > 0) {
                    $comma = ",";
                }
                $params_string .= $comma.$key ;
                $params_questions .= $comma."?";
                $params_copy[$key] = &$post[$key];
            } else if (in_array($key,$required)) {
                $error = "A required field is missing";
                return false;
            }
            ++$i;
        }
        $params = $params_copy;
        $params["password"] = &$password_hash;
        $params_string.=",password";
        $params_questions.=",?";

        $stmt = $db->prepare("INSERT INTO users ($params_string) VALUES ($params_questions)");
        $params = array_merge(array(str_repeat('s', count($params))), array_values($params));
        call_user_func_array(array(&$stmt, 'bind_param'), $params);



        if (!$stmt) {
            $error ="Binding parameter failed";
            return false;

        }

        if (!$stmt->execute()) {
            $error = "Database error";
            $error.= mysqli_error($db);
            return false;
        }

        return true;
    }
    public function delete() {
        $db = connect_to_database();
        $db->query("DELETE FROM users WHERE id='$this->id'");

    }
    public static function login($username,$password) {
        $user = self::get_user_by(config::get_unique(),$username);
        if (!$user) {
            return false;
        }
        $db = connect_to_database();
        $id = $user->get_val("id");
        if (!$user->verify_password($password)) {
            $db->query("UPDATE users SET failed_attempts = failed_attempts+1 WHERE id=$id");
            return false;
        }

        $db->query("UPDATE users SET failed_attempts = 0 WHERE id=$id");
        self::set_this_user($user);
        return true;
    }
    public function verify_password($password) {

        if (!password_verify($password,$this->password)) {
            return false;
        }
        $this->logged_in = true;
        return true;

    }

    public function get_val($param) {
        return $this->{$param};
    }
    public function set_val($param,$value) {
        try {
            $db = connect_to_database();
            $param = mysqli_real_escape_string($db,$param);
            $stmt = $db->prepare("UPDATE users SET {$param}=? WHERE id=?");
            $stmt->bind_param("ss",$value, $this->id);
            $stmt->execute();
            $this->{$param} = $value;
        } catch (Exception $e) {
            echo mysqli_error($db);
            die($e);
        }
    }
    public function force_login() {
        $this->logged_in = true;
    }
    public function is_logged_in() {

        return $this->logged_in;
    }

    public function generate_password_reset($expire_len = 300) {
        $db = connect_to_database();
        try {
            $code = random_int(100000,999999);
            $code_encrypted = password_hash($code,PASSWORD_BCRYPT);
            $user_id = $this->id;
            $time = time();
            $db->query("DELETE FROM password_reset WHERE user_id = $user_id OR expire_time < $time");
            $time+=$expire_len;
            $db->query("INSERT INTO password_reset (user_id,reset_code,expire_time) VALUES ('$user_id','$code_encrypted','$time')");
            return $code;

        } catch (Exception $e) {
            die("Something went wrong");
        }
        return $code;
    }


    public function verify_password_reset_code($reset_code) {
        $db = connect_to_database();

        $time = time();
        $db->query("DELETE FROM password_reset WHERE user_id = $this->id AND expire_time < $time");

        $res = $db->query("SELECT * FROM password_reset WHERE user_id = $this->id");
        if ($res->num_rows == 0) {
            echo "??";
            return false;
        }
        $code_verify = $res->fetch_assoc()['reset_code'];
        if (password_verify($reset_code,$code_verify)) {
            $db->query("DELETE FROM password_reset WHERE user_id = $this->id OR expire_time < $time");
            return true;
        }
        return false;
    }

    public $total_ranks = array();
    public $actual_ranks = array();

    private function set_user_info($user_values) {
        try {



            foreach ($user_values as $key => $value) {
                $this->$key = $value;
            }
            $this->failed_attempts = $user_values['failed_attempts'];

            $this->fetch_ranks_from();
            return true;

        } catch (Exception $e) {
            echo $e;
            exit;
        }
    }
    private static function construct() {
        if (!isset($_SESSION['user'])) {
            self::$instance = new user();
            $_SESSION['user'] = self::$instance;
        } else {
            self::$instance = $_SESSION['user'];
        }
    }
    private function fetch_ranks_from() {
        $db = connect_to_database();
        $stmt = $db->prepare("SELECT * FROM ranks WHERE user_id=?");
        $stmt->bind_param("s",$this->id);
        $stmt->execute();
        $res = $stmt->get_result();
        $ranks = config::get_ranks();
        while ($row = mysqli_fetch_assoc($res)) {
            $this->find_ranks_to($ranks,$row['rank_id'],$this->total_ranks);
        }

    }
    private function find_ranks_to($ranks, $rank,&$returnarray) {
        if (is_object($ranks)) {
            $return = false;
            foreach($ranks as $x=>$v) {
                if ($x == $rank) {
                    $returnarray[$x] = true;
                    $this->actual_ranks[$x] = true;
                    $return = true;
                } else {
                    $returnarray[$x] = false;
                    if ($this->find_ranks_to($v,$rank,$returnarray)) {

                        $returnarray[$x] = true;
                        $return = true;

                    }
                    if ($x == $rank) {

                        $returnarray[$x] = true;
                        $return = true;

                    }


                }
            }
            return $return;
        } else {
            return false;
        }
    }
    public function logout() {
        $this->logged_in = false;
        if (user::get_this_user()->get_val("id") == $this->id) {
            session_destroy();
        }
        echo "Done!";
    }
    private static $instance;
    private $id;
    private $failed_attempts;

    private $logged_in = false;

}

session_start();