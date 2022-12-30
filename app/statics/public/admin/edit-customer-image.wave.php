<wv:adminIndex>
    <wv:comp.admin.header/>
    <wv:comp.admin.sidebar/>
    <div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="@url(':view_acc')" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a><a href="@url(':view_acc_detail')?account={~$record->accId~}" title="Go back" class="tip-bottom"><i class="icon-arrow-left"></i> Account Details</a> <a href="url(':edit_cus_image')?account={~$record->accId~}" class="current"> Customer Photo</a> </div>
    <h1>Customer Photo</h1>
  </div>
  <div class="container-fluid">
    <hr>
    <div class="row-fluid">
      <div class="span6">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
            <h5>Customer Photo</h5>
            <h4 class="pull-right"> <span>
                <a href="@url(':edit_acc')?account={~$record->accId~}" class="btn btn-warning btn-mini"> Edit</a>
            </span></h4>
          </div>
          <div class="widget-content nopadding">
            <img src="@statics('images/avatar/'.$record->picName)" style="width:25%;"/>
            <form action="@url(':updateCustomerImage')" method="post" class="form-horizontal" enctype="multipart/form-data">
              <div class="control-group">
                <label class="control-label">Select Photo:</label>
                <div class="controls">
                  {~@csrf~}
                  <input type="file" name="customerImage" class="span11" required />
                  <input type="hidden" class="span11"   value="{~$record->cusId~}" name="cusId"/>
                  <input type="hidden" class="span11"   value="{~$record->accId~}" name="accId"/>
                </div>
              </div>


              <div class="control-group">

                <div class="controls">
                    <button type="submit" class="btn btn-success">Upload Photo</button>
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