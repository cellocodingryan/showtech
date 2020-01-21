<?php

if (!isset($user)) {
    $user = user::get_this_user();
    if (!$user) {
        header("Location: index.php");
    }
}

function print_as_form_add($ranks, &$user, &$rank_hash) {
    if (is_object($ranks)) {
        foreach ($ranks as $x => $v) {
            print_as_form_add($v,$user,$rank_hash);
            if (!isset($rank_hash[$x]) and !$user->has_rank($x)) {
                $rank_hash[$x] = true;
                echo "<option value='{$x}'>{$x}</option>";
            }
        }
    }
}
?>


<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<div class="container">
    <div class="row flex-lg-nowrap">

        <div class="col">
            <div class="row">
                <div class="col mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="e-profile">
                                <div class="row">
                                    <div class="col-12 col-sm-auto mb-3">
                                        <div class="mx-auto" style="width: 140px;">
                                            <?php
                                            $db = connect_to_database();
                                            $id = $user->get_val("id");
                                            $pic_id = $db->query("SELECT * FROM uploads WHERE title='{$id}' ORDER BY id DESC");
                                            ?>
                                            <div class="d-flex justify-content-center align-items-center rounded" style="height: 140px; <?php if ($pic_id->num_rows == 0) echo 'background-color: rgb(233, 236, 239);'?>">
                                                <?php

                                                if ($pic_id->num_rows == 0) {
                                                    echo "<span style=\"color: rgb(166, 168, 170); font: bold 8pt Arial;\">140x140</span>";
                                                } else {
                                                    $pic_id=$pic_id->fetch_assoc();
                                                    list($width, $height, $type, $attr) =
                                                        getimagesize("../uploads/profile_picture_{$pic_id['id']}.{$pic_id['ext']}");
                                                    $style = "width: 100%";
                                                    if ($height > $width)
                                                        $style = "height: 100%";
                                                    echo "<img style='$style' id='profile_pic_edit' src='uploads/profile_picture_{$pic_id['id']}.{$pic_id['ext']}'>";
                                                }
                                                ?>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col d-flex flex-column flex-sm-row justify-content-between mb-3">
                                        <div class="text-center text-sm-left mb-2 mb-sm-0">
                                            <h4 class="pt-sm-2 pb-1 mb-0 text-nowrap"><?php echo $user->get_val("firstname")." ". $user->get_val("lastname");?></h4>
                                            <!--                                            <div class="text-muted"><small>Last seen 2 hours ago</small></div>-->
                                            <div class="mt-2">
                                                <!--                                                <button class="btn btn-primary change_pic_button" type="button">-->
                                                <!--                                                    <i class="fa fa-fw fa-camera"></i>-->
                                                <!--                                                    <span>Change Photo</span>-->
                                                <!--                                                </button>-->
                                                <div id="profile_progress"></div>
                                                <input type="file" id="profile_picture" class="hidden">
                                            </div>
                                        </div>
                                        <div class="text-center text-sm-right">
                                            <span class="badge badge-secondary">
                                                <?php
                                                if ($user->has_rank("chair")) {
                                                    echo "Chair";
                                                } else if ($user->has_rank("sound_crew_chief") || $user->has_rank("lights_crew_chief")) {
                                                    echo "Crew Chief";
                                                } else if ($user->has_rank("sound_tech") || $user->has_rank("lights_tech")) {
                                                    echo "Technician";
                                                } else if ($user->has_rank("trainee")) {
                                                    echo "Trainee";
                                                } else {
                                                    echo "General User";
                                                }
                                                ?>

                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <ul class="nav nav-tabs">
                                    <li class="nav-item"><a href="" class="active nav-link">Settings</a></li>
                                </ul>
                                <div class="tab-content pt-3">
                                    <div class="tab-pane active">
                                        <form class="form" id="profile_form" novalidate="">
                                            <input type="hidden" name="id" value="<?php echo $user->get_val('id');?>">
                                            <div class="row">
                                                <div class="col">
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="form-group">
                                                                <label>First Name</label>
                                                                <input class="form-control" type="text" name="firstname" value="<?php echo $user->get_val("firstname");?>">
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group">
                                                                <label>Last Name</label>
                                                                <input class="form-control" type="text" name="lastname" value="<?php echo $user->get_val("lastname");?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="form-group">
                                                                <label>Phone Number</label>
                                                                <input class="form-control" type="tel" name="phone_number" value="<?php
                                                                echo $user->get_val("phone_number");
                                                                ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="form-group">
                                                                <label>Primary Email</label>
                                                                <input class="form-control" type="text" name="email" value="<?php
                                                                echo $user->get_val("email");
                                                                ?>">


                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="form-group">
                                                                <label>Rin Email</label>
                                                                <input class="form-control" type="text" name="rin" value="<?php
                                                                echo $user->get_val("rin");
                                                                ?>">


                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>








                                            </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-6 mb-3">
                                            <div class="mb-2"><b>Change Password</b></div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label>Current Password</label>
                                                        <input class="form-control" name="current_password_profile" type="password" placeholder="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label>New Password</label>
                                                        <input class="form-control" name="password_profile" type="password" placeholder="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label>Confirm <span class="d-none d-xl-inline">Password</span></label>
                                                        <input class="form-control" name="confirm_password_profile" type="password" placeholder=""></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-5 offset-sm-1 mb-3">
                                            <div class="mb-2"><b>Change Permissions</b></div>
                                            <?php

                                            foreach ($user->total_ranks as $rank => $_) {
                                                $checked = "";
                                                if (isset($user->actual_ranks[$rank]) && $user->actual_ranks[$rank] == true) {
                                                    $checked = "checked";
                                                }
                                                echo "<label> Rank:{$rank} <input type='checkbox' $checked name='rank_$rank'></label><br>";
                                            }

                                            ?>
                                            <div class="mb-2"><b>Click below to remove account from server</b></div>
                                            <button type="button" class="btn btn-danger delete_account_button">Delete Account</button>
                                            <input type="hidden" id="keep_account" name="keep_account" value="">
                                        </div>
                                        <!--                                                --><?php //endif ?>
                                    </div>
                                    <div class="row">
                                        <div class="col d-flex justify-content-end">
                                            <button class="btn btn-primary submit" type="submit">Save Changes</button>
                                        </div>
                                    </div>
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
