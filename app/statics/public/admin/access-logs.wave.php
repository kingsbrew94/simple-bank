<wv:adminIndex>
    <wv:comp.admin.header/>
    <wv:comp.admin.sidebar/>
    <div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="@url(':view_acc')" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="@url(':access_logs')" class="current"> View Log </a> </div>
    <h1>VIEW LOGS</h1>
  </div>
  <div class="container-fluid">
  <strong class="alert-danger"><?php echo isset($_REQUEST['m']) ? $_REQUEST['m'] : ''; ?></strong>
    <hr>
    <div class="row-fluid">
      <div class="span12">


     <table class="table table-bordered table-striped" >
  <thead>
  <tr>
    <td bgcolor="#006600"><b><font color="#FFFFFF" face="Verdana" size="2">#</font></b></td>
    <td bgcolor="#006600"><b><font color="#FFFFFF" face="Verdana" size="2">Account ID</font></b></td>
    <td bgcolor="#006600"><b><font color="#FFFFFF" face="Verdana" size="2">Name</font></b></td>

    <td bgcolor="#006600"><b><font color="#FFFFFF" face="Verdana" size="2">Activity</font></b></td>
    <td bgcolor="#006600"><b><font color="#FFFFFF" face="Verdana" size="2">Date and Time</font></b></td>
    <td bgcolor="#006600"><b><font color="#FFFFFF" face="Verdana" size="2">IP Address</font></b></td>

    <td bgcolor="#006600">&nbsp;</td>
  </tr>
  </thead>
  @each $accessLogs as $key => $data:
    <tr>
      <td bgcolor="#D9FFD9"> {~$key+1~} </td>
      <td bgcolor="#D9FFD9"> {~$data->email~} </td>
      <td bgcolor="#D9FFD9"> {~$data->firstName.' '.$data->lastName~} </td>
      <td bgcolor="#D9FFD9"> {~$data->activity~} </td>
      <td bgcolor="#D9FFD9"> {~dateQuery($data->dateOfActivity,'D, d M, Y - h:m A')~} </td>
      <td bgcolor="#D9FFD9"> {~$data->ip~} </td>
      <td bgcolor="#D9FFD9">
        <b><a class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this Log?');"  href="@url(':delAccessLog')?accessId={~$data->logId~}">
		 Delete </a></b></td>
    </tr>
  @endeach
</table>



      </div>

    </div>

  </div>

</div>
<!--Footer-part-->
<div class="row-fluid">
  <div id="footer" class="span12">  </div>
</div>
</wv:adminIndex>