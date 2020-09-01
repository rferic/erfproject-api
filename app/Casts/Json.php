<?php


namespace App\Casts;


class Json implements \Illuminate\Contracts\Database\Eloquent\CastsAttributes
{

	/**
	 * @inheritDoc
	 */
	public function get ($model, string $key, $value, array $attributes)
	{
        return json_decode($value, true);
	}

	/**
	 * @inheritDoc
	 */
	public function set ($model, string $key, $value, array $attributes)
	{
        return json_encode($value);
	}
}
