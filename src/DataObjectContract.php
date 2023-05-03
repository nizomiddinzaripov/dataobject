<?php

namespace Programm011\Dataobject;

use Illuminate\Http\Request;

interface DataObjectContract
{
    public static function createFromRequest(Request $request);
}
