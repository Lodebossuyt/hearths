<?php

namespace App\Hearts;

enum RoundStatus: string
{
    case Passing = 'passing';
    case Playing = 'playing';
    case Finished = 'finished';
}
