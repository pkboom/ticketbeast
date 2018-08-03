<?php

function action($name, $parameters = [], $absolute = true)
{
    if (is_array($name)) {
        $name = implode('@', $name);
    }

    return app('url')->action($name, $parameters, $absolute);
}
