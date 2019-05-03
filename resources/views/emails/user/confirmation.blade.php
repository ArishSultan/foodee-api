@if(isset($data['username']))
    Hello <strong> {{ ucfirst($data['username']) }}</strong>
    <br/><br/>
    <p>
        Please click this button to confirm your Foodee account:
    </p>

    <center><a href="http://34.220.151.44/account/confirm/{{$data['email']}}/{{$data['uid']}}" class="btn btn-lg btn-primary" style="background: #56b656;

padding: 15px;

font-size: 21px;

color: #fff;

border-radius: 4px;">Confirm your account</a> </center>

    <p><a href="http://34.220.151.44/account/confirm/{{$data['email']}}/{{$data['uid']}}">http://34.220.151.44/account/confirm/{{$data['email']}}/{{$data['uid']}}</a> </p>

    <center style="color:#cccccc">*This is an automated message - PLease do not reply directly to this email.</center>


@endif


