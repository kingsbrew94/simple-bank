<wv:adminIndex>
    <wv:comp.admin.header/>
    <wv:comp.admin.sidebar/>
    <div id="content">
        <div id="content-header">
            <div id="breadcrumb"> <a href="@url(':view_acc')" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="@url(':view_acc_detail')?account={~$record->accId~}" class="current">Account Details</a> </div>
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
                    <a href="@url(':edit_cus_image')?account={~$record->accId~}" class="btn btn-success btn-mini"> Change Photo</a> 
                    <a onclick="return confirm('Are you sure you want to delete this Account?');" href="@url(':delCustomer')?customer={~$record->cusId~}" class="btn btn-danger btn-mini"> Delete</a>
                    </span></h4>

                </div>
                <div class="widget-content nopadding">

                    <img src="@statics('images/avatar/'.$record->picName)" style="width:25%;"/>
                    <form action="@url(':editAcc')" method="POST"class="form-horizontal">
                        <div class="control-group">
                            <label class="control-label">First Name:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="firstName" value="{~$record->firstName~}" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Last Name:</label>
                            <div class="controls">
                                <input type="text" class="span11"  name="lastName" value="{~$record->lastName~}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Date of Birth:</label>
                            <div class="controls">
                                <input type="date" class="span11 datepicker" name="dob" value="{~$record->dob~}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Phone:</label>
                            <div class="controls">
                                <input type="text" class="span11"  name="phoneNum" value="{~$record->phoneNum~}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Email:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="email" value="{~$record->email~}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Zip Code:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="zipCode" value="{~$record->zipCode~}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Gender:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="gender" value="{~['M' => 'Male', 'F' => 'Female'][$record->gender]~}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Occupation:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="occupation" value="{~$record->occupation~}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Address:</label>
                            <div class="controls">
                                <textarea class="span11" name="address">{~$record->address~}</textarea>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">State:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="state" value="{~$record->state~}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">City:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="city" value="{~$record->city~}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Country:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="country" value="{~$record->country~}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Account Number:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="accNumber" value="{~$record->accNumber~}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Account PIN:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="pin" value="{~$record->pin~}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Account Status:</label>
                            
                            <div class="controls">
                                <input type="text" class="span11" name="accStatus" value="{~$record->accStatus~}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Type of Account:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="accType" value="{~$record->accType~}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">State of Account:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="accTypeType" value="{~$record->accTypeType~}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Account Currency:</label>
                            <div class="controls">
                                <input type="text" class="span11" name="accCurrency" value="{~$record->accCurrency~}"/>
                            </div>
                        </div>
                        <input type="hidden" value="{~$record->cusId~}" name="cusId">
                        <input type="hidden" value="{~$record->accId~}" name="accId">
                        {~@csrf~}
                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" class="btn btn-success">Save Changes</button>
                            </div>
						</div>
                    </form>
                </div>
                </div>
            </div>
                <div class="span6">
                <div class="widget-box">
						<div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
							<h5>Disable Transfers</h5>
						</div>
						<div class="widget-content nopadding">
							<form action="@url(':setRestriction')" method="post" class="form-horizontal">
                                <div class="control-group">
									<label class="control-label">Transfer Status</label>
									<div class="controls">
                                        <input type="text" value="{~['-1' => 'allowed', '0' => 'denied', '1' => 'allowed'][(string)((int)(isset($record->blockTransfer)?$record->blockTransfer:'-1'))]~}" disabled/>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">Set Transfer Restriction</label>
									<div class="controls">
                                        <select class="span11" name="blockTransfer">
											<option value=""></option>
											<option value="1">Allow Transfer</option>
											<option value="0">Deny Transfer</option>
										</select>
									</div>
								</div>

								<div class="control-group">
									<label class="control-label">Display Message</label>
									<div class="controls">
										<textarea class="span11" name="transferDisplay">{~$record->transferDisplay??''~}</textarea>
									</div>
								</div>
                                {~@csrf~}
								<input type="hidden" value="{~$record->cusId~}" name="cusId">
                                <input type="hidden" value="{~$record->accId~}" name="accId">
								<div class="control-group">
									<div class="controls">
										<button type="submit" class="btn btn-success">Save Changes</button>
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