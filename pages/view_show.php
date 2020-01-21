<?php
error_log("first:".$_SESSION['showid']);

if (isset($_GET['showid'])) {
    $_SESSION['showid'] = $_GET['showid'];
}
if (!isset($_SESSION['showid'])) {
    die("Invalid show id");
}
$id = $_SESSION['showid'];

error_log($_SESSION['showid']);
$db = connect_to_database();
$show = $db->prepare("SELECT * FROM requests WHERE id=?");
$show->bind_param("s",$id);
$show->execute();
$show = $show->get_result();
if (mysqli_num_rows($show) != 1) {
    die("something went wrong");
}
$show=$show->fetch_assoc();
if (!user::get_this_user()->is_logged_in()) {
    die("Invalid Permission");
} else if (user::get_this_user()->get_val("id") != $show['user_id']) {
    if (!user::get_this_user()->has_rank("trainee")) {
        die("Invalid Permission");
    }
}
if ($show['type'] == "Show") {
    $pagenum = 5;
} else {
    $pagenum = 3;
}
?>
    <form class="request_form">
<?php
echo "<input type='hidden' name='id' value='$id'>";
include 'flexible_text.php';
//$pagenum = 7;
//if (isset($_GET['edit_mode']) and $_GET['edit_mode'] == 1) {
//    $edit_mode = true;
//    $pagenum =6;
//}
//include 'request_either.php';
//error_log("test".$_SESSION['showid']);
//include 'request_either.php';
?>

<?php if ($show['type'] == "Show"): ?>
    <div class="content-box">
        <h2>Staffing</h2>

            <label>Load in Hours: <input id="load_in_hours" type="number" name="load_in_hours"> </label>
            <label>Show Hours: <input id='show_hours' type="number" name="show_hours"> </label>
            <label>Load out Hours: <input id="load_out_hours" type="number" name="load_out_hours"> </label>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Load in</th>
                    <th scope="col">Show</th>
                    <th scope="col">Load Out</th>
                    <th scope="col">Remove</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th scope="row">1</th>
                    <td>Mark</td>
                    <td>Otto</td>
                    <td>@mdo</td>
                </tr>
                <tr>
                    <th scope="row">2</th>
                    <td>Jacob</td>
                    <td>Thornton</td>
                    <td>@fat</td>
                </tr>
                <tr>
                    <th scope="row">3</th>
                    <td>Larry</td>
                    <td>the Bird</td>
                    <td>@twitter</td>
                </tr>
                </tbody>
            </table>
            <label>Add Worker <input type="email" name="new_tech"> </label>
            <button type="button" class="btn btn-primary">Add</button>

    </div>
<?php endif ?>
        <div class="button_center">
            <input style="margin: 0 auto;" type="submit" class="submit_form btn btn-success">
        </div>
    </form>
<?php


$i = 0;
foreach($show as $k=>$v) {
    if ($i < 2) {
        ++$i;
        continue;
    }
    echo "<script>document.getElementById('$k').setAttribute('value','$v');</script>";
    echo "<script>document.getElementById('$k').innerText = '$v';</script>";
}

?>