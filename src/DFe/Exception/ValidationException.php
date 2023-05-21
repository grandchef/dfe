<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Exception;

class ValidationException extends \Exception
{
    private $errors = [];

    public function __construct($errors = [])
    {
        $this->errors = $errors;
        reset($errors);
        parent::__construct(current($errors));
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
