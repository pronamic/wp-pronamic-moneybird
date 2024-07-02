<?php

require_once __DIR__ . '/functions.php';

$slug = 'pronamic-moneybird';

$src_dir       = realpath( __DIR__ . '/../../' );
$build_dir     = realpath( __DIR__ . '/../' );
$stage_1_dir   = $build_dir . '/stage-1';
$stage_2_dir   = $build_dir . '/stage-2';
$artifacts_dir = $build_dir . '/artifacts';

echo $src_dir, PHP_EOL;
echo $build_dir, PHP_EOL;
echo $stage_1_dir, PHP_EOL;
echo $stage_2_dir, PHP_EOL;

run_command( "rm -rf $stage_1_dir" );
run_command( "rm -rf $stage_2_dir" );

run_command( "mkdir $stage_1_dir" );
run_command( "mkdir $stage_2_dir" );

run_command( "rsync --recursive --verbose --exclude-from=$build_dir/scripts/stage-1-ignore.txt --exclude-from=.distignore $src_dir/ $stage_1_dir/" );

run_command( "composer install --no-dev --prefer-dist --optimize-autoloader --working-dir=$stage_1_dir" );

run_command( "rsync --recursive --verbose --exclude-from=.distignore $stage_1_dir/ $stage_2_dir/" );

run_command( "vendor/bin/wp i18n make-pot $stage_2_dir --slug=$slug" );

run_command( "vendor/bin/wp dist-archive $stage_2_dir $artifacts_dir/ --create-target-dir --plugin-dirname=$slug" );
