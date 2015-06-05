<?php
 
$env = getenv('APP_ENV') ? : 'development';
 
$modules = array(
    'Contato',
);
if ($env == 'development') {
    $modules[] = 'ZendDeveloperTools';
    $modules[] = 'BjyProfiler';
    $modules[] = 'ZFTool';
}
 
return array(
    'modules' => $modules,
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor',
        ),
        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'config_cache_enabled' => false,
        'config_cache_key' => 'app_config',
        'module_map_cache_enabled' => ($env == 'development'),
        'module_map_cache_key' => 'module_map',
        'cache_dir' => './data/cache',
        'check_dependencies' => ($env != 'development'),
    ),
);
