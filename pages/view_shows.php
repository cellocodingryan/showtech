<table class="table table-hover">
    <thead>
    <tr>
        <?php
        $db =connect_to_database();
        $res = $db->query("DESCRIBE requests");
        $i = 0;
        $cols = array();
        echo "<th scope=\"col\">Requester Name</th>";
        while ($row = $res->fetch_assoc()) {
            if ($i < 3) {
                ++$i;
                continue;
            }
            array_push($cols,$row['Field']);
            echo "<th scope='col'>{$row['Field']}</th>";
            ++$i;
        }
        ?>
<!--        <th scope="col">#</th>-->
<!--        <th scope="col">First</th>-->
<!--        <th scope="col">Last</th>-->
<!--        <th scope="col">email</th>-->
    </tr>
    </thead>
    <tbody>
    <?php
    $db = connect_to_database();
    $res = null;
    if (user::get_this_user()->has_rank("trainee")) {
        $res = $db->query("SELECT * FROM requests");
    } else {
        $id = user::get_this_user()->get_val("id");
        $res = $db->query("SELECT * FROM requests WHERE user_id=$id");
    }
    $i = 1;

    while ($row = $res->fetch_assoc()) {
        echo "<tr class='show_row' show_id='{$row['id']}'>";
        $user = user::get_user_by("id",$row['user_id']);
        $request_name = $user->get_val("firstname") . " " . $user->get_val("lastname");
        echo "<th scope='row'>$request_name</th>";
        foreach ($cols as $v) {
            echo "<td>$row[$v]</td>";
        }
//                    <th scope='row'></th>
//                    <th scope='row'>{$i}</th>
//                    <td>{$row['firstname']}</td>
//                    <td>{$row['lastname']}</td>
//                    <td>{$row['email']}</td>
//                    </tr>";
        echo "</tr>";
        ++$i;
    }
    ?>
    </tbody>
</table>