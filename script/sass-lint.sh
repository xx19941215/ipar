#!/bin/bash

# http://eslint.org/docs/user-guide/command-line-interface

base_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && cd .. && pwd )"
cd "$base_dir"

sass-lint \
"$base_dir/dev/app/*/front/scss/**/*.scss" \
-c "$base_dir/script/sass-lint/sass-lint.yml" \
-f "html" \
-o "$base_dir/site/doc/sass-lint/report.html" \
-v \
-q
