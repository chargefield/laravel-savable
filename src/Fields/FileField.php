<?php

namespace Chargefield\Savable\Fields;

use Chargefield\Savable\Exceptions\InvalidUploadFileException;
use Illuminate\Http\UploadedFile;

class FileField extends Field
{
    /**
     * @var string
     */
    protected string $path = 'images';

    /**
     * @var bool
     */
    protected bool $withOriginalName = false;

    /**
     * @var string|null
     */
    protected ?string $disk = null;

    /**
     * @return $this
     */
    public function withOriginalName(): self
    {
        $this->withOriginalName = true;

        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function path(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param string $disk
     * @return $this
     */
    public function disk(string $disk): self
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * @param array $data
     * @return string|null
     *
     * @throws InvalidUploadFileException
     */
    public function handle(array $data = [])
    {
        $file = $this->value;

        if (! ($file instanceof UploadedFile)) {
            if ($this->nullable) {
                return null;
            }

            throw new InvalidUploadFileException($file);
        }

        if ($this->withOriginalName) {
            return $file->storeAs($this->path, $file->getClientOriginalName(), $this->getOptions());
        }

        return $file->store($this->path, $this->getOptions());
    }

    protected function getOptions(): array
    {
        return [
            'disk' => $this->getDisk(),
        ];
    }

    protected function getDisk(): string
    {
        return $this->disk ?? config('filesystems.default');
    }
}
