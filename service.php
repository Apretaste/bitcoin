<?php

class Bitcoin extends Service
{
	public function _main(Request $request)
	{
		$createResponse = false;
		if (!$this->checkValidBitcoinUser($request->email)){
			$createResponse=$this->createBitcoinUser($request->email);
		}
		else {
			$createResponse = true;
		}

		$response = new Response();
		if ($createResponse) {

			$balance = $this->getBalance($request->email);
			$publicKey = $this->getPublicKey($request->email);
			$transactions = $this->getTransactions($request->email);

			// create the response
			// create a json object to send to the template
			$responseContent = array(
				"balance" => $balance,
				"usdBalance" => "89.93",
				"email" => $request->email,
				"publicKey" => $publicKey,
				"transactions" => $transactions
			);

			$response->setResponseSubject("Resumen de su cuenta de Bitcoin");
			//$smarty->assign('transactions', $transactions);
			$response->createFromTemplate("basic.tpl", $responseContent);

		} else {

			$response->setResponseSubject("Bitcoin no disponible");
			$response->createFromText("Este servicio no se encuentra disponible en este momento. Por favor, vuelva a enviar emails de Bitcoin mas tarde.");

		}

		return $response;
	}

	public function _enviar(Request $request){
		//TODO:  enviar 
	}

	private function checkValidBitcoinUser($email) {

		// Check to see if the user already exists in the bitcoin table
		$connection = new Connection();
		$usersAccount = $connection->deepQuery("SELECT * FROM _bitcoin_accounts WHERE email like '$email' and active=1");
		
		//DISCUSS WITH SALVI - IS THIS ENOUGH VALIDATION OR SHOULD WE USE THE API TOO?
		//IT MAY MAKE IT MORE SECURE??
		return !empty($usersAccount[0]->email);
	}

	private function createBitcoinUser($email) {
		//DO THIS AFTER SALVI'S PIECE
		$publicKey = 'zeSRYrbYrtbdmH82x9CiJmhfY1JEiVhE7M';

		//TODO create bitcoin user in Apretaste
		// Create a new record in the bitcoin table
		$connection = new Connection();
		$return = $connection->deepQuery("INSERT INTO _bitcoin_accounts (email,public_key) VALUES ('$email','$publicKey')");
		//print($return);
		return $return;
	}

	private function getBalance($email) {
		//TODO AFTER SALVI'S PIECE
		return 100.01;
	}

	private function getPublicKey($email) {
		$connection = new Connection();
		$publicKey = $connection->deepQuery("SELECT public_key FROM _bitcoin_accounts WHERE email like '$email' and active=true");
		
		return $publicKey[0]->public_key;
	}

	private function getTransactions($email) {
		//TODO AFTER SALVI'S PIECE
		return array(
			array("30/01/2016", "1JEiV9CiJmhfYhE7MzeSdmH82xRYrbYrtb", "56.76", "received"),
			array("29/01/2016", "4GEiYhE7MzeSdmH82rbYrxV9CiJmhfRYtg", "824.56", "received"),
			array("28/01/2016", "hE7MzeSdmH1JEiV9CiJYrmhfY82xRYrbtb", "5.55", "sent"),
			array("28/01/2016", "hE7MzeSdmH1JEiV9CiJYrmhfY82xRYrbtb", "5.55", "sent"),
			array("27/01/2016", "9CiJmhfY1JEiVhE7MzeSRYrbYrtbdmH82x", "58.23", "received"),
			array("26/01/2016", "YhE7MzeSd1bYrtJEiV9CiJmhfmH82xRYrb", "498.36", "sent")			
		);
	}


}
