<?php declare(strict_types=1);

namespace Shopware\Country\Collection;

use Shopware\Country\Struct\CountryTranslationDetailStruct;
use Shopware\Shop\Collection\ShopBasicCollection;

class CountryTranslationDetailCollection extends CountryTranslationBasicCollection
{
    /**
     * @var CountryTranslationDetailStruct[]
     */
    protected $elements = [];

    public function getCountries(): CountryBasicCollection
    {
        return new CountryBasicCollection(
            $this->fmap(function (CountryTranslationDetailStruct $countryTranslation) {
                return $countryTranslation->getCountry();
            })
        );
    }

    public function getLanguages(): ShopBasicCollection
    {
        return new ShopBasicCollection(
            $this->fmap(function (CountryTranslationDetailStruct $countryTranslation) {
                return $countryTranslation->getLanguage();
            })
        );
    }

    protected function getExpectedClass(): string
    {
        return CountryTranslationDetailStruct::class;
    }
}