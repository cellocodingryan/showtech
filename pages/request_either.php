<form class="request_form">
    <input type="hidden" name="type" value="<?php echo $rentalorshow;?>">
    <?php
    if ($page == "request_either") {
        die("Something went wrong :(");
    }
    require "pages/flexible_text.php";
    ?>
    <?php if (user::get_this_user()->has_rank("chair")): ?>
        <?php if ($edit_mode == true): ?>
            <div id="add_mod_button">
                <button type="button" class="btn btn-primary add_mod" mod_num='-1' data-toggle="modal"
                        data-target=".edit-module-popup">Add Module
                </button>
                <a href="<?php echo "index.php?page=$page&edit_mode=0";?>" class="btn btn-primary edit_mode">
                    Exit Edit Mode
                </a>

            </div>
        <?php endif ?>
        <?php if ($edit_mode == false): ?>
            <div class="button_center">
                <a href="<?php echo "index.php?page=$page&edit_mode=1";?>" class="btn btn-primary edit_mode">
                    Edit Mode
                </a>

            </div>
        <?php endif ?>





        <?php if ($edit_mode == true): ?>
            <div class="modal fade edit-module-popup" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <h4 class="center">Edit Module</h4>
                        <?php
                        include 'form-api/form-instructions.html';
                        include "popup_windows/pop-up-full-writer.php";
                        ?>
                        <button style="margin-top: 20px"  type="button" page="<?php echo $pagenum; ?>"
                                class="savemod btn btn-primary">Save
                        </button>
                    </div>
                </div>
            </div>
        <?php endif ?>

    <?php endif ?>

    <div class="button_center">
        <input style="margin: 0 auto;" type="submit" class="submit_form btn btn-success">
    </div>
</form>
