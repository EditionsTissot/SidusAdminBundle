<?php

declare(strict_types=1);
/*
 * This file is part of the Sidus/AdminBundle package.
 *
 * Copyright (c) 2015-2019 Vincent Chalnot
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sidus\AdminBundle\Configuration;

use Sidus\AdminBundle\Admin\Admin;
use UnexpectedValueException;

/**
 * Keep tracks of all services tagged as admins
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class AdminRegistry
{
    /** @var Admin[] */
    protected array $admins = [];

    /** @var Admin */
    protected $currentAdmin;

    public function addAdmin(Admin $admin): void
    {
        $this->admins[$admin->getCode()] = $admin;
    }

    /**
     * @return Admin[]
     */
    public function getAdmins(): array
    {
        return $this->admins;
    }

    /**
     * @throws UnexpectedValueException
     */
    public function getAdmin(string $code): Admin
    {
        if (empty($this->admins[$code])) {
            throw new UnexpectedValueException("No admin with code: {$code}");
        }

        return $this->admins[$code];
    }

    public function hasAdmin(string $code): bool
    {
        return isset($this->admins[$code]);
    }

    public function getCurrentAdmin(): ?Admin
    {
        return $this->currentAdmin;
    }

    public function setCurrentAdmin(Admin $currentAdmin): void
    {
        $this->currentAdmin = $currentAdmin;
    }
}
