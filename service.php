<?php

// Secret Mnemonic: mansion alpha inquiry enforce boring sorry rug ready come awkward camera seven

//require "vendor/autoload.php";

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

	public function _main(Request $request)
	{
//		$res = $this->createNewWallet("salvi.pascual@gmail.com");
//		$res =  $this->checkFunds("salvi.pascual@gmail.com");
//		$res = $this->listTransactions("31pspFm7ymb8EA7RsqBxAZbuXoAxzZejnj");
//		$res = $this->transfer(0.12, "salvi.pascual@gmail.com", "1KfFRdihhRPdu87enUp1nbJ5jVSfayjEoR");
//		$res = $this->USDToBTC(1);
		$res = $this->BTCToUSD(1);

		print($res);
		exit;

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

	public function _enviar(Request $request)
	{
//		isBitcoinKeyValid
		
		// get the wallet to make the transfer
		$wallet = "31pspFm7ymb8EA7RsqBxAZbuXoAxzZejnj";

		// get the amount to send in Bitcoin
		$amountUSD = $request->query;
		$amountBTC = $this->USDToBTC($amountUSD);

		$responseContent = array(
			"wallet" => $wallet,
			"amountBTC" => $amountBTC,
			"amountUSD" => $amountUSD
		);

		$response = new Response();
		$response->setResponseSubject("Transferencia realizada correctamente");
		$response->createFromTemplate("transfer.tpl", $responseContent);
		return $response;
	}

	public function _ayuda(Request $request)
	{
		$wallet = "31pspFm7ymb8EA7RsqBxAZbuXoAxzZejnj";

		$response = new Response();
		$response->setResponseSubject("Que es BitCoin?");
		$response->createFromTemplate("help.tpl", array("wallet"=>$wallet));
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

		try
		{
			$res = $block_io->withdraw_from_labels(array('amounts' => $amount, 'from_labels' => $fromEmail, 'to_addresses' => $toPublickey));
			return $res->status != "fail";
		}
		catch(Exception $e)
		{
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

		foreach ($received->data->txs as $data) {
			$res = new stdClass();
			$res->time = $data->time;
			$res->sender = $data->senders[0];
			$res->amount = $data->amounts_received[0]->amount;
			$res->type = "received";
			$transactions[] = $res;
		}

		// get all sent
		$sent = $block_io->get_transactions(array('type' => 'sent', 'addresses' => $publickey));
		foreach ($sent->data->txs as $data) {
			$res = new stdClass();
			$res->time = $data->time;
			$res->sender = $data->senders[0];
			$res->amount = $data->amounts_received[0]->amount;
			$res->type = "sent";
			$transactions[] = $res;
		}
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
		return "31pspFm7ymb8EA7RsqBxAZbuXoAxzZejnj"; // @TODO remove in production
	
		$block_io = new BlockIo($this->apiKey, $this->pin, 2);
		$address = $block_io->get_new_address(array('label' => $email));
		return $address->data->address;
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
		return $this->rate/$amount;
	}
	
	/**
	 * Check if a Bitcoin address is valid
	 * 
	 * @author internet
	 * @param String, Bitcoin public address
	 * @return Boolean
	 * */
	function isBitcoinKeyValid($address)
	{
		$origbase58 = $address;
		$dec = "0";

		for ($i = 0; $i < strlen($address); $i++)
		{
			$dec = bcadd(bcmul($dec,"58",0),strpos("123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz",substr($address,$i,1)),0);
		}

		$address = "";
		while (bccomp($dec,0) == 1)
		{
			$dv = bcdiv($dec,"16",0);
			$rem = (integer)bcmod($dec,"16");
			$dec = $dv;
			$address = $address.substr("0123456789ABCDEF",$rem,1);
		}
		$address = strrev($address);

		for ($i = 0; $i < strlen($origbase58) && substr($origbase58,$i,1) == "1"; $i++)
		{
			$address = "00".$address;
		}

		if (strlen($address)%2 != 0)
		{
			$address = "0".$address;
		}

		if (strlen($address) != 50)
		{
			return false;
		}

		if (hexdec(substr($address,0,2)) > 0)
		{
			return false;
		}

		return substr(strtoupper(hash("sha256",hash("sha256",pack("H*",substr($address,0,strlen($address)-8)),true))),0,8) == substr($address,strlen($address)-8);
	}
}
