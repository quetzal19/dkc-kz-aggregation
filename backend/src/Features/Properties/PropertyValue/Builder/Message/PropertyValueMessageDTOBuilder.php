<?php

namespace App\Features\Properties\PropertyValue\Builder\Message;

use App\Features\Properties\PropertyName\DTO\Message\PropertyNameMessageDTO;
use App\Features\Properties\PropertyValue\DTO\Message\PropertyValueMessageDTO;
use App\Helper\Enum\LocaleType;
use App\Helper\Interface\BuilderInterface;
use Doctrine\Common\Collections\{ArrayCollection, Collection};

final class PropertyValueMessageDTOBuilder implements BuilderInterface
{
    private ?string $code = null;
    private Collection $names;

    public function __construct()
    {
        $this->names = new ArrayCollection();
    }

    public function build(): PropertyValueMessageDTO
    {
        return new PropertyValueMessageDTO(
            code: $this->code,
            names: $this->names
        );
    }

    public static function create(): self
    {
        return new PropertyValueMessageDTOBuilder();
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function addName(string $name, string $locale): self
    {
        $this->names->add(new PropertyNameMessageDTO($name, $locale));
        return $this;
    }

    public function initializeLocalesByValue(string $name): self
    {
        foreach (LocaleType::getNamesLocale() as $locale) {
            $this->addName($name, $locale);
        }
        return $this;
    }
}