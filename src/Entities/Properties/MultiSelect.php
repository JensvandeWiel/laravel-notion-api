<?php

namespace Jensvandewiel\LaravelNotionApi\Entities\Properties;

use Jensvandewiel\LaravelNotionApi\Entities\Contracts\Modifiable;
use Jensvandewiel\LaravelNotionApi\Entities\PropertyItems\SelectItem;
use Jensvandewiel\LaravelNotionApi\Exceptions\HandlingException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class MultiSelect.
 */
class MultiSelect extends Property implements Modifiable
{
    /**
     * @var Collection
     */
    private Collection $options;

    /**
     * @param  $names
     * @return MultiSelect
     */
    public static function value(array $names): MultiSelect
    {
        $multiSelectProperty = new MultiSelect();
        $multiSelectRawContent = [];
        $selectItemCollection = new Collection();

        foreach ($names as $name) {
            $selectItem = new SelectItem();
            $selectItem->setName($name);
            $selectItemCollection->add($selectItem);
            array_push($multiSelectRawContent, [
                'name' => $selectItem->getName(),
            ]);
        }

        $multiSelectProperty->content = $selectItemCollection;
        $multiSelectProperty->rawContent = $multiSelectRawContent;

        return $multiSelectProperty;
    }

    /**
     * @throws HandlingException
     */
    protected function fillFromRaw(): void
    {
        parent::fillFromRaw();

        // Handle null or empty rawContent
        if ($this->rawContent === null || !is_array($this->rawContent)) {
            return;
        }

        $itemCollection = new Collection();

        if (Arr::exists($this->rawContent, 'options')) {
            $this->options = new Collection();
            foreach ($this->rawContent['options'] as $key => $item) {
                $this->options->add(new SelectItem($item));
            }
        } else {
            foreach ($this->rawContent as $key => $item) {
                $itemCollection->add(new SelectItem($item));
            }
        }

        $this->content = $itemCollection;
    }

    /**
     * @return Collection
     */
    public function getContent(): Collection
    {
        return $this->getItems();
    }

    /**
     * @return Collection
     */
    public function getItems(): Collection
    {
        return $this->content;
    }

    /**
     * @return Collection
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getNames(): array
    {
        $names = [];
        foreach ($this->getItems() as $item) {
            array_push($names, $item->getName());
        }

        return $names;
    }

    /**
     * @return array
     */
    public function getColors(): array
    {
        $colors = [];
        foreach ($this->getItems() as $item) {
            array_push($colors, $item->getColor());
        }

        return $colors;
    }
}
