<h1>Su Cuenta de Bitcoin</h1>
<p>Este estado de cuenta fue creada {$smarty.now|date_format:"%D %T"} y le pertenece a la cuenta de {$email}</p>


{space10}

<h3>Cantidad Disponible:</h3>
<p>Cantidad en Bitcoin: {$balance} &#3647;</p>
<p>Equivalente a ${$usdBalance} USD</p>

{space10}

<h3>Detalle de Movimientos Realizados</h3>

<table border="0" cellpadding="4" cellspacing="0" width="100%">
	<tr>
		<th align="center">Fecha</th>
		<th align="center">Billetera</th>
		<th align="center">Cantidad</th>
	</tr>
	{foreach $transactions as $t}
		{if $t@index mod 2 eq 0}
		  {if $t[3] eq "sent"}
		     {* put empty table row *}
		     <tr><td bgcolor="#F2F2F2" ><font color="#FF0000">{$t[0]}</font></td><td bgcolor="#F2F2F2" ><font color="#FF0000">{$t[1]}</font></td><td bgcolor="#F2F2F2" align="right"><font color="#FF0000">-{$t[2]} &#3647;</font></td></tr>
		    {else}
		  <tr><td bgcolor="#F2F2F2"><font color="#5EBB47">{$t[0]}</font></td><td bgcolor="#F2F2F2"><font color="#5EBB47">{$t[1]}</font></td><td bgcolor="#F2F2F2" align="right"><font color="#5EBB47">{$t[2]} &#3647;</font></td></tr>
		    {/if}
		  {else}
		  {if $t[3] eq "sent"}
		     {* put empty table row *}
		     <tr> <td bgcolor="#E6E6E6" ><font color="#FF0000">{$t[0]}</font></td><td  bgcolor="#E6E6E6"><font color="#FF0000">{$t[1]}</font></td><td bgcolor="#E6E6E6" align="right"><font color="#FF0000">-{$t[2]} &#3647;</font></td></tr>
		    {else}
		  <tr><td bgcolor="#E6E6E6"><font color="#5EBB47">{$t[0]}</font></td><td bgcolor="#E6E6E6"><font color="#5EBB47">{$t[1]}</font></td><td bgcolor="#E6E6E6" align="right"><font color="#5EBB47">{$t[2]} &#3647;</font></td></tr>
		    {/if}
		{/if}
	{/foreach}
</table>

{space10}

<h3>Su codigo publico para recibir Bitcoin</h3>
<p>{$publicKey}</p>

{space10}

<center>
	<p><small>Si quieres enviar dinero,haga clic al boton abajo.</small></p>
	{button href="Bitcoin EMAIL CANTIDAD" caption="Enviar Bitcoin"}
	{space15}
</center>
