<wv:adminIndex>
    <wv:comp.admin.header/>
    <wv:comp.admin.sidebar/>
<div id="content">
    <!--breadcrumbs-->
    <div id="content-header">
      <div id="breadcrumb"> <a href="@url(':view_acc')" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a></div>
      <h1>VIEW ACCOUNTS</h1>
    </div>
    <!--End-breadcrumbs-->

    <!--Action boxes-->
    <div class="container-fluid">

    <hr/>

    <div class="widget-box">
      <div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
        <h5>Accounts</h5>
        <span class="label label-info">Action</span> </div>
        <div class="widget-content nopadding">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Account Name</th>
                <th>Account Number</th>
                <th>Currency</th>
                <th>Account Balance</th>
                <th>Account Type</th>
                <th>Money Market</th>
                <th>Account Status</th>
                <th>Account PIN</th>
              </tr>
            </thead>
            <tbody>
              @each $accounts as $key => $data:
                <tr>
                  <td>{~$key+1~}</td>
                  <td>{~$data->firstName.' '.$data->lastName~}</td>
                  <td>{~$data->accountNumber~}</td>
                  <td>{~$data->currency~}</td>
                  <td>{~$data->accountBalance~}</td>
                  <td>{~$data->accountType~}</td>
                  <td>{~$data->moneyMarket~}</td>
                  <td>{~$data->accountStatus~}</td>
                  <td>{~$data->accountPin~}</td>
                  <td><a href="@url(':view_acc_detail')?account={~$data->accountId~}" class="btn btn-success btn-mini"> Details</a></td>
                  <td><a href="@url(':edit_acc')?account={~$data->accountId~}" class="btn btn-warning btn-mini"> Edit</a></td>
                  <td><a onclick="return confirm('Are you sure you want to delete this Account?');" href="@url(':delCustomer')?customer={~$data->customerId~}" class="btn btn-danger btn-mini"> Delete</a></td>
                </tr>
              @endeach
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>

  <!--end-main-container-part-->

  <!--Footer-part-->

  <div class="row-fluid">
    <div id="footer" class="span12">  </div>
  </div>
</wv:adminIndex>