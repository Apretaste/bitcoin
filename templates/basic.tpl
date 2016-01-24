<h1>Su Cuenta de Bitcoin</h1>
<p>Este estado de cuenta fue creada {$smarty.now|date_format:"%D %T"} y le pertenece a la cuenta de {$email}</p>


{space10}

<h3>Cantidad Disponible:</h3>
<p>Cantidad en Bitcoin: {$balance|number_format:5:".":","} &#3647;</p>
<p>Equivalente a ${$usdBalance|number_format:2:".":","} USD</p>

{space10}

{if !empty($transactions)}
<h3>Detalle de Movimientos Realizados</h3>

<p>

<table border="0" cellpadding="4" cellspacing="0" width="100%">
<tr><td align="center"><b>Fecha</b></td><td align="center"><b>Billetera</b></td><td align="center"><b>Cantidad</b></td></tr>
{foreach $transactions as $t}
	{if $t@index mod 2 eq 0}
	  {if $t->type eq "sent"}
	     {* put empty table row *}
	     <tr> <td bgcolor="#F2F2F2" ><font color="#FF0000">{$t->time|date_format:"%d/%m/%y"}</font></td><td bgcolor="#F2F2F2" ><font color="#FF0000">{$t->sender}</font></td><td bgcolor="#F2F2F2" align="right"><font color="#FF0000">-{$t->amount|number_format:5:".":","} &#3647;</font></td></tr>
	    {else}
	  <tr><td bgcolor="#F2F2F2"><font color="#5EBB47">{$t->time|date_format:"%d/%m/%y"}</font></td><td bgcolor="#F2F2F2"><font color="#5EBB47">{$t->sender}</font></td><td bgcolor="#F2F2F2" align="right"><font color="#5EBB47">{$t->amount|number_format:5:".":","} &#3647;</font></td></tr>
	    {/if}
	  {else}
	  {if $t->type eq "sent"}
	     {* put empty table row *}
	     <tr> <td bgcolor="#E6E6E6" ><font color="#FF0000">{$t->time|date_format:"%d/%m/%y"}</font></td><td  bgcolor="#E6E6E6"><font color="#FF0000">{$t->sender}</font></td><td bgcolor="#E6E6E6" align="right"><font color="#FF0000">-{$t->amount|number_format:5:".":","} &#3647;</font></td></tr>
	    {else}
	  <tr><td bgcolor="#E6E6E6"><font color="#5EBB47">{$t->time|date_format:"%d/%m/%y"}</font></td><td bgcolor="#E6E6E6"><font color="#5EBB47">{$t->sender}</font></td><td bgcolor="#E6E6E6" align="right"><font color="#5EBB47">{{$t->amount|number_format:5:".":","}} &#3647;</font></td></tr>
	    {/if}
	{/if}
{/foreach}
</table>

</p>

{space10}
{/if}

<h3>Su codigo publico para recibir Bitcoin</h3>
<p>{$publicKey}</p>

{space10}

<center>
	<p><small>Si quieres enviar dinero,haga clic al boton abajo.</small></p>
	{button href="BITCOIN ENVIAR cantidad billetera" caption="Enviar Bitcoin"}
	{space15}
</center>