<?php

declare(strict_types=1);

namespace App\Jobs;

use Amp\Mysql\MysqlConnectionPool;
use function Amp\delay;
use function random_int;

final readonly class EmailCheck
{
    public function __construct(private MysqlConnectionPool $pool, private string $email) {
    }


    public function __invoke(): void {
        delay(random_int(1, 60));

        $value = (bool) random_int(0, 1);

        echo "Check {$this->email}: {$value}\n";

        $this->pool->execute('update emails set checked = ?, valid = ? where email = ?', [1, $value, $this->email]);
    }
}
