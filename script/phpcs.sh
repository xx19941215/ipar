#!/bin/bash

base_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && cd .. && pwd )"
cd "$base_dir"

/home/mars/.config/composer/vendor/bin/phpcs \
    "$base_dir/dev/app/admin/src" "$base_dir/dev/app/admin/test" \
    "$base_dir/dev/app/ipar/src" "$base_dir/dev/app/ipar/test" \
    "$base_dir/dev/app/user/src" "$base_dir/dev/app/user/test" \
    "$base_dir/dev/lib/gap/src" "$base_dir/dev/lib/gap/test" \
    "$base_dir/dev/lib/mars/src" "$base_dir/dev/lib/mars/test" \
    --standard=PSR2 \
    --encoding=utf-8 \
    --extensions=php \
    --report-full="$base_dir/site/doc/phpcs/report-full.txt" \
    --report-summary="$base_dir/site/doc/phpcs/report-summary.txt"
