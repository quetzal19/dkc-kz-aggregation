<?php

namespace App\Features\Priority\Builder;

use App\Features\Priority\Filter\PriorityFilter;
use App\Helper\Pagination\DTO\PaginationDTO;

final class PriorityFilterBuilder
{
    private ?int $limit = null;
    private ?int $page = null;
    private ?int $actionPriority = null;
    private ?int $priority = null;
    private ?string $entity = null;
    private ?string $action = null;

    public static function create(): self
    {
        return new self();
    }

    public function build(): PriorityFilter
    {
        $pagination = null;
        if (is_null($this->page) && is_null($this->limit)) {
            $pagination = new PaginationDTO($this->page, $this->limit);
        }
        return new PriorityFilter(
            paginationDTO: $pagination,
            actionPriority: $this->actionPriority,
            priority: $this->priority,
            entity: $this->entity,
            action: $this->action
        );
    }

    public function setPagination(int $page, int $limit): self
    {
        $this->page = $page;
        $this->limit = $limit;
        return $this;
    }

    public function setActionPriority(int $actionPriority): self
    {
        $this->actionPriority = $actionPriority;
        return $this;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function setEntity(string $entity): self
    {
        $this->entity = $entity;
        return $this;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;
        return $this;
    }
}