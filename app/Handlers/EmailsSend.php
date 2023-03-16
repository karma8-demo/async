<?php

declare(strict_types=1);

namespace App\Handlers;

use Amp\Mysql\MysqlConnectionPool;
use Amp\Pipeline\Pipeline;
use App\Jobs\EmailSend;
use function date;
use function strtotime;

final readonly class EmailsSend
{
    public function __construct(private MysqlConnectionPool $pool) {
    }


    public function __invoke(): void {
        $result = $this->pool->execute('select users.username, emails.email
            from users
                     inner join emails on users.id = emails.user_id
            where users.validts <= ?
              and (users.notifiedts is null or users.notifiedts <= ?)
              and emails.valid = ?
            order by users.validts', [date('Y-m-d H:i:s', strtotime('+3 days')), date('Y-m-d H:i:s', strtotime('-3 days')), 1]);

        Pipeline::fromIterable($result)
            ->concurrent(100)
            ->unordered()
            ->forEach(fn ($item): null => (new EmailSend($this->pool, $item['username'], $item['email']))());
    }
}
