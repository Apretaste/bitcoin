<h1>Su Cuenta de Bitcoin</h1>
<p>Este estado de cuenta fue creado el {$smarty.now|date_format:"%D %T"} y le pertenece al email {$email}</p>

{space10}

<h2>Cantidad disponible</h2>
<p>Cantidad en Bitcoin: {$balance|number_format:5:".":","} &#3647;</p>
<p>Equivalente a ${$usdBalance|number_format:2:".":","} USD</p>

{space10}

<h2>Su billetera</h2>
<p>Su billetera es el c&oacute;digo que debe darle a sus deudores para que le env&iacute;en Bitcoin. Es imposible sacar dinero de ella, solo enviar.</p>
<p>{$publicKey}</p>

{button href="BITCOIN AYUDA" caption="Leer m&aacute;s"}
{button href="BITCOIN ENVIAR cantidad billetera" caption="Enviar Bitcoin"}

{space15}

{if !empty($transactions)}
	<h2>Transacciones</h2>
	<table border="0" cellpadding="4" cellspacing="0" width="100%">
		<tr>
			<th align="center">Fecha</th>
			<th align="center">Billetera</th>
			<th align="center">Cantidad</th>
		</tr>
		{foreach $transactions as $t}
			{if $t@index mod 2 eq 0}
				{if $t->type eq "sent"}
					<tr><td bgcolor="#F2F2F2" ><font color="#FF0000">{$t->time|date_format:"%d/%m/%y"}</font></td><td bgcolor="#F2F2F2" ><font color="#FF0000">{$t->sender}</font></td><td bgcolor="#F2F2F2" align="right"><font color="#FF0000">-{$t->amount|number_format:5:".":","} &#3647;</font></td></tr>
				{else}
			  		<tr><td bgcolor="#F2F2F2"><font color="#5EBB47">{$t->time|date_format:"%d/%m/%y"}</font></td><td bgcolor="#F2F2F2"><font color="#5EBB47">{$t->sender}</font></td><td bgcolor="#F2F2F2" align="right"><font color="#5EBB47">{$t->amount|number_format:5:".":","} &#3647;</font></td></tr>
				{/if}
			{else}
				{if $t->type eq "sent"}
					<tr> <td bgcolor="#E6E6E6" ><font color="#FF0000">{$t->time|date_format:"%d/%m/%y"}</font></td><td  bgcolor="#E6E6E6"><font color="#FF0000">{$t->sender}</font></td><td bgcolor="#E6E6E6" align="right"><font color="#FF0000">-{$t->amount|number_format:5:".":","} &#3647;</font></td></tr>
				{else}
			  		<tr><td bgcolor="#E6E6E6"><font color="#5EBB47">{$t->time|date_format:"%d/%m/%y"}</font></td><td bgcolor="#E6E6E6"><font color="#5EBB47">{$t->sender}</font></td><td bgcolor="#E6E6E6" align="right"><font color="#5EBB47">{{$t->amount|number_format:5:".":","}} &#3647;</font></td></tr>
				{/if}
			{/if}
		{/foreach}
	</table>
	{space10}
{/if}