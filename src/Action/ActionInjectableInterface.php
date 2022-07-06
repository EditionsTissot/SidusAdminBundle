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

namespace Sidus\AdminBundle\Action;

use Sidus\AdminBundle\Admin\Action;

/**
 * If an action implements this interface, it will be injected with it's current matching action
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
interface ActionInjectableInterface
{
    public function setAction(Action $action): void;
}
