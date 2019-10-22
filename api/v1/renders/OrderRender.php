<?php

namespace api\v1\renders;

class OrderRender extends BaseRender
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
		$canceled = 0;
		if($entity->is_finished == 1 AND $entity->filled != $entity->count)
			$canceled = 1;
			
        return [
				'id' => $entity->id,
				'is_sell' => $entity->is_sell,
				'price' => $entity->price,
                'count' => $entity->count,
				'filled' => $entity->filled,
				'token_id' => $entity->token_id,
				'is_finished' => $entity->is_finished,
				'created_at' => $entity->created_at,
				'canceled' => $canceled,
        ];
    }
}