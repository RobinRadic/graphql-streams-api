<?php

namespace Radic\GraphqlStreamsApiModule\Command;

use Anomaly\Streams\Platform\Addon\FieldType\FieldTypeCollection;
use Anomaly\Streams\Platform\Assignment\AssignmentCollection;
use Anomaly\Streams\Platform\Field\Contract\FieldRepositoryInterface;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;
use Anomaly\Streams\Platform\Stream\Contract\StreamRepositoryInterface;

class GetStreamFields
{
    /** @var string */
    protected $namespace;

    /** @var string */
    protected $slug;

    /** @var array */
    protected $options = [];

    /** @var StreamRepositoryInterface */
    protected $streams;

    /** @var FieldRepositoryInterface */
    protected $fields;

    /** @var FieldTypeCollection */
    protected $fieldTypes;

    /**
     * GenerateStreamSchema constructor.
     *
     * @param       $namespace
     * @param       $slug
     * @param array $options
     */
    public function __construct($namespace, $slug, array $options = [])
    {
        $this->namespace = $namespace;
        $this->slug      = $slug;
        $this->options   = $options;
    }

    public function handle(StreamRepositoryInterface $streamRepository, FieldRepositoryInterface $fieldRepository, FieldTypeCollection $fieldTypes)
    {
        $this->streams    = $streamRepository;
        $this->fields     = $fieldRepository;
        $this->fieldTypes = $fieldTypes;

        $f = $this->fields->findAllByNamespace($this->namespace);

        /** @var StreamInterface $stream */
        $stream = $this->streams->findBySlugAndNamespace($this->slug, $this->namespace);
        /** @var Field[] $fields */
        $fields = $this->getStreamFields($stream);

        return $fields;
    }

    protected function getStreamFields(StreamInterface $stream)
    {
        $fields      = [];
        $assignments = new AssignmentCollection(array_merge(
            $stream->getDateAssignments()->all(),
            $stream->getAssignments()->all()
        ));
        /** @var \Anomaly\Streams\Platform\Assignment\Contract\AssignmentInterface $assignment */
        foreach ($assignments as $assignment) {
            $field                       = $assignment->getField();
            $fields[ $field->getSlug() ] = new Field($assignment);
        }
        return collect($fields);
    }
}

class Field
{
    /** @var string */
    public $namespace;// = "users"

    /** @var string */
    public $slug;// = "username"

    /** @var string */
    public $type; // = "anomaly.field_type.slug"

    /** @var \Anomaly\ApiModule\Resource\Component\Field\Contract\FieldInterface */
    public $field;

    /** @var \Anomaly\Streams\Platform\Assignment\Contract\AssignmentInterface */
    public $assignment;

    /** @var \Anomaly\Streams\Platform\Addon\FieldType\FieldType */
    public $fieldType;

    /**+
     * Field constructor.
     *
     * @param \Anomaly\Streams\Platform\Assignment\Contract\AssignmentInterface $assignment
     */
    public function __construct($assignment)
    {
        $this->field      = $assignment->getField();
        $this->assignment = $assignment;
        $this->fieldType  = $assignment->getFieldType();
        $this->namespace  = $this->field->getNamespace();
        $this->slug       = $this->field->getSlug();
        $this->type       = $this->field->getTypeValue();
    }


}