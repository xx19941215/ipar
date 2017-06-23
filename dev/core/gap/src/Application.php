<?php
namespace Gap;

class Application
{
    protected static $app;

    public static function app()
    {
        return self::$app;
    }

    public static function console($opts = [])
    {
        self::$app = new Console\Application();
        self::$app->init($opts);
        return self::$app;
    }

    public static function http($opts = [])
    {
        self::$app = new Http\Application();
        self::$app->init($opts);
        return self::$app;
    }
}
