<?php

namespace Foutraz\Outlook\Facades;

use Illuminate\Support\Facades\Facade;

class Outlook extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'outlook';
    }
}
