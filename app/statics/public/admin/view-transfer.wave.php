<wv:adminIndex>
    <wv:comp.admin.header/>
    <wv:comp.admin.sidebar/>
    <div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="@url(':view_acc')" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="@url(':view_transfer')" class="current"> View Transfers </a> </div>
    <h1>VIEW TRANSFERS</h1>
  </div>
  <div class="container-fluid">
  <strong class="alert-danger"><?php echo $_REQUEST['msg'] ?? ''; ?></strong>
    <hr>
    <div class="row-fluid">
      <div class="span12">

 <table class="table table-bordered table-striped" >
  <thead>
  <tr>
    <th>#</th>
    <th>Bankname</th>
    <th>Bank Address</th>
    <th>Account Name</th>
    <th>Account Number</th>
    <th>Routing Number</th>
    <th>Amount</th>
    <th>Date</th>
    <td bgcolor="#006600">&nbsp;</td>
  </tr>
  </thead>
  @each $transfers as $key => $data:
    <tr>
        <td bgcolor="#D9FFD9">{~$key+1~}</td>
        <td bgcolor="#D9FFD9">{~$data->bankName~}</td>
        <td bgcolor="#D9FFD9">{~$data->bankAddress~}</td>
        <td bgcolor="#D9FFD9">{~$data->accountName??''~}</td>
        <td bgcolor="#D9FFD9">{~$data->accountNumber??''~}</td>
        <td bgcolor="#D9FFD9">{~$data->routingNumber??''~}</td>
        <td bgcolor="#D9FFD9">{~$data->amount~}</td>
        <td bgcolor="#D9FFD9">{~dateQuery($data->dateTranfered,'D, d M, Y - h:m A')~}</td>
        <td bgcolor="#D9FFD9"><b><a class="btn btn-danger"  onclick="return confirm('Are you sure you want to delete this Transaction?');" href="url(':delTransfer')?transferId={~$data->transfId~}">
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
<!--end-Footer-part-->
</wv:adminIndex>