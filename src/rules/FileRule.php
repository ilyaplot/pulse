<?php

declare(strict_types=1);

namespace ilyaplot\pulse\rules;

use ilyaplot\pulse\LevelEnum;

class FileRule extends AbstractRule implements RuleInterface
{
    public function __construct(
        protected string $fileName,
        protected string $description = '',
        protected ?LevelEnum $level = null,
        protected readonly bool $checkExists = true,
        protected readonly bool $checkReadable = false,
        protected readonly bool $checkWriteable = false,
        protected readonly bool $checkIsFile = false,
        protected readonly bool $checkIsDirectory = false,
    ) {
    }

    public function run(): bool
    {
        return file_exists($this->fileName);
    }
}
