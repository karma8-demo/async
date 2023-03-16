<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use Amp\Mysql\MysqlConfig;
use Amp\Mysql\MysqlConnectionPool;
use App\Handlers\EmailsCheck;
use App\Handlers\EmailsPromote;
use App\Handlers\EmailsSend;
use Revolt\EventLoop;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->loadEnv(dirname(__DIR__).'/.env');

$pool = new MysqlConnectionPool(MysqlConfig::fromString('host='.$_ENV['DB_HOST'].' user='.$_ENV['DB_USERNAME'].' password='.$_ENV['DB_PASSWORD'].' db='.$_ENV['DB_DATABASE']));

//EventLoop::repeat(3600, static function () use ($pool): void {
EventLoop::delay(0, static function () use ($pool): void {
    (new EmailsCheck($pool))();
});

//EventLoop::repeat(3600, static function () use ($pool): void {
EventLoop::delay(0, static function () use ($pool): void {
    (new EmailsPromote($pool))();
});

//EventLoop::repeat(3600, static function () use ($pool): void {
EventLoop::delay(0, static function () use ($pool): void {
    (new EmailsSend($pool))();
});

EventLoop::run();
