<?php

namespace Jensvandewiel\LaravelNotionApi\Endpoints;

use Jensvandewiel\LaravelNotionApi\Entities\Blocks\Block;
use Jensvandewiel\LaravelNotionApi\Entities\Database;
use Jensvandewiel\LaravelNotionApi\Entities\Entity;
use Jensvandewiel\LaravelNotionApi\Entities\NotionParent;
use Jensvandewiel\LaravelNotionApi\Entities\Page;
use Jensvandewiel\LaravelNotionApi\Entities\Properties\Relation;
use Jensvandewiel\LaravelNotionApi\Entities\User;
use Jensvandewiel\LaravelNotionApi\Exceptions\HandlingException;
use Jensvandewiel\LaravelNotionApi\Exceptions\NotionException;
use Jensvandewiel\LaravelNotionApi\Notion;
use Jensvandewiel\LaravelNotionApi\Traits\HasParent;
use Illuminate\Support\Collection;

/**
 * Class Resolve.
 */
class Resolve extends Endpoint
{
    /**
     * Block constructor.
     *
     * @param  Notion  $notion
     *
     * @throws HandlingException
     * @throws \Jensvandewiel\LaravelNotionApi\Exceptions\LaravelNotionAPIException
     */
    public function __construct(Notion $notion)
    {
        parent::__construct($notion);
    }

    /**
     * Resolve User.
     *
     * @param  User  $user
     * @return User
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function user(User $user): User
    {
        return $this->notion->users()->find($user->getId());
    }

    /**
     * Resolve Parent of an entity.
     *
     * @param  Entity  $entity
     * @return Page|Database|Block
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function parentOf(Entity $entity)
    {
        if (! in_array(HasParent::class, class_uses_recursive(get_class($entity)))) {
            throw new HandlingException("The given entity '{$entity->getObjectType()}' does not have a parent.");
        }

        return $this->parent($entity->getParent());
    }

    /**
     * Resolve Parent.
     *
     * @param  NotionParent  $parent
     * @return Page|Database|Block
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function parent(NotionParent $parent): Page|Database|Block|NotionParent
    {
        switch ($parent->getObjectType()) {
            case 'page_id':
                return $this->notion->pages()->find($parent->getId());
            case 'database_id':
                return $this->notion->databases()->find($parent->getId());
            case 'block_id':
                return $this->notion->block($parent->getId())->retrieve();
            case 'workspace':
                return $parent;
            default:
                throw new HandlingException('Unknown parent type while resolving the Notion parent');
        }
    }

    /**
     * Resolve Relations.
     *
     * @param  Relation  $relation
     * @return Collection<Page>
     *
     * @throws HandlingException
     * @throws NotionException
     */
    public function relations(Relation $relation, bool $onlyTitles = false): Collection
    {
        $pages = collect();
        $relationIds = $relation->getRelation()->map(function ($o) {
            return $o['id'];
        });

        foreach ($relationIds as $relationId) {
            if ($onlyTitles) {
                $pages->add($this->notion->pages()->find($relationId)->getTitle());
            } else {
                $pages->add($this->notion->pages()->find($relationId));
            }
        }

        return $pages;
    }
}
