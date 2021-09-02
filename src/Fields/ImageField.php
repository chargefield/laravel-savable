<?php

namespace Chargefield\Supermodel\Fields;

use Chargefield\Supermodel\Exceptions\InvalidImageFileException;
use Illuminate\Http\UploadedFile;

class ImageField extends Field
{
    /**
     * @var string
     */
    protected string $path = 'images';

    protected bool $withOriginalName = false;

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
     * @param string $disk
     * @return $this
     */
    public function disk(string $disk): self
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * @param array $fields
     * @return string
     */
    public function handle(array $fields = [])
    {
        $file = parent::handle($fields);

        if (is_null($file)) {
            return null;
        }

        if (! ($file instanceof UploadedFile)) {
            throw new InvalidImageFileException($file);
        }

        $disk = $this->disk ?? config('filesystems.default');

        if ($this->withOriginalName) {
            return $file->storeAs($this->path, $file->getClientOriginalName(), ['disk' => $disk]);
        }

        return $file->store($this->path, ['disk' => $disk]);
    }
}