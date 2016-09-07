<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="google-site-verification" content="gjwv4kU5z6-YVauYSJAD4a2qv1BArBWBKor-m4Uk3R0" />
@if( isset( $meta ) )
<title>{{ $meta['title'] }}</title>
	<meta name="title" 			content="{{ $meta['title'] }}" />
	<meta name="description" 	content="{{ $meta['description'] }}" />
	<meta property="og:url" 		content="{!! Request::url( ) !!}" />
	<meta property="og:type" 		content="website" />
	<meta property="og:title" 		content="{{ $meta['title'] }}" />
	<meta property="og:description" content="{{ $meta['description'] }}" />
	@if( isset( $logo ) )
	<meta property="og:image"		content="{{URL::to('/')}}{{ $logo }}" />	
	@endif
@endif