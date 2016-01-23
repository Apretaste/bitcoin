<?php

class Bitcoin extends Service
{
	/**
	 * Function executed when the service is called
	 * 
	 * @param Request
	 * @return Response
	 * */
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
}
