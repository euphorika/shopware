<?php declare(strict_types=1);

namespace Shopware\Media\Definition;

use Shopware\Api\Entity\EntityDefinition;
use Shopware\Api\Entity\EntityExtensionInterface;
use Shopware\Api\Entity\Field\DateField;
use Shopware\Api\Entity\Field\FkField;
use Shopware\Api\Entity\Field\IntField;
use Shopware\Api\Entity\Field\LongTextField;
use Shopware\Api\Entity\Field\ManyToOneAssociationField;
use Shopware\Api\Entity\Field\OneToManyAssociationField;
use Shopware\Api\Entity\Field\StringField;
use Shopware\Api\Entity\Field\TranslatedField;
use Shopware\Api\Entity\Field\TranslationsAssociationField;
use Shopware\Api\Entity\Field\UuidField;
use Shopware\Api\Entity\FieldCollection;
use Shopware\Api\Write\Flag\PrimaryKey;
use Shopware\Api\Write\Flag\Required;
use Shopware\Category\Definition\CategoryDefinition;
use Shopware\Mail\Definition\MailAttachmentDefinition;
use Shopware\Media\Collection\MediaBasicCollection;
use Shopware\Media\Collection\MediaDetailCollection;
use Shopware\Media\Event\Media\MediaWrittenEvent;
use Shopware\Media\Repository\MediaRepository;
use Shopware\Media\Struct\MediaBasicStruct;
use Shopware\Media\Struct\MediaDetailStruct;
use Shopware\Product\Definition\ProductMediaDefinition;
use Shopware\User\Definition\UserDefinition;

class MediaDefinition extends EntityDefinition
{
    /**
     * @var FieldCollection
     */
    protected static $primaryKeys;

    /**
     * @var FieldCollection
     */
    protected static $fields;

    /**
     * @var EntityExtensionInterface[]
     */
    protected static $extensions = [];

    public static function getEntityName(): string
    {
        return 'media';
    }

    public static function getFields(): FieldCollection
    {
        if (self::$fields) {
            return self::$fields;
        }

        self::$fields = new FieldCollection([
            (new UuidField('uuid', 'uuid'))->setFlags(new PrimaryKey(), new Required()),
            (new FkField('media_album_uuid', 'albumUuid', MediaAlbumDefinition::class))->setFlags(new Required()),
            new FkField('user_uuid', 'userUuid', UserDefinition::class),
            (new StringField('file_name', 'fileName'))->setFlags(new Required()),
            (new StringField('mime_type', 'mimeType'))->setFlags(new Required()),
            (new IntField('file_size', 'fileSize'))->setFlags(new Required()),
            (new TranslatedField(new StringField('name', 'name')))->setFlags(new Required()),
            new LongTextField('meta_data', 'metaData'),
            new DateField('created_at', 'createdAt'),
            new DateField('updated_at', 'updatedAt'),
            new TranslatedField(new LongTextField('description', 'description')),
            new ManyToOneAssociationField('album', 'media_album_uuid', MediaAlbumDefinition::class, true),
            new ManyToOneAssociationField('user', 'user_uuid', UserDefinition::class, false),
            new OneToManyAssociationField('categories', CategoryDefinition::class, 'media_uuid', false, 'uuid'),
            new OneToManyAssociationField('mailAttachments', MailAttachmentDefinition::class, 'media_uuid', false, 'uuid'),
            (new TranslationsAssociationField('translations', MediaTranslationDefinition::class, 'media_uuid', false, 'uuid'))->setFlags(new Required()),
            new OneToManyAssociationField('productMedia', ProductMediaDefinition::class, 'media_uuid', false, 'uuid'),
        ]);

        foreach (self::$extensions as $extension) {
            $extension->extendFields(self::$fields);
        }

        return self::$fields;
    }

    public static function getRepositoryClass(): string
    {
        return MediaRepository::class;
    }

    public static function getBasicCollectionClass(): string
    {
        return MediaBasicCollection::class;
    }

    public static function getWrittenEventClass(): string
    {
        return MediaWrittenEvent::class;
    }

    public static function getBasicStructClass(): string
    {
        return MediaBasicStruct::class;
    }

    public static function getTranslationDefinitionClass(): ?string
    {
        return MediaTranslationDefinition::class;
    }

    public static function getDetailStructClass(): string
    {
        return MediaDetailStruct::class;
    }

    public static function getDetailCollectionClass(): string
    {
        return MediaDetailCollection::class;
    }
}