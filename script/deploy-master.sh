#!/bin/bash

#script=$(readlink -f "$0")
#script_dir=$(dirname "$script")
#base_dir=$(dirname "$script_dir")

base_dir="/var/ideapar"
cd $base_dir

git checkout master
git pull origin master

./cmd/jsconfig

cd "$base_dir/dev/front"

gulp ipar-scss --nomaps --cleancss --production
gulp admin-scss --nomaps --cleancss --production
gulp user-scss --nomaps --cleancss --production

gulp ipar-webpack --nomaps --minifyjs --production
gulp admin-webpack --nomaps --minifyjs --production
gulp user-webpack --nomaps --minifyjs --production
