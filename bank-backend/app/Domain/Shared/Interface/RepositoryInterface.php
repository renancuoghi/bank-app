<?php

namespace App\Domain\Shared\Interface;

use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    /**
     * Create data using eloquent models.
     *
     * @param array $data
     * return Model|null
     */
    public function create(array $data): ?Model;

    /**
     * Update data using eloquent models.
     *
     * @param array $data
     * return Model|null
     */
    public function update(int $id, array $data): ?Model;

    /**
     * Get data by id using eloquent models.
     *
     * @param int $id
     * return Model|null
     */
    public function getById(int $id): ?Model;
}
