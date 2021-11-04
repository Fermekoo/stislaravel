<?php 
namespace App\Traits;

trait ResponseTrait
{
    protected function ok($message = '', $code = 200, $data = [])
	{
		return response()->json(['code' => $code, 'message' => $message, 'data' => $data], $code);
	}

	protected function bad($message = '', $code = 400, $errMsg = '')
	{
		if (env('APP_ENV') == 'production') {
			$errMsg = "Whoops! Something's Wrong 🤓🤓🤓";
		}
		return response()->json(['code' => $code, 'message' => $message, 'error_message' => $errMsg], $code);
	}

	protected function validatorMessage($validator)
	{
		$data = array();
		foreach ($validator->messages()->getMessages() as $field_name => $values) {
			$data[$field_name] = $values[0];
		}

		$res = [
			'msg'   => reset($data),
			'data'  => $data
		];
		return $res;
	}
}