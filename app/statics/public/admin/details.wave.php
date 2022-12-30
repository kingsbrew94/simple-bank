<wv:adminIndex>
    <wv:comp.admin.header/>
    <wv:comp.admin.sidebar/>
    <div id="content">
        <div id="content-header">
            <div id="breadcrumb"> <a href="@url(':view_acc')" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a><a href="@url(':view_acc_detail')?account={~$record->accId~}" title="Go back" class="tip-bottom"><i class="icon-arrow-left"></i> Account Details</a></div>
            <h1>Account Details</h1>
        </div>
        <div class="container-fluid">
            <hr>
            <div class="row-fluid">
            <div class="span6">
                <div class="widget-box">
                <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"> </i> </span>
                    <h5>Account-info</h5>
                <h4 class="pull-right"> <span>
                    <a href="@url(':edit_acc')?account={~$record->accId~}" class="btn btn-warning btn-mini"> Edit</a>
                    <a onclick="return confirm('Are you sure you want to delete this Account?');" href="@url(':delCustomer')?customer={~$record->cusId~}" class="btn btn-danger btn-mini"> Delete</a>
                    </span></h4>

                </div>
                <div class="widget-content nopadding">

                    <img src="@statics('images/avatar/'.$record->picName)" style="width:25%;"/>
                    <form action="#!" method="get"class="form-horizontal">
                        <div class="control-group">
                            <label class="control-label">First Name:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="firstName" value="{~$record->firstName~}"  disabled/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Last Name:</label>
                            <div class="controls">
                                <input type="text" class="span11"  name="lastName" value="{~$record->lastName~}" disabled/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Date of Birth:</label>
                            <div class="controls">
                                <input type="date" class="span11 datepicker" name="dob" value="{~$record->dob~}" disabled/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Phone:</label>
                            <div class="controls">
                                <input type="text" class="span11"  name="phoneNum" value="{~$record->phoneNum~}" disabled/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Email:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="email" value="{~$record->email~}" disabled/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Zip Code:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="zipCode" value="{~$record->zipCode~}" disabled/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Gender:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="gender" value="{~$record->gender~}" disabled/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Occupation:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="occupation" value="{~$record->occupation~}" disabled/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Address:</label>
                            <div class="controls">
                                <textarea class="span11" name="address" disabled>{~$record->address~}</textarea>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">State:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="state" value="{~$record->state~}" disabled/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">City:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="city" value="{~$record->city~}" disabled/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Country:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="country" value="{~$record->country~}" disabled/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Account Number:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="accNumber" value="{~$record->accNumber~}" disabled/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Account PIN:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="pin" value="{~$record->pin~}" disabled/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Account Status:</label>
                            
                            <div class="controls">
                                <input type="text" class="span11" name="accStatus" value="{~$record->accStatus~}" disabled/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Type of Account:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="accType" value="{~$record->accType~}" disabled/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">State of Account:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="accTypeType" value="{~$record->accTypeType~}" disabled/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Account Currency:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="accCurrency" value="{~$record->accCurrency~}" disabled/>
                            </div>
                        </div>
                    </form>
                </div>
                </div>


            </div>
            <div class="span6">
                <div class="widget-box">
                <div class="widget-title bg_ly" data-toggle="collapse" href="#collapseG2"><span class="icon"><i class="icon-chevron-down"></i></span>
                    <h5>Logs</h5>
                </div>
                <div class="widget-content nopadding collapse in" id="collapseG2">
                    <ul class="recent-posts">
                        @each $accessLogs as $log:
                            <li>
                                <div class="article-post">
                                    <span class="user-info">Date: {~dateQuery($log->dateOfActivity,'D, d M, Y - h:m A')~}</span>
                                    <p><a href="#">Activity: {~$log->activity~}</a> </p>
                                    <strong>IP: {~$log->ip~}</strong>
                                </div>
                            </li>
                        @endeach
                    </ul>
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