<?php

namespace App\Enum;

enum Status: string
{
    case Success = 'success';
    case Aborted = 'aborted';
    case Pending = 'pending';
}
