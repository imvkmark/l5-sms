<?php namespace Imvkmark\L5Sms\Support;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

class Facade extends IlluminateFacade {

	protected static function getFacadeAccessor() {
		return 'sl.sms';
	}

}