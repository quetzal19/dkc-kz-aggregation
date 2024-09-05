<?php

namespace App\Patterns;

/**
 * @see https://www.mongodb.com/docs/manual/tutorial/model-tree-structures-with-materialized-paths/
 *
 * Class TreeNode
 * @package App\Patterns
 */
abstract class TreeNode
{
    public const PATH_DELIMITER = ',';

    protected ?string $path;

    /**
     * Получает массив кодов предков
     *
     * @return array
     */
    public function getAncestorsCode(): array
    {
        return $this->getPath() ? self::getAncestorsCodeByPath($this->getPath()) : [];
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public static function getAncestorsCodeByPath(string $path): array
    {
        return explode(self::PATH_DELIMITER, $path);
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string|null $path
     * @return self
     */
    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Получает код родителя
     *
     * @return string|null
     */
    public function getParentId(): ?string
    {
        if ($this->path) {
            $codes = $this->getAncestorsCode();

            return array_pop($codes);
        }

        return null;
    }

    protected function setParentNode(?TreeNode $parent): self
    {
        $this->path = '';

        if ($parent !== null) {
            if ($parent->getPath()) {
                $this->path = $parent->getPath() . self::PATH_DELIMITER;
            }

            $this->path .= $parent->getCode();
        }

        return $this;
    }

    abstract public function getCode(): string;
}
