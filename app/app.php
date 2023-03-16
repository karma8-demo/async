<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use Amp\Mysql\MysqlConfig;
use Amp\Mysql\MysqlConnectionPool;
use App\Handlers\EmailsCheck;
use App\Handlers\EmailsPromote;
use App\Handlers\EmailsSend;
use Revolt\EventLoop;

$pool = new MysqlConnectionPool(MysqlConfig::fromString('host=localhost user=root db=karma8async'));

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
