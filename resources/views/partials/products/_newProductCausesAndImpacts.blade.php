<h3>
    Select Cause(s)<br />
    <small>Don't see the cause? Only the causes that you have entered will show as options.</small>
</h3>
<div class='currentCauseList row margin-0'>
    @foreach( $causes as $cause )
    <div class='col-lg-4 col-md-4 col-sm-4 project-org-cause' data-org-cause='{{ $cause["id"] }}' data-org-cause-id='{{ $cause["orgCauseId"] }}' >
        <div class='row'>
            <div class='col-lg-3 col-md-3 col-sm-3'>
                <i class='{{ $iconMap[$cause["id"]]}} pwi-icon-2em'></i>
            </div>
            <div class='col-lg-9 col-lg-9 col-sm-9 padding-left-0' >
                <div class='cause-title padding-top-5'>{{ $cause["name"] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>