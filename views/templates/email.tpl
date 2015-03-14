<body style="background-color:#000000;width:100%;font-family:'Ubuntu';">
	<table cellspacing="0" cellpadding="0" style="width:100%;background-color:#aaaaff;">
		<tr>
			<td style="background-color:#000000;"><img src="http://www.yupsie.eu/views/images/logo_mail.png" alt="" style="margin: 0 -168px -60px 0;"></td>
			<td style="background-color:#000000;color:#aaaaff;text-align:right;padding:20px;font-family:'Ubuntu';font-size:24px;padding:20px;" colspan="2">#TITLE#</td>
		</tr>
{% foreach %}
{% if %}
		<tr>
			<td style="background-color:#000000;border-bottom:1px solid #000000;color:#aaaaff;width:16%;font-weight:bold;padding:20px;text-align:right;vertical-align:top;">{{ key }}</td>
			<td style="background-color:#aaaaff;border-bottom:4px solid #aaaaff;color:#000000;width:100%;padding:20px;">{{ value }}</td>
		</tr>
{% elseif %}
		<tr>
			<td style="background-color:#aaaaff;border-bottom:1px solid #aaaaff;color:#aaaaff;width:16%;font-weight:bold;padding:20px;text-align:right;vertical-align:top;">{{ key }}</td>
			<td style="background-color:#000000;border-bottom:4px solid #000000;color:#aaaaff;width:100%;padding:20px;text-align:right;">{{ value }}</td>
		</tr>
{% else %}
		<tr>
			<td style="background-color:#000000;border-bottom:1px solid #000000;color:#aaaaff;width:16%;font-weight:bold;padding:20px;text-align:right;vertical-align:top;">{{ key }}</td>
			<td style="background-color:#ddddff;border-bottom:1px solid #aaaaff;color:#000000;width:100%;padding:20px;">{{ value }}</td>
		</tr>
{% endif %}
{% endforeach %}
	</table>
</body>