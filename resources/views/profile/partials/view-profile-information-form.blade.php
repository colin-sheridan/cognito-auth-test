<section>
    <header>
        <p>
            See your account's profile information and email address:
        </p>
    </header>

    <div>
        <p>
            Your Display Name is: {!! $user->display_name !!}
        </p>
        <p>
            Your Given Name is: {!! $user->given_name !!}
        </p>
        <p>
            Your Family Name is: {!! $user->family_name !!}
        </p>
        <p>
            Your Email is: {!! $user->email !!}
        </p>
        <p>
            Your ID Number is: {!! $user->employee_id !!}
        </p>
        <p>
            Your Token is: {!! $user->refresh_token !!}
        </p>
    </div>
</section>
