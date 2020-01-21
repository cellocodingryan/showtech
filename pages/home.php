<?php

$pagenum = 1;
$edit_mode = true;
require_once "pages/flexible_text.php";
?>

<?php if (user::get_this_user()->has_rank("chair")): ?>
    <div id="add_mod_button">
        <button type="button" class="btn btn-primary add_mod" mod_num='-1' data-toggle="modal"
                data-target=".edit-module-popup">Add Module
        </button>

    </div>

    <div class="modal fade edit-module-popup" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <h4 class="center">Edit Module</h4>
                <?php
                include "popup_windows/pop-up-full-writer.php";
                ?>
                <button style="margin-top: 20px" type="button" page="<?php echo $pagenum;?>" class="savemod btn btn-primary">Save</button>
            </div>
        </div>
    </div>

<?php endif ?>
