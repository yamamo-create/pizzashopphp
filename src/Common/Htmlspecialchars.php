<?php

namespace App\Common;

function h(string $data): mixed
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
