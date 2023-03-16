<?php

declare(strict_types=1);

namespace App\Jobs;

use Amp\Mysql\MysqlConnectionPool;
use function Amp\delay;
use function date;
use function random_int;

final readonly class EmailSend
{
    public function __construct(private MysqlConnectionPool $pool, private string $username, private string $email) {
    }


    public function __invoke(): void {
        delay(random_int(1, 10));

        echo "{$this->username} ({$this->email}), your subscription is expiring soon\n";

        $this->pool->execute('update users set notifiedts = ? where username = ?', [date('Y-m-d H:i:s'), $this->username]);
    }
}
