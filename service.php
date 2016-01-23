<?php

class Bitcoin extends Service
{
	public function _main(Request $request)
	{

		// create a json object to send to the template
		$responseContent = array(
			"var_one" => "hello",
			"var_two" => "world",
			"var_three" => 23
		);

		// create the response
		$response = new Response();
		$response->setResponseSubject("Llego el dinero!");
		$response->createFromTemplate("basic.tpl", $responseContent);
		return $response;
	}

	public function _enviar(Request $request){
		
	}
}
