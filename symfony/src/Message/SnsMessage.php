<?php

namespace App\Message;

class SnsMessage
{
    public function __construct(
        public readonly string $content
    ) {}
}
