<?php

function command_exists($cmd) {
    $return = shell_exec(sprintf("which %s", escapeshellarg($cmd)));
    return !empty($return);
}

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    echo 'This command may not work as expected on Windows.';
    exit(1);
} 

if(!command_exists("git")) {
    echo "You need to install git first.";
    exit(2);
}

$work_dir = "for_api";
$res_dir = ".couscous/generated";

if(!file_exists($work_dir))
    @mkdir($work_dir);

@mkdir($res_dir);
chdir($work_dir);

if(!file_exists("pho-lib-graph"))
    exec("git clone http://github.com/phonetworks/pho-lib-graph");
else {
    chdir("pho-lib-graph");
    exec("git pull");
    chdir("..");
}

if(!file_exists("pho-framework"))
    exec("git clone http://github.com/phonetworks/pho-framework");
else {
    chdir("pho-framework");
    exec("git pull");
    chdir("..");
}

/*
if(!file_exists("pho-microkernel"))
    exec("git clone http://github.com/phonetworks/pho-microkernel");
else {
    chdir("pho-microkernel");
    exec("git pull");
    chdir("..");
}
*/

//exec("mv pho-lib-graph/src/Pho/Lib pho-microkernel/src/Pho/Kernel pho-framework/src/Pho");
exec("mv pho-lib-graph/src/Pho/Lib  pho-framework/src/Pho");
exec("../vendor/bin/phpdoc run -t ../{$res_dir}/api -d pho-framework/src/ --template=clean --ignore=\"*tests?*,*Tests?*\"");
exec("mv pho-framework/src/Pho/Lib pho-framework/src/Pho");
//exec("mv pho-framework/src/Pho/Kernel pho-microkernel/src/Pho");

exec("cp -pR ../img ../{$res_dir}/img");

exit(0);