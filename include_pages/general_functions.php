<?php
require_once "../form-api/form-edit.php";
function edit_module($page,$module_id,$content) {
    $db = connect_to_database();
    $stmt = $db->prepare("SELECT * FROM mods WHERE id=?");
    $stmt->bind_param("s",$module_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $edit_mode = false;
    if ($page == 2 or $page == 4 or $page == 6) {
        $edit_mode =true;
    }
    if (mysqli_num_rows($res) != 1) {
        echo "There was a database error";
        return false;
    }
    file_put_contents("../mods/".$module_id.".html",$content);
//    if edit mode, then replace inputs with input tag
    if ($edit_mode) {
        file_put_contents("../mods/" . $module_id . "-e.html", $content);
        $f = new form($page+1);
        $content = $f->replace_input_with_input($content);
        file_put_contents("../mods/".$module_id.".html",$content);
    }
    $stmt->close();
}
function remove_module($module_id) {
    $db = connect_to_database();
    $stmt = $db->prepare("DELETE FROM mods WHERE id=?");
    $stmt->bind_param("s",$module_id);
    $stmt->execute();
}
function add_module($page,$content) {

    $db = connect_to_database();
    $edit_mode = false;
    if ($page == 2 or $page == 4 or $page == 6) {
        $edit_mode =true;
        //place page correct page number
        ++$page;
    }
    $stmt = $db->prepare("INSERT INTO mods (page) VALUES (?)");
    $stmt->bind_param("s",$page);
    $stmt->execute();
    $id = mysqli_insert_id($db);
    if ($edit_mode) {
        file_put_contents("../mods/" . $id . "-e.html", $content);
        $f = new form();
        $content = $f->replace_input_with_input($content);
    }
    file_put_contents("../mods/".$id.".html",$content);
    echo "test";
}