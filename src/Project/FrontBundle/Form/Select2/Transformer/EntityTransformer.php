<?php
namespace Project\FrontBundle\Form\Select2\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class EntityTransformer
 * @package Project\FrontBundle\Form\Select2\Transformer
 */
class EntityTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;
    /**
     * @var string
     */
    private $class;
    /**
     * @var bool
     */
    private $multiple;

    /**
     * @param ObjectManager|RegistryInterface $om
     * @param string                          $class
     * @param bool                            $multiple
     */
    public function __construct(RegistryInterface $om, $class, $multiple = false)
    {
        $this->om       = $om;
        $this->class    = $class;
        $this->multiple = $multiple;
    }

    /**
     * Entities to ids
     * @inheritdoc
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return '';
        }

        if ($this->multiple && (!is_array($entity) && !$entity instanceof \ArrayAccess)) {
            throw new TransformationFailedException('Expected an array or \ArrayAccess.');
        }

        // Array of ids if multiple
        if ($this->multiple) {
            $response = [];
            foreach ($entity as $e) {
                $response[] = $e->getId();
            }

            return $response;
        }

        if (is_object($entity)) {
            return $entity->getId();
        }


        return $entity;
    }

    /**
     * Ids to entities
     * @inheritdoc
     */
    public function reverseTransform($id)
    {
        // Empty
        if (!$id) {
            return $this->multiple ? new ArrayCollection() : null;
        }

        // Assert array if multiple
        if ($this->multiple && !is_array($id)) {
            throw new TransformationFailedException('Expected an array.');
        }

        // Not numeric
        $this->assertNumeric($id);


        // Query all entites with the given ids
        $ids      = $this->multiple ? $id : [$id];
        $query    = $this->getQuery($ids);
        $response = $this->multiple ? $query->getResult() : $query->getOneOrNullResult();

        // We should have the same amount of results
        if ($response === null || ($this->multiple && count($id) !== count($response))) {
            throw new TransformationFailedException('Could not find all matching choices for the given value(s)');
        }

        return $response;
    }

    /**
     * Assert than id is numeric or an array of numeric values
     *
     * @param mixed $id a value or an array
     *
     * @throws TransformationFailedException if it's not a numeric value
     */
    private function assertNumeric($id)
    {
        if (!$this->multiple && false === is_numeric($id)) {
            throw new TransformationFailedException("Expected numeric");
        }
        if ($this->multiple) {
            foreach ($id as $i) {
                if (false === is_numeric($i)) {
                    throw new TransformationFailedException("Expected numeric");
                }
            };
        }
    }

    /**
     * @param int[] $ids Array of entities id
     *
     * @return Query
     */
    protected function getQuery(array $ids)
    {
        /** @var EntityRepository $repo */
        $repo = $this->om->getRepository($this->class);

        return $repo->createQueryBuilder("e")->where("e.id in (:ids)")->setParameter("ids", $ids)->getQuery();
    }
}
