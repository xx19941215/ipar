#!/bin/bash

# http://eslint.org/docs/user-guide/command-line-interface

base_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && cd .. && pwd )"
cd "$base_dir"

eslint \
"$base_dir/dev/front/js/**" \
"$base_dir/dev/app/ipar/front/js/app/**" "$base_dir/dev/app/ipar/front/js/com/**" \
"$base_dir/dev/app/admin/front/js/app/**" "$base_dir/dev/app/admin/front/js/com/**" \
"$base_dir/dev/app/user/front/js/app/**" "$base_dir/dev/app/user/front/js/com/**" \
--ext .js \
-c "$base_dir/script/eslint/eslintrc.js" \
-f "html" \
-o "$base_dir/site/doc/eslint/report.html"
