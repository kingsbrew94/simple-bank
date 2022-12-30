<wv:adminIndex>
    <wv:comp.admin.header/>
    <wv:comp.admin.sidebar/>
    
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="@url(':view_acc')" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="@url(':change_pass')" class="current"> Change Account Access </a> </div>
    <h1>CHANGE ACCOUNT ACCESS</h1>
  </div>
  <div class="container-fluid">
    <hr>
    <div class="row-fluid">
      <div class="span6">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
            <h5>Change Account Access</h5>
          </div>
          <div class="widget-content nopadding">
            <form action="@url(':changePass')" method="POST" class="form-horizontal">
              <div class="control-group">
                <label class="control-label">Old Password:</label>
                <div class="controls">
                 <input class="span11" type="password" name="password" required>
                </div>
              </div>
              {~@csrf~}
              <div class="control-group">
                <label class="control-label">New Password:</label>
                <div class="controls">
              <input type="password" name="newPassword" class="span11" required>
                </div>
              </div>
               <div class="control-group">
                <label class="control-label">Confirm New Password:</label>
                <div class="controls">
                 <input class="span11" type="password" name="confirmNewPassword" required >
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
</wv:adminIndex>