<?php

declare(strict_types=1);

namespace ilyaplot\pulse\rules;

use ilyaplot\pulse\LevelEnum;

class FileExistsRule extends AbstractRule implements RuleInterface
{
    public function __construct(
        protected string $fileName,
        protected string $description = '',
        protected ?LevelEnum $level = null,
    ) {
    }

    public function run(): bool
    {
        return file_exists($this->fileName);
    }
}
