<?php

namespace Programm011\DataObject;

use Illuminate\Http\Request;

interface DataObjectContract
{
    public static function createFromRequest(Request $request);
}
