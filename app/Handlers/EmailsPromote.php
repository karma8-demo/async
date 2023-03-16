<?php

declare(strict_types=1);

namespace App\Handlers;

use Amp\Mysql\MysqlConnectionPool;
use function printf;

final readonly class EmailsPromote
{
    public function __construct(private MysqlConnectionPool $pool) {
    }


    public function __invoke(): void {
        $result = $this->pool->execute('update emails inner join users on users.id = emails.user_id and users.confirmed = ?
set emails.checked = ?,
    emails.valid = ?
where emails.valid = ?',
            [1, 1, 1, 0]);

        $count = $result->getRowCount();

        printf("Found %d confirmed emails and promoted as checked and validated\n", $count);
    }
}
