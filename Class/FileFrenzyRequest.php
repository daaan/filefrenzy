<?php

declare(strict_types=1);

class FileFrenzyRequest
{

    protected array $shorttagAttributes;

    public function __construct($atts, array $allowedShorttagAttributes)
    {
        $attributes = wp_parse_args([], $atts);
        foreach (array_keys($attributes) as $attribute) {
            if (!in_array($attribute, $allowedShorttagAttributes, true)) {
                unset($attributes[$attribute]); // Only let intended attributes through.
            }
        }
        $this->shorttagAttributes = $attributes;

    }

    public function getPath(): string
    {
        // Todo: Use names? instead of path? Like file-away?
        if (isset($this->shorttagAttributes['path'])) {
            return $this->shorttagAttributes['path'];
        }

        throw new RuntimeException('No path set.');
    }

    public function getOrder(): ?string
    {
        return $this->shorttagAttributes['order'] ?? null;
    }

    public function getFileSizeDecimals(): ?int
    {
        if (isset($this->shorttagAttributes['filesizedecimals'])) {
            return (int)$this->shorttagAttributes['filesizedecimals'];
        }

        return null;
    }

    public function getWhitelistedExtensions(): array
    {
        if (isset($this->shorttagAttributes['whitelistedextensions'])) {
            return explode(',', $this->shorttagAttributes['whitelistedextensions']);
        }

        return [];
    }

    public function getBlacklistedExtensions(): array
    {
        if (isset($this->shorttagAttributes['blacklistedextensions'])) {
            return explode(',', $this->shorttagAttributes['blacklistedextensions']);
        }

        return [];
    }

    public function getWhitelistedFilenames(): array
    {
        if (isset($this->shorttagAttributes['whitelistedFilenames'])) {
            return explode(',', $this->shorttagAttributes['whitelistedFilenames']);
        }

        return [];
    }

    public function getBlacklistedFilenames(): array
    {
        if (isset($this->shorttagAttributes['blacklistedFilenames'])) {
            return explode(',', $this->shorttagAttributes['blacklistedFilenames']);
        }

        return [];
    }

}
