#!/bin/bash

# arg1=$1

script=$(readlink -f "$0")
script_dir=$(dirname "$script")
base_dir=$(dirname "$script_dir")

cd $base_dir
printf "Merge develop branch into master and push the master branch to ideapar.com"

git checkout develop
git pull origin develop

git checkout master
git merge --no-ff -m 'merge develop into master' develop

git push origin master
git checkout develop

printf "\n"

ssh i@ideapar.com < "$base_dir/script/deploy-master.sh"

#scp $base_dir/site/static/css/*.css i@ideapar.com:"$base_dir/site/static/css/"
#scp $base_dir/site/static/js/*.js i@ideapar.com:"$base_dir/site/static/js/"
