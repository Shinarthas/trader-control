<?php

namespace api\v1\renders;

class AccountRender extends BaseRender
{

	public static function all($entities)
	{
		$out = [];
		
		for ($i = 0; $i < count($entities); $i++) 
            $out[] = self::one($entities[$i]);
		
		return $out;
	}
	
    public static function one($entity)
    {
        return [
				'id' => $entity->id,
				'type' => $entity->type,
                'name' => $entity->name
        ];
    }
}