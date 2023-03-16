<?php

declare(strict_types=1);

namespace App\Handlers;

use Amp\Mysql\MysqlConnectionPool;
use Amp\Pipeline\Pipeline;
use App\Jobs\EmailCheck;

final readonly class EmailsCheck
{
    public function __construct(private MysqlConnectionPool $pool) {
    }


    public function __invoke(): void {
        $result = $this->pool->execute('select email from emails where checked = ?', [0]);

        Pipeline::fromIterable($result)
            ->concurrent(100)
            ->unordered()
            ->forEach(fn ($item): null => (new EmailCheck($this->pool, $item['email']))());
    }
}
