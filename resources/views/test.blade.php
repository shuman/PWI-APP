<form name='login' action='/auth/login' method='post'>
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type='text' name='username' placeholer='username' value='michael.realmuto@projectworldimpact.com'/>
    <br />
<input type='password' name='password' placeholder='password' value='password'/>
    <br />
    
    <input type='submit' value='log in' />


</form>