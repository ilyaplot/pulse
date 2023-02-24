<?php

declare(strict_types=1);

namespace ilyaplot\pulse;

enum LevelEnum: int
{
    case info = 1;
    case warning = 2;
    case critical = 3;
}
