<?php
if (!user::get_this_user()->has_rank("chair")) {
    $_SESSION['last'] = "home";
    die("Invalid Permission");
}

?>
<h1 class="center">Manage Users</h1>
<div class="container admin_page">
    <div class="row">
        <div class="col">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">First</th>
                        <th scope="col">Last</th>
                        <th scope="col">email</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $db = connect_to_database();
                $res = $db->query("SELECT id,firstname,lastname,email FROM users");
                $i = 1;
                while ($row = $res->fetch_assoc()) {
                    echo "<tr class='user_row' user_id='{$row['id']}'>
                    <th scope='row'>{$i}</th>
                    <td>{$row['firstname']}</td>
                    <td>{$row['lastname']}</td>
                    <td>{$row['email']}</td>
                    </tr>";
                    ++$i;
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="col " id="view_user" >
            <?php
            if (isset($_GET['userid'])) {
                $_SESSION['viewed_user'] = $_GET['userid'];
            }
            if (isset($_SESSION['viewed_user'])) {
                $id = $_SESSION['viewed_user'];
                $user = user::get_user_by("id","$id");
                include_once "pages/user_info.php";
            } else {
                echo "<h2>No User Selected, click on a user to view more information</h2>";
            }
            ?>
        </div>
    </div>
</div>
