@extends('emails.templates.content')
@section('content')  
	<table role="presentation" cellpadding="0" cellspacing="0" style="max-width: 720px; width: 100%; margin: 0 auto; border-top: 1px solid #EF5350; padding: 60px 40px 28px;">
		<tr>
			<td>
				<h2 style="font-size: 24px; font-weight: bold; color: #464646; margin: 0 0 25px; padding: 0; color: #000000;">A client sent a message</h2>
				<h3 style="font-size: 18px; font-weight: 600; color: #4d4d4d; margin: 0 0 20px; padding: 0; line-height: 1.5em;">Hi Admin,</h3>
				<p style="font-size: 14px; color: #666666; margin: 0; padding: 0; line-height: 1.5em;">First Name: {{ $data['first_name'] }}</p>
				<p style="font-size: 14px; color: #666666; margin: 0; padding: 0; line-height: 1.5em;">Email: {{ $data['email'] }}</p>
			</td>
		</tr>
	</table>
	<table role="presentation" cellpadding="0" cellspacing="0" style="max-width: 720px; width: 100%; margin: 0 auto; padding: 40px; border: 1px solid rgba(170,170,170,.4);">
		<tr>
			<td>
				<h3 style="color: #7a8a93; font-size: 16px; font-weight: 500; margin: 0 0 20px;">Message Content:</h3>
				<p style="color: #646464; font-size: 14px; margin: 0; padding: 0; line-height: 1.5em;">{{ $data['message'] }}</p>
			</td>
		</tr>
	</table>
@endsection