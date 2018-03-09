<?php declare(strict_types=1);

namespace Shopware\Api\Country\Event\Country;

use Shopware\Api\Country\Collection\CountryDetailCollection;
use Shopware\Api\Country\Event\CountryArea\CountryAreaBasicLoadedEvent;
use Shopware\Api\Country\Event\CountryState\CountryStateBasicLoadedEvent;
use Shopware\Api\Country\Event\CountryTranslation\CountryTranslationBasicLoadedEvent;
use Shopware\Context\Struct\ShopContext;
use Shopware\Framework\Event\NestedEvent;
use Shopware\Framework\Event\NestedEventCollection;

class CountryDetailLoadedEvent extends NestedEvent
{
    public const NAME = 'country.detail.loaded';

    /**
     * @var ShopContext
     */
    protected $context;

    /**
     * @var CountryDetailCollection
     */
    protected $countries;

    public function __construct(CountryDetailCollection $countries, ShopContext $context)
    {
        $this->context = $context;
        $this->countries = $countries;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): ShopContext
    {
        return $this->context;
    }

    public function getCountries(): CountryDetailCollection
    {
        return $this->countries;
    }

    public function getEvents(): ?NestedEventCollection
    {
        $events = [];
        if ($this->countries->getAreas()->count() > 0) {
            $events[] = new CountryAreaBasicLoadedEvent($this->countries->getAreas(), $this->context);
        }
        if ($this->countries->getStates()->count() > 0) {
            $events[] = new CountryStateBasicLoadedEvent($this->countries->getStates(), $this->context);
        }
        if ($this->countries->getTranslations()->count() > 0) {
            $events[] = new CountryTranslationBasicLoadedEvent($this->countries->getTranslations(), $this->context);
        }

        return new NestedEventCollection($events);
    }
}