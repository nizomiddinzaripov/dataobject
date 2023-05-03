<?php

namespace Programm011\DataObjects;

use Illuminate\Http\Request;

interface DataObjectContract
{
    public static function createFromRequest(Request $request);
}
