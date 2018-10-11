<?php

namespace Radic\GraphqlStreamsApiModule\GraphQL;

use Pyradic\CoreModule\Mergable\Model;
use GraphQL\Type\Definition\ResolveInfo;

class Utils
{
    public static function ena(Model $model, ResolveInfo $info, $depth = 10)
    {
        $selection = $info->getFieldSelection($depth);
        $show = static::transformSelectionToShow($selection);


        $data = $model->getGraphSelection($show);

        return $data;
    }

    public static function transformSelectionToShow($selection)
    {
        $show = static::transformFieldSelectionToShowArray($selection);
        return array_keys(array_dot($show));
    }

    private static function transformFieldSelectionToShowArray($selection){
        $new = [];
        foreach($selection as $key => $value){
            $key = snake_case($key);
            if(is_array($value)){
                $value = static::transformFieldSelectionToShowArray($value);
            }
            $new[$key] = $value;
        }
        return $new;
    }
}
