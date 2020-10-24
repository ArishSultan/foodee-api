@if(isset($data['username']))
    Hello <strong> {{ ucfirst($data['username']) }}</strong>

    <br/><br/>
    <p>Please click this button to confirm your Foodee account:</p>

    <div style="text-align: center;">
        <a href="http://{{$ip}}{{$port}}/account/confirm/{{$data['email']}}/{{$data['uid']}}" class="btn btn-lg btn-primary" style="background: #56b656;

padding: 15px;

font-size: 21px;

color: #fff;

border-radius: 4px;">Confirm your account</a>
    </div>
    <p>
        <a href="http://{{$ip}}{{$port}}/account/confirm/{{$data['email']}}/{{$data['uid']}}">http://{{$ip}}{{$port}}/account/confirm/{{$data['email']}}/{{$data['uid']}}</a>
    </p>

    <div style="color:#cccccc; text-align: center;">
        *This is an automated message - PLease do not reply directly to this email.
    </div>
@endif


