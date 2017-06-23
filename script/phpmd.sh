#!/bin/bash

base_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && cd .. && pwd )"
cd "$base_dir"
/home/mars/.config/composer/vendor/bin/phpmd \
"$base_dir/dev/app/admin/src/","$base_dir/dev/app/admin/test/","$base_dir/dev/app/admin/view/",\
"$base_dir/dev/app/ipar/src/","$base_dir/dev/app/ipar/test/","$base_dir/dev/app/ipar/view/",\
"$base_dir/dev/app/user/src/","$base_dir/dev/app/user/test/","$base_dir/dev/app/user/view/",\
"$base_dir/dev/lib/gap/src/","$base_dir/dev/lib/gap/test/",\
"$base_dir/dev/lib/mars/src/","$base_dir/dev/lib/mars/test/" \
html \
"$base_dir/script/phpmd/rules.xml" \
--reportfile "$base_dir/site/doc/phpmd/report.html" \
--suffixes php,phtml \

#cleancode,codesize,controversial,design,unusedcode,naming \
