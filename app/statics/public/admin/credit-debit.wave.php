<wv:adminIndex>
    <wv:comp.admin.header/>
    <wv:comp.admin.sidebar/>
    <div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="@url(':view_acc')" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="@url(':credit')" class="current">Credit/Debit Account </a> </div>
    <h1>CREDIT / DEBIT ACCOUNT</h1>
  </div>
  <div class="container-fluid">
    <hr>
    <div class="row-fluid">
      <div class="span6">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
            <h5>Account Details</h5>
          </div>
          <div class="widget-content nopadding">
            <form action="@url(':creditDebitAccount')" method="POST" class="form-horizontal">
              <div class="control-group">
                <label class="control-label">Select Account Number:</label>
                <div class="controls">
                <select name="accId" class="form-control"required>
                    <option value="{~$request['accNumber']??''~}">Select Account Number</option>
                    @each $accounts as $acc:
                        <option value="{~$acc->accId~}">{~$acc->accNumber~}</option>
                    @endeach
                </select>
                </div>
              </div>
              {~@csrf~}
              <div class="control-group">
                <label class="control-label">Transaction Type:</label>
                <div class="controls">
                  <select name="tranType" class="form-control" required>
                    <option value="{~$request['tranType']??''~}">--Select Transaction Type--</option>
                        <option value="CREDIT">Credit</option>
                        <option value="DEBIT">Debit</option>
                    </select>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label">Amount:</label>
                <div class="controls">
                 <input class="numeric span11" type="text" name="amount" placeholder=" e.g 50000" value="{~$request['amount']??''~}" required  >
                </div>
              </div>
                <div class="control-group">
                    <label class="control-label">Description:</label>
                    <div class="controls">
                        <input type="text" name="tranDescription" value="{~$request['tranDescription']??''~}" class="span11" required>
                    </div>
                </div>
              <div class="control-group">

                <div class="controls">
        <button type="submit" class="btn btn-success">PROCEED</button>
                </div>
              </div>


            </form>
          </div>
        </div>


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