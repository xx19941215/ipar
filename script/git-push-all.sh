#!/bin/bash

cmd_dir () {
    dir=$1
    if [[ -d $dir ]]; then
        if [[ -d "${dir}/.git" ]]; then
            printf "$dir\n"
            cd $dir
            git checkout develop
            git push origin develop
            printf "\n"
        fi
    fi
}

base_dir=$(pwd)
arg1=$1


cmd_dir $base_dir


lib_base_dir="$base_dir/src/lib"
cd $lib_base_dir
for d in *; do
    lib_dir="$lib_base_dir/$d"
    cmd_dir $lib_dir
done

service_base_dir="$base_dir/src/service"
cd $service_base_dir
for d in *; do
    service_dir="$service_base_dir/$d"
    cmd_dir $service_dir
done

app_base_dir="$base_dir/src/app"
cd $app_base_dir
for d in *; do
    app_dir="$app_base_dir/$d"
    cmd_dir $app_dir

    js_lib_base="${app_dir}/front/js/lib"
    if [[ -d $js_lib_base ]]; then
        cd $js_lib_base
        for i in *; do
            js_lib_dir="$js_lib_base/$i"
            cmd_dir $js_lib_dir
        done
    fi
done
