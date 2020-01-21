<?php

class inputtypes {


}

class form {

    public function __construct($page = -1) {
        $this->page = $page;

    }

    function verify_type($type,$content) {

    }
    private function replace_($start, $end, $type, $content) {

        if (!$start or !$end) {
            return false;
        }
        $end+=2;
        $beg = substr($content,0,$start);
        $mid = substr($content,$start,$end);
        $end += 3;
        $endstr = substr($content,$end);
        $lbracket = strpos($mid,"&lt;");
        $rbracket = strpos($mid, "&gt;]");
        if (!$lbracket or !$rbracket) {
            return false;
        }
        ++$lbracket;

        $name = substr($mid,0,$rbracket);
        if (!$name) {
            return false;
        }
        error_log($name);
        $name = substr($name,$lbracket+3);
        if (!$name) {
            return false;
        }
        $this->add_column($type,$name);
//        $db->query("")
        if ($type == "largetext") {
            $replacewith = "<textarea required class='form-control' id='$name' rows='5' name='$name'> </textarea>";
        } else {
            $required = "required";
            if ($type == "checkbox" || $type == "radio"){
                $required = "";
            }
            $replacewith = "<input $required placeholder='Your Answer' type='$type' id='$name' name='$name'>";
        }
        return $beg.$replacewith.$endstr;


    }
    private function add_column($type,$name) {
        $sqltype = $this->mysqltypes[$type][0];
        $sqllen = $this->mysqltypes[$type][1];
        $db = connect_to_database();
        $name = mysqli_escape_string($db,$name);
        $result = mysqli_query($db,"SHOW COLUMNS FROM `requests` LIKE '$name'");
        $exists = (mysqli_num_rows($result))?TRUE:FALSE;
        if (!$exists) {
            $sql = "ALTER TABLE requests ADD COLUMN `$name` $sqltype($sqllen)";
            $res = $db->query($sql);

        }
        error_log(mysqli_error($db));

    }
    function replace_input_with_input($content) {
        $done = false;
        while(!$done) {
            $done = true;
            foreach ($this->template as $k=>$v) {
                $start = strpos($content,$k);
                if (!$start) {
                    continue;
                }
                $end = strpos($content,"&gt;]",$start);
                $test = $this->replace_($start,$end,$v,$content);
                if (!$test) {

                    continue;
                }

                $content = $test;
                $done = false;
            }




        }
        return $content;
    }

    private $mysqltypes = [
        "checkbox" => ["TINYINT",1],
        "radio"=> ["TINYINT",1],
        "color"=> ["VARCHAR",7],
        "date"=> ["DATE",6],
        "datetime-local" => ["DATETIME",10],
        "email" => ["VARCHAR",256],
        "month"=> ["VARCHAR",256],
        "number"=>["FLOAT",15],
        "largetext"=>["TEXT",1000],
        "range"=>["INT",5],
        "tel"=>["VARCHAR",20],
        "text"=>["VARCHAR",300],
        "time"=>["TIME",7],
        "url"=>["VARCHAR",256],
        "week"=>["VARCHAR",100]

    ];


    private $template = [
        "[CHECK " => "checkbox",
        "[RADIO " => "radio",
        "[COLOR " => "color",
        "[LARGETEXT" => "largetext",
        "[DATE " => "date",
        "[DATETIME " => "datetime-local",
        "[EMAIL " => "email",
        "[MONTH " => "month",
        "[NUMBER " => "number",
        "[RANGE " => "range",
        "[TEL " => "tel",
        "[TEXT " => "text",
        "[TIME " => "time",
        "[URL " => "url",
        "[WEEK " => "week"
    ];

    private $page;

}

//$f = new form();
////$test = $f->replace_input_with_input("this is some text here [CHECK <some  here>] aweardsf");
////echo $test;