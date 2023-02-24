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
        protected readonly bool $checkExecutable = false,
    ) {
    }

    public function run(): bool
    {
        if ($this->checkExists && !file_exists($this->fileName)) {
            $this->setErrorMessage($this->fileName . ' does not exist');
            return false;
        }

        if ($this->checkReadable && !is_readable($this->fileName)) {
            $this->setErrorMessage($this->fileName . ' is not readable');
            return false;
        }

        if ($this->checkWriteable && !is_writeable($this->fileName)) {
            $this->setErrorMessage($this->fileName . ' is not writeable');
            return false;
        }

        $checkIsFile = $this->checkExecutable || $this->checkIsFile;

        if ($checkIsFile && !is_file($this->fileName)) {
            $this->setErrorMessage($this->fileName . ' is not a file');
            return false;
        }

        if ($this->checkIsDirectory && !is_dir($this->fileName)) {
            $this->setErrorMessage($this->fileName . ' is not a directory');
            return false;
        }

        if ($this->checkExecutable && !is_executable($this->fileName)) {
            $this->setErrorMessage($this->fileName . ' is not executable');
            return false;
        }

        return true;
    }
}
