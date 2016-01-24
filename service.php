<?php

// Secret Mnemonic: mansion alpha inquiry enforce boring sorry rug ready come awkward camera seven

class Bitcoin extends Service
{
	private $apiKey = "c301-fe8f-c116-8c15";
	private $pin = "Apretaste";
	private $rate;

	/**
	 * Get the current rate when the class is loaded
	 * 
	 * @author salvipascual
	 * */
	public function __construct()
	{
		$this->rate = json_decode(file_get_contents("https://bitpay.com/api/rates/usd"))->rate;
	}

	/**
	 * Get details about your account
	 * 
	 * @param Request
	 * @return Response
	 * */
	public function _main(Request $request)
	{
		// be sure there is a valid user, else create it
		$createResponse = true;
		if ( ! $this->checkValidBitcoinUser($request->email))
		{
			$createResponse = $this->createBitcoinUser($request->email);
		}

		$response = new Response();
		if ($createResponse)
		{
			$balance = $this->checkFunds($request->email);
			$publicKey = $this->getPublicKey($request->email);
			$transactions = $this->listTransactions($publicKey);

			// create the response
			$responseContent = array(
				"balance" => $balance,
				"usdBalance" => $this->BTCToUSD($balance),
				"email" => $request->email,
				"publicKey" => $publicKey,
				"transactions" => $transactions
			);

			$response->setResponseSubject("Su cuenta de Bitcoin");
			$response->createFromTemplate("basic.tpl", $responseContent);
		}
		else
		{
			$response->setResponseSubject("Informacion de Bitcoin no disponible");
			$response->createFromText("Lo sentimos, pero hemos tenido un fallo temporal de comunicaci&oacute;n con los servidores de Bitcoin. Por favor intente nuevamente en unos minutos.");
		}

		return $response;
	}

	/**
	 * Send bitcoins
	 *
	 * @param Request
	 * @return Response
	 * */
	public function _enviar(Request $request)
	{
		// get info from the subject
		$info = explode(" ", $request->query);
		$amountUSD = $info[0];
		$amountBTC = $this->USDToBTC($amountUSD);
		$wallet = $info[1];

		// make the transfer
		$result = $this->transfer($amountBTC, $request->email, $wallet);

		// create the content to to send to the view
		$responseContent = array("wallet" => $wallet, "amountBTC" => $amountBTC, "amountUSD" => $amountUSD);

		// the response if everything was ok
		if($result)
		{
			$response = new Response();
			$response->setResponseSubject("Transferencia realizada correctamente");
			$response->createFromTemplate("transfer.tpl", $responseContent);
			return $response;
		}
		// the response in case the transfer failed
		else 
		{
			$response = new Response();
			$response->setResponseSubject("Transferencia fallida");
			$response->createFromTemplate("fail.tpl", $responseContent);
			return $response;
		}
	}

	/**
	 * Introductory words to bitcoin
	 *
	 * @param Request
	 * @return Response
	 * */
	public function _ayuda(Request $request)
	{
		$response = new Response();
		$response->setResponseSubject("Que es BitCoin?");
		$response->createFromTemplate("help.tpl", array());
		return $response;
	}

	/**
	 * List of businesses that accept bitcoin
	 *
	 * @param Request
	 * @return Response
	 * */
	public function _negocios(Request $request)
	{
		$path = $this->pathToService;
		$images = array(
			"$path/images/airbnb.jpg",
			"$path/images/carnival.jpg",
			"$path/images/virgin-atlantic.jpg",
			"$path/images/unilever.jpg",
			"$path/images/netflix.png",
			"$path/images/DimeCuba.jpg"
		);

		$response = new Response();
		$response->setResponseSubject("Negocios que aceptan BitCoin");
		$response->createFromTemplate("negocios.tpl", array("path"=>$path), $images);
		return $response;
	}

	/**
	 * Send bitcoins
	 *
	 * @author salvipascual
	 * @param String, email
	 * @return Boolean
	 * */
	private function transfer($amount, $fromEmail, $toPublickey)
	{
		$block_io = new BlockIo($this->apiKey, $this->pin, 2);

		// the API only accept numbers of 8 digit precition
		$amount = number_format($amount, 8);

		try
		{
			// place the request
			$res = $block_io->withdraw_from_labels(array('amounts' => $amount, 'from_labels' => $fromEmail, 'to_addresses' => $toPublickey, 'pin' => 'apretaste'));
			return $res->status != "fail";
		}
		catch(Exception $e)
		{
			print_r($e); exit;
			return false;
		}
	}

	/**
	 * Check the available funds based on an email
	 *
	 * @author salvipascual
	 * @param String, email
	 * @return Float
	 * */
	private function checkFunds($email)
	{
		$block_io = new BlockIo($this->apiKey, $this->pin, 2);
		$balance = $block_io->get_address_balance(array('labels' => $email));
		return $balance->data->available_balance + $balance->data->pending_received_balance;
	}

	/**
	 * List all transactions for an specific email
	 * 
	 * @author salvipascual
	 * @param String, $publickey
	 * @return Array of Objects [datetime, senderkey, amount, type]
	 * */
	private function listTransactions($publickey)
	{
		$block_io = new BlockIo($this->apiKey, $this->pin, 2);
		$transactions = array();

		// get all received 
		$received = $block_io->get_transactions(array('type' => 'received', 'addresses' => $publickey));
		foreach ($received->data->txs as $data)
		{
			$res = new stdClass();
			$res->time = $data->time;
			$res->sender = $data->senders[0];
			$res->amount = $data->amounts_received[0]->amount;
			$res->type = "received";
			$transactions[] = $res;
		}

		// get all sent
		$sent = $block_io->get_transactions(array('type' => 'sent', 'addresses' => $publickey));
		foreach ($sent->data->txs as $data)
		{
			$res = new stdClass();
			$res->time = $data->time;
			$res->sender = $data->senders[0];
			$res->amount = $data->amounts_sent[0]->amount;
			$res->type = "sent";
			$transactions[] = $res;
		}

		return $transactions;
	}

	/**
	 * Create a new wallet for an email and return its public key
	 *
	 * @author salvipascual
	 * @param String, email
	 * @return String
	 * */
	private function createNewWallet($email)
	{
		$block_io = new BlockIo($this->apiKey, $this->pin, 2);
		$address = $block_io->get_new_address(array('label' => $email));
		return $address->data->address;
	}

	/**
	 * Check to see if the user already exists in the bitcoin table
	 *
	 * @author techibis
	 * @param String, email
	 * @return Boolean
	 * */
	private function checkValidBitcoinUser($email)
	{
		$connection = new Connection();
		$usersAccount = $connection->deepQuery("SELECT * FROM _bitcoin_accounts WHERE email like '$email' and active=1");
		return !empty($usersAccount[0]->email);
	}

	/**
	 * Create a new bitcoin user in our database
	 *
	 * @author techibis
	 * @param String, email
	 * @return Boolean
	 * */
	private function createBitcoinUser($email)
	{
		// create a new wallet
		$publicKey = $this->createNewWallet($email);
		if(empty($publicKey)) return false;

		// Create a new record in the bitcoin table
		$connection = new Connection();
		return $connection->deepQuery("INSERT INTO _bitcoin_accounts (email,public_key) VALUES ('$email','$publicKey')");
	}

	/**
	 * Get the public key for a particular user
	 *
	 * @author techibis
	 * @param String, email
	 * @return String, public_key
	 * */
	private function getPublicKey($email)
	{
		$connection = new Connection();
		$publicKey = $connection->deepQuery("SELECT public_key FROM _bitcoin_accounts WHERE email like '$email' and active=true");

		return $publicKey[0]->public_key;
	}

	/**
	 * Convert from USD to BTC
	 *
	 * @author salvipascual
	 * @param Float, amount in dollars
	 * @return Float
	 * */
	private function USDToBTC($amount)
	{
		return $amount/$this->rate;
	}

	/**
	 * Convert from BTC to USD
	 *
	 * @author salvipascual
	 * @param Float, amount in Bitcoin
	 * @return Float
	 * */
	private function BTCToUSD($amount)
	{
		return $this->rate*$amount;
	}
}