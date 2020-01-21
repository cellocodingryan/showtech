<?php
$db = connect_to_database();
if ($page == "request_either") {
    die("Something went wrong :(");
}
$edit_filename = "";
$pagenumsql = $pagenum;
if ($pagenum == 2 or $pagenum == 4 or $pagenum == 6) {
    $pagenumsql = $pagenum+1;
    $edit_filename = "-e";
}
$res = $db->query("SELECT * FROM mods WHERE page=$pagenumsql");

while ($row = mysqli_fetch_assoc($res)) {
    $edit = "";
    if (user::get_this_user()->has_rank("chair") and $edit_mode) {
        $edit = "<button type=\"button\" class=\"btn btn-primary edit_mod\" mod_num=\"{$row['id']}\" data-toggle=\"modal\" data-target=\".edit-module-popup\">Edit Module</button>
    <button type=\"button\" class=\"btn btn-danger delete_mod\" mod_num=\"{$row['id']}\" >Delete Module</button>
    <div class=\"clearfix\"></div>";
    }
    $content = file_get_contents("mods/".$row['id'].$edit_filename.".html");
    echo "<div class=\"content-box \">
    <div class=\"content ql-editor\">
        {$content}
    </div>
    {$edit}
</div>";
}
?>





